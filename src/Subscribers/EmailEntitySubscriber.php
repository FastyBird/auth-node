<?php declare(strict_types = 1);

/**
 * EmailEntitySubscriber.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AccountsNode!
 * @subpackage     Subscribers
 * @since          0.1.0
 *
 * @date           30.03.20
 */

namespace FastyBird\AccountsNode\Subscribers;

use Doctrine\Common;
use Doctrine\ORM;
use FastyBird\AccountsNode\Entities;
use FastyBird\AccountsNode\Types;
use Nette;

/**
 * Doctrine entities events
 *
 * @package        FastyBird:AccountsNode!
 * @subpackage     Subscribers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class EmailEntitySubscriber implements Common\EventSubscriber
{

	use Nette\SmartObject;

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
	 * @param ORM\Event\OnFlushEventArgs $eventArgs
	 *
	 * @return void
	 *
	 * @throws ORM\ORMException
	 */
	public function onFlush(ORM\Event\OnFlushEventArgs $eventArgs): void
	{
		$em = $eventArgs->getEntityManager();
		$uow = $em->getUnitOfWork();

		// Check all scheduled updates
		foreach (array_merge($uow->getScheduledEntityInsertions(), $uow->getScheduledEntityUpdates()) as $object) {
			if ($object instanceof Entities\Emails\IEmail && $object->isDefault()) {
				$changeSet = $uow->getEntityChangeSet($object);
				$classMetadata = $em->getClassMetadata(get_class($object));

				// Check if entity was set as default
				if (array_key_exists('default', $changeSet)) {
					$this->setAsDefault($uow, $classMetadata, $object);
					$this->updateSystemIdentityEmail($em, $uow, $object);
				}
			}
		}
	}

	/**
	 * @param ORM\UnitOfWork $uow
	 * @param ORM\Mapping\ClassMetadata $classMetadata
	 * @param Entities\Emails\IEmail $email
	 *
	 * @return void
	 */
	private function setAsDefault(
		ORM\UnitOfWork $uow,
		ORM\Mapping\ClassMetadata $classMetadata,
		Entities\Emails\IEmail $email
	): void {
		$property = $classMetadata->getReflectionProperty('default');

		foreach ($email->getAccount()->getEmails() as $accountEmail) {
			// Deactivate all other user emails
			if (
				$accountEmail->getId() !== $email->getId()
				&& $accountEmail->isDefault()
			) {
				$accountEmail->setDefault(false);

				$oldValue = $property->getValue($email);

				$uow->propertyChanged($accountEmail, 'default', $oldValue, true);
				$uow->scheduleExtraUpdate($accountEmail, [
					'default' => [$oldValue, false],
				]);
			}
		}
	}

	/**
	 * @param ORM\EntityManager $em
	 * @param ORM\UnitOfWork $uow
	 * @param Entities\Emails\IEmail $email
	 *
	 * @return void
	 *
	 * @throws ORM\ORMException
	 */
	private function updateSystemIdentityEmail(
		ORM\EntityManager $em,
		ORM\UnitOfWork $uow,
		Entities\Emails\IEmail $email
	): void {
		foreach ($email->getAccount()->getIdentities() as $identity) {
			if (
				$identity instanceof Entities\Identities\System
				&& $identity->getEmail() !== $email->getAddress()
				&& $identity->getStatus()->equalsValue(Types\IdentityStatusType::STATE_ACTIVE)
			) {
				$identity->setEmail($email->getAddress());

				$em->persist($identity);
				$classMetadata = $em->getClassMetadata(Entities\Identities\System::class);
				$uow->computeChangeSet($classMetadata, $identity);
			}
		}
	}

}
