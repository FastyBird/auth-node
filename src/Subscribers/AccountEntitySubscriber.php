<?php declare(strict_types = 1);

/**
 * AccountEntitySubscriber.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Subscribers
 * @since          0.1.0
 *
 * @date           18.08.20
 */

namespace FastyBird\AuthNode\Subscribers;

use Doctrine\Common;
use Doctrine\ORM;
use FastyBird\AuthNode;
use FastyBird\AuthNode\Entities;
use FastyBird\AuthNode\Exceptions;
use FastyBird\AuthNode\Models;
use FastyBird\AuthNode\Queries;
use FastyBird\NodeAuth;
use Nette;
use Throwable;

/**
 * Doctrine entities events
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Subscribers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class AccountEntitySubscriber implements Common\EventSubscriber
{

	use Nette\SmartObject;

	/** @var string[] */
	private $singleRoles = [
		NodeAuth\Constants::ROLE_ADMINISTRATOR,
		NodeAuth\Constants::ROLE_USER,
	];

	/** @var string[] */
	private $notAssignableRoles = [
		NodeAuth\Constants::ROLE_VISITOR,
		NodeAuth\Constants::ROLE_ANONYMOUS,
	];

	/** @var bool */
	private $singleAdministrator;

	/** @var Models\Accounts\IAccountRepository */
	private $accountRepository;

	/** @var Models\Roles\IRoleRepository */
	private $roleRepository;

	/**
	 * Register events
	 *
	 * @return string[]
	 */
	public function getSubscribedEvents(): array
	{
		return [
			ORM\Events::prePersist,
			ORM\Events::preFlush,
			ORM\Events::onFlush,
		];
	}

	public function __construct(
		bool $singleAdministrator,
		Models\Accounts\IAccountRepository $accountRepository,
		Models\Roles\IRoleRepository $roleRepository
	) {
		$this->singleAdministrator = $singleAdministrator;

		$this->accountRepository = $accountRepository;
		$this->roleRepository = $roleRepository;
	}

	/**
	 * @param ORM\Event\LifecycleEventArgs $eventArgs
	 *
	 * @return void
	 *
	 * @throws Throwable
	 */
	public function prePersist(ORM\Event\LifecycleEventArgs $eventArgs): void
	{
		$em = $eventArgs->getEntityManager();
		$uow = $em->getUnitOfWork();

		// Check all scheduled updates
		foreach ($uow->getScheduledEntityInsertions() as $object) {
			if ($object instanceof Entities\Accounts\IUserAccount) {
				/**
				 * If new account is without any role
				 * we have to assign default roles
				 */
				if (count($object->getRoles()) === 0) {
					$object->setRoles($this->getDefaultRoles(AuthNode\Constants::USER_ACCOUNT_DEFAULT_ROLES));
				}
			}

			if ($object instanceof Entities\Accounts\IMachineAccount) {
				/**
				 * Machine account has always only defined roles
				 */
				$object->setRoles($this->getDefaultRoles(AuthNode\Constants::MACHINE_ACCOUNT_DEFAULT_ROLES));
			}

			if (
				$this->getAdministrator() === null
				&& !$object->hasRole(NodeAuth\Constants::ROLE_ADMINISTRATOR)
			) {
				throw new Exceptions\InvalidStateException('First account have to be an administrator account');
			}
		}
	}

	/**
	 * @param ORM\Event\PreFlushEventArgs $eventArgs
	 *
	 * @return void
	 *
	 * @throws Throwable
	 */
	public function preFlush(ORM\Event\PreFlushEventArgs $eventArgs): void
	{
		$em = $eventArgs->getEntityManager();
		$uow = $em->getUnitOfWork();

		// Check all scheduled updates
		foreach (array_merge($uow->getScheduledEntityInsertions(), $uow->getScheduledEntityUpdates()) as $object) {
			if ($object instanceof Entities\Accounts\IUserAccount) {
				/**
				 * When node is single administrator mode
				 * every user have to have as a parent administrator account
				 *
				 * This check is skipped when node is without administrator account
				 */
				if (
					$this->singleAdministrator
					&& $this->getAdministrator() !== null
					&& !$object->hasParent()
					&& !$object->getId()->equals($this->getAdministrator()->getId())
				) {
					throw new Exceptions\RelationEntityRequired('Account parent entity have to be defined');
				}
			}
		}
	}

	/**
	 * @param ORM\Event\OnFlushEventArgs $eventArgs
	 *
	 * @return void
	 *
	 * @throws Throwable
	 */
	public function onFlush(ORM\Event\OnFlushEventArgs $eventArgs): void
	{
		$em = $eventArgs->getEntityManager();
		$uow = $em->getUnitOfWork();

		// Check all scheduled updates
		foreach (array_merge($uow->getScheduledEntityInsertions(), $uow->getScheduledEntityUpdates()) as $object) {
			if ($object instanceof Entities\Accounts\IUserAccount) {
				/**
				 * When in node is single administrator mode
				 * every user have to have as a parent administrator account
				 *
				 * This check is skipped when node is without administrator account
				 */
				if (
					$this->singleAdministrator
					&& $this->getAdministrator() !== null
					&& $object->getParent() !== null
					&& !$object->getParent()->getId()->equals($this->getAdministrator()->getId())
				) {
					throw new Exceptions\ParentInvalidException('Provided parent entity is not node administrator');
				}

				/**
				 * When node is single administrator mode
				 * every user have to have as a parent administrator account
				 *
				 * This check is skipped when node is without administrator account
				 */
				if (
					$this->singleAdministrator
					&& $this->getAdministrator() !== null
					&& !$object->hasParent()
					&& !$object->getId()->equals($this->getAdministrator()->getId())
				) {
					throw new Exceptions\RelationEntityRequired('Account parent entity have to be defined');
				}

				foreach ($object->getRoles() as $role) {
					/**
					 * If account has administrator role
					 * it have to be a account without parent
					 */
					if (
						$role->getRoleId() === NodeAuth\Constants::ROLE_ADMINISTRATOR
						&& $object->hasParent()
					) {
						throw new Exceptions\AccountRoleInvalidException('Account with administrator role have to be without parent');
					}

					/**
					 * Special roles like administrator or user
					 * can not be assigned to account with other roles
					 */
					if (
						in_array($role->getRoleId(), $this->singleRoles, true)
						&& count($object->getRoles()) > 1
					) {
						throw new Exceptions\AccountRoleInvalidException(sprintf('Role %s could not be combined with other roles', $role->getRoleId()));
					}

					/**
					 * Special roles like visitor or guest
					 * can not be assigned to account
					 */
					if (in_array($role->getRoleId(), $this->notAssignableRoles, true)) {
						throw new Exceptions\AccountRoleInvalidException(sprintf('Role %s could not be assigned to account', $role->getRoleId()));
					}
				}

				if (
					!$object->hasRole(NodeAuth\Constants::ROLE_ADMINISTRATOR)
					&& count($object->getChildren())
				) {
					throw new Exceptions\AccountRoleInvalidException('Only account with administrator role could have children');
				}
			}
		}
	}

	/**
	 * @return Entities\Accounts\IUserAccount|null
	 *
	 * @throws Throwable
	 */
	private function getAdministrator(): ?Entities\Accounts\IUserAccount
	{
		if (!$this->singleAdministrator) {
			throw new Exceptions\InvalidArgumentException('Node is in multi-administrator mode');
		}

		$findRole = new Queries\FindRolesQuery();
		$findRole->byName(NodeAuth\Constants::ROLE_ADMINISTRATOR);

		$role = $this->roleRepository->findOneBy($findRole);

		if ($role === null) {
			throw new Exceptions\InvalidStateException(sprintf('Role %s is not created', NodeAuth\Constants::ROLE_ADMINISTRATOR));
		}

		$findAccount = new Queries\FindAccountsQuery();
		$findAccount->inRole($role);

		/** @var Entities\Accounts\IUserAccount|null $account */
		$account = $this->accountRepository->findOneBy($findAccount, Entities\Accounts\UserAccount::class);

		return $account;
	}

	/**
	 * @param string[] $roleNames
	 *
	 * @return Entities\Roles\IRole[]
	 */
	private function getDefaultRoles(array $roleNames): array
	{
		$roles = [];

		foreach ($roleNames as $roleName) {
			$findRole = new Queries\FindRolesQuery();
			$findRole->byName($roleName);

			$role = $this->roleRepository->findOneBy($findRole);

			if ($role === null) {
				throw new Exceptions\InvalidStateException(sprintf('Role %s is not created', $roleName));
			}

			$roles[] = $role;
		}

		return $roles;
	}

}
