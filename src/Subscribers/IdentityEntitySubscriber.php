<?php declare(strict_types = 1);

/**
 * IdentityEntitySubscriber.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Subscribers
 * @since          0.1.0
 *
 * @date           13.06.20
 */

namespace FastyBird\AuthNode\Subscribers;

use Doctrine\Common;
use Doctrine\ORM;
use FastyBird\AuthNode\Entities;
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
final class IdentityEntitySubscriber implements Common\EventSubscriber
{

	use Nette\SmartObject;

	/** @var Models\Vernemq\IAccountRepository */
	private $accountRepository;

	/**
	 * Register events
	 *
	 * @return string[]
	 */
	public function getSubscribedEvents(): array
	{
		return [
			ORM\Events::preFlush,
			ORM\Events::preUpdate,
			ORM\Events::preRemove,
		];
	}

	public function __construct(
		Models\Vernemq\IAccountRepository $accountRepository
	) {
		$this->accountRepository = $accountRepository;
	}

	/**
	 * @param ORM\Event\PreUpdateEventArgs $eventArgs
	 *
	 * @return void
	 *
	 * @throws Throwable
	 */
	public function preUpdate(ORM\Event\PreUpdateEventArgs $eventArgs): void
	{
		$em = $eventArgs->getEntityManager();
		$uow = $em->getUnitOfWork();

		// Check all scheduled updates
		foreach ($uow->getScheduledEntityUpdates() as $object) {
			if ($object instanceof Entities\Identities\IIdentity) {
				$this->processIdentityEntity($object, $em, $uow);
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
		foreach ($uow->getScheduledEntityInsertions() as $object) {
			if ($object instanceof Entities\Identities\IIdentity) {
				$this->processIdentityEntity($object, $em, $uow);
			}
		}
	}

	/**
	 * @param ORM\Event\LifecycleEventArgs $eventArgs
	 *
	 * @return void
	 *
	 * @throws Throwable
	 */
	public function preRemove(ORM\Event\LifecycleEventArgs $eventArgs): void
	{
		$em = $eventArgs->getEntityManager();
		$uow = $em->getUnitOfWork();

		foreach (array_merge($uow->getScheduledEntityDeletions(), $uow->getScheduledCollectionDeletions()) as $object) {
			if ($object instanceof Entities\Accounts\IAccount) {
				$findAccount = new Queries\FindVerneMqAccountsQuery();
				$findAccount->forAccount($object);

				$verneMqAccounts = $this->accountRepository->findAllBy($findAccount);

				foreach ($verneMqAccounts as $verneMqAccount) {
					$uow->scheduleForDelete($verneMqAccount);
				}
			}
		}
	}

	/**
	 * @param Entities\Identities\IIdentity $identity
	 * @param ORM\EntityManager $em
	 * @param ORM\UnitOfWork $uow
	 *
	 * @return void
	 *
	 * @throws Throwable
	 */
	private function processIdentityEntity(
		Entities\Identities\IIdentity $identity,
		ORM\EntityManager $em,
		ORM\UnitOfWork $uow
	): void {
		if ($identity instanceof Entities\Identities\IMachineAccountIdentity) {
			$verneMqAccount = $this->findAccount($identity);

			if ($verneMqAccount === null) {
				$publishAcls = [
					'/fb/' . $identity->getUid() . '/#',
				];

				$subscribeAcls = [
					'/fb/' . $identity->getUid() . '/#',
				];

				$this->createAccount($identity, $uow, $publishAcls, $subscribeAcls);

			} else {
				$this->updateAccount($verneMqAccount, $identity, $em, $uow);
			}
		}

		if ($identity instanceof Entities\Identities\INodeAccountIdentity) {
			$verneMqAccount = $this->findAccount($identity);

			if ($verneMqAccount === null) {
				$publishAcls = [
					'/fb/#',
				];

				$subscribeAcls = [
					'/fb/#',
					'$SYS/broker/log/#',
				];

				$this->createAccount($identity, $uow, $publishAcls, $subscribeAcls);

			} else {
				$this->updateAccount($verneMqAccount, $identity, $em, $uow);
			}
		}

		if ($identity instanceof Entities\Identities\IUserAccountIdentity) {
			$account = $identity->getAccount();

			$verneMqAccount = $this->findAccount($identity);

			if ($verneMqAccount === null) {
				$publishAcls = [];
				$subscribeAcls = [
					'/fb/#',
				];

				if (
					$account->hasRole(NodeAuth\Constants::ROLE_ADMINISTRATOR)
					|| $account->hasRole(NodeAuth\Constants::ROLE_MANAGER)
				) {
					$publishAcls[] = '/fb/#';
				}

				if ($account->hasRole(NodeAuth\Constants::ROLE_ADMINISTRATOR)) {
					$subscribeAcls[] = '$SYS/broker/log/#';
				}

				$this->createAccount($identity, $uow, $publishAcls, $subscribeAcls);
			}
		}
	}

	/**
	 * @param Entities\Identities\IIdentity $identity
	 *
	 * @return Entities\Vernemq\IAccount|null
	 */
	private function findAccount(
		Entities\Identities\IIdentity $identity
	): ?Entities\Vernemq\IAccount {
		$account = $identity->getAccount();

		$findAccount = new Queries\FindVerneMqAccountsQuery();
		$findAccount->forAccount($account);

		return $this->accountRepository->findOneBy($findAccount);
	}

	/**
	 * @param Entities\Identities\IIdentity $identity
	 * @param ORM\UnitOfWork $uow
	 * @param string[] $publishAcls
	 * @param string[] $subscribeAcls
	 *
	 * @return void
	 *
	 * @throws Throwable
	 */
	private function createAccount(
		Entities\Identities\IIdentity $identity,
		ORM\UnitOfWork $uow,
		array $publishAcls,
		array $subscribeAcls
	): void {
		if (
			!$identity instanceof Entities\Identities\IUserAccountIdentity
			&& !$identity instanceof Entities\Identities\IMachineAccountIdentity
			&& !$identity instanceof Entities\Identities\INodeAccountIdentity
		) {
			return;
		}

		if ($identity instanceof Entities\Identities\IUserAccountIdentity) {
			$password = $identity->getPassword()->getPassword();

			if ($password === null) {
				return;
			}

		} else {
			$password = $identity->getPassword();
		}

		$verneMqAccount = new Entities\Vernemq\Account(
			$identity->getUid(),
			$password,
			$identity
		);

		foreach ($publishAcls as $publishAcl) {
			$verneMqAccount->addPublishAcl($publishAcl);
		}

		foreach ($subscribeAcls as $subscribeAcl) {
			$verneMqAccount->addSubscribeAcl($subscribeAcl);
		}

		$uow->scheduleForInsert($verneMqAccount);
	}

	/**
	 * @param Entities\Vernemq\IAccount $verneMqAccount
	 * @param Entities\Identities\IIdentity $identity
	 * @param ORM\EntityManager $em
	 * @param ORM\UnitOfWork $uow
	 *
	 * @return void
	 */
	private function updateAccount(
		Entities\Vernemq\IAccount $verneMqAccount,
		Entities\Identities\IIdentity $identity,
		ORM\EntityManager $em,
		ORM\UnitOfWork $uow
	): void {
		if (
			!$identity instanceof Entities\Identities\IMachineAccountIdentity
			&& !$identity instanceof Entities\Identities\INodeAccountIdentity
		) {
			return;
		}

		$classMetadata = $em->getClassMetadata(get_class($verneMqAccount));

		$passwordProperty = $classMetadata->getReflectionProperty('password');
		$usernameProperty = $classMetadata->getReflectionProperty('username');

		$verneMqAccount->setPassword($identity->getPassword());
		$verneMqAccount->setUsername($identity->getUid());

		$uow->propertyChanged(
			$verneMqAccount,
			'password',
			$passwordProperty->getValue($verneMqAccount),
			$identity->getPassword()
		);

		$uow->propertyChanged(
			$verneMqAccount,
			'username',
			$usernameProperty->getValue($verneMqAccount),
			$identity->getUid()
		);

		$uow->scheduleExtraUpdate($verneMqAccount, [
			'password' => [
				$passwordProperty->getValue($verneMqAccount),
				$identity->getPassword(),
			],
			'username' => [
				$usernameProperty->getValue($verneMqAccount),
				$identity->getUid(),
			],
		]);
	}

}
