<?php declare(strict_types = 1);

/**
 * AccountEntitySubscriber.php
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
final class UserAccountIdentityEntitySubscriber implements Common\EventSubscriber
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
			ORM\Events::onFlush,
		];
	}

	/**
	 * @param Models\Vernemq\IAccountRepository $accountRepository
	 */
	public function __construct(
		Models\Vernemq\IAccountRepository $accountRepository
	) {
		$this->accountRepository = $accountRepository;
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
			if (
				$object instanceof Entities\Identities\IUserAccountIdentity
				&& $object->getPassword()->getPassword() !== null
			) {
				$findAccount = new Queries\FindVerneMqAccountsQuery();
				$findAccount->forAccount($object->getAccount());

				$verneMqAccount = $this->accountRepository->findOneBy($findAccount);

				if ($verneMqAccount === null) {
					$verneMqAccount = new Entities\Vernemq\Account(
						$object->getUid(),
						$object->getPassword()->getPassword(),
						$object->getAccount()
					);

					$verneMqAccount->addPublishAcl('/fb/+/#');
					$verneMqAccount->addSubscribeAcl('/fb/+/#');
					$verneMqAccount->addSubscribeAcl('$SYS/broker/log/#');

					$uow->scheduleForInsert($verneMqAccount);

				} else {
					$classMetadata = $em->getClassMetadata(get_class($verneMqAccount));

					$property = $classMetadata->getReflectionProperty('password');

					$oldValue = $property->getValue($verneMqAccount);

					$uow->propertyChanged($verneMqAccount, 'password', $oldValue, true);

					$uow->scheduleExtraUpdate($verneMqAccount, [
						'password' => [$oldValue, false],
					]);
				}
			}
		}

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

}
