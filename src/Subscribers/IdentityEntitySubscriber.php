<?php declare(strict_types = 1);

/**
 * IdentityEntitySubscriber.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
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

		foreach ($uow->getScheduledEntityDeletions() as $object) {
			if ($object instanceof Entities\Identities\IUserAccountIdentity) {
				$findAccount = new Queries\FindVerneMqAccountsQuery();
				$findAccount->forAccount($object->getAccount());

				$verneMqAccount = $this->accountRepository->findOneBy($findAccount);

				if ($verneMqAccount !== null) {
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
		if (
			$identity instanceof Entities\Identities\INodeAccountIdentity
			|| $identity instanceof Entities\Identities\IMachineAccountIdentity
		) {
			$findAccount = new Queries\FindVerneMqAccountsQuery();
			$findAccount->forAccount($identity->getAccount());

			$verneMqAccount = $this->accountRepository->findOneBy($findAccount);

			if ($verneMqAccount === null) {
				$verneMqAccount = new Entities\Vernemq\Account(
					$identity->getUid(),
					$identity->getPassword(),
					$identity->getAccount()
				);

				$verneMqAccount->addPublishAcl('/fb/' . $identity->getUid() . '/#');
				$verneMqAccount->addSubscribeAcl('/fb/' . $identity->getUid() . '/#');

				if ($identity instanceof Entities\Identities\INodeAccountIdentity) {
					$verneMqAccount->addSubscribeAcl('$SYS/broker/log/#');
				}

				$uow->scheduleForInsert($verneMqAccount);

			} else {
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
	}

}
