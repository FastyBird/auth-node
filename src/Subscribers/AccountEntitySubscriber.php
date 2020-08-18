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
		foreach ($uow->getScheduledEntityInsertions() as $object) {
			if ($object instanceof Entities\Accounts\IUserAccount) {
				if (
					$this->singleAdministrator
					&& !$object->hasParent()
					&& $this->getAdministrator() !== null
				) {
					throw new Exceptions\ParentRequiredException('Account parent entity have to be defined');
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
				if (
					$object->getParent() !== null
					&& $this->singleAdministrator
					&& $this->getAdministrator() !== null
					&& (
						!$object->getParent()->getId()->equals($this->getAdministrator()->getId())
					)
				) {
					throw new Exceptions\ParentInvalidException('Provided parent entity is not node administrator');
				}

				foreach ($object->getRoles() as $role) {
					if (
						$role->getRoleId() === NodeAuth\Constants::ROLE_ADMINISTRATOR
						&& $object->hasParent()
					) {
						throw new Exceptions\AccountRoleInvalidException('Account with administrator role have to be without parent');
					}

					if (
						$role->getRoleId() === NodeAuth\Constants::ROLE_ADMINISTRATOR
						&& count($object->getRoles()) > 1
					) {
						throw new Exceptions\AccountRoleInvalidException('Administrator role could not be combined with other roles');
					}

					if (
						$role->getRoleId() === NodeAuth\Constants::ROLE_USER
						&& count($object->getRoles()) > 1
					) {
						throw new Exceptions\AccountRoleInvalidException('User role could not be combined with other roles');
					}

					if (
						$role->getRoleId() === NodeAuth\Constants::ROLE_ANONYMOUS
						|| $role->getRoleId() === NodeAuth\Constants::ROLE_VISITOR
					) {
						throw new Exceptions\AccountRoleInvalidException('Guest or visitor role could not be assigned to account');
					}
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
			throw new Exceptions\InvalidStateException('Administrator is not registered');
		}

		$findAccount = new Queries\FindAccountsQuery();
		$findAccount->inRole($role);

		/** @var Entities\Accounts\IUserAccount|null $account */
		$account = $this->accountRepository->findOneBy($findAccount, Entities\Accounts\UserAccount::class);

		return $account;
	}

}
