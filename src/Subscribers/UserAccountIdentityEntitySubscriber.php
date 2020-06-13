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
use Nette\Utils;

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

	/** @var Models\Vernemq\IAccountsManager */
	private $accountsManager;

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
	 * @param Models\Vernemq\IAccountsManager $accountsManager
	 */
	public function __construct(
		Models\Vernemq\IAccountRepository $accountRepository,
		Models\Vernemq\IAccountsManager $accountsManager
	) {
		$this->accountRepository = $accountRepository;
		$this->accountsManager = $accountsManager;
	}

	/**
	 * @param ORM\Event\OnFlushEventArgs $eventArgs
	 *
	 * @return void
	 */
	public function onFlush(ORM\Event\OnFlushEventArgs $eventArgs): void
	{
		$em = $eventArgs->getEntityManager();
		$uow = $em->getUnitOfWork();

		// Check all scheduled updates
		foreach (array_merge($uow->getScheduledEntityInsertions(), $uow->getScheduledEntityUpdates()) as $object) {
			if (
				$object instanceof Entities\Identities\IUserAccountIdentity
				&& $object->getPassword() !== null
			) {
				$findAccount = new Queries\FindVerneMqAccountsQuery();
				$findAccount->forAccount($object->getAccount());

				$verneMqAccount = $this->accountRepository->findOneBy($findAccount);

				if ($verneMqAccount === null) {
					$create = Utils\ArrayHash::from([
						'account'  => $object->getAccount(),
						'username' => $object->getUid(),
						'password' => $object->getPassword(),
					]);

					$this->accountsManager->create($create);

				} else {
					$update = Utils\ArrayHash::from([
						'password' => $object->getPassword(),
					]);

					$this->accountsManager->update($verneMqAccount, $update);
				}
			}
		}
	}

}
