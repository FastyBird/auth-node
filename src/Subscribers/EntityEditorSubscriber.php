<?php declare(strict_types = 1);

/**
 * EntityEditorSubscriber.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Subscribers
 * @since          0.1.0
 *
 * @date           23.06.20
 */

namespace FastyBird\AuthNode\Subscribers;

use Doctrine\Common;
use Doctrine\ORM;
use FastyBird\AuthNode\Entities;
use FastyBird\AuthNode\Exceptions;
use IPub\DoctrineBlameable;
use Nette;

/**
 * Entity editor listener
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Subscribers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class EntityEditorSubscriber implements Common\EventSubscriber
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
			ORM\Events::loadClassMetadata,
		];
	}

	/**
	 * @param ORM\Event\LoadClassMetadataEventArgs $eventArgs
	 *
	 * @return void
	 *
	 * @throws ORM\Mapping\MappingException
	 */
	public function loadClassMetadata(ORM\Event\LoadClassMetadataEventArgs $eventArgs): void
	{
		$metadata = $eventArgs->getClassMetadata();

		if (!in_array(DoctrineBlameable\Entities\IEntityEditor::class, class_implements($metadata->getName()), true)) {
			return;
		}

		$association = [
			'targetEntity' => Entities\Accounts\Account::class,
			'fieldName'    => 'updatedBy',
			'joinColumns'  => [
				[
					'name'                 => 'updated_by',
					'referencedColumnName' => 'account_id',
					'onDelete'             => 'SET NULL',
				],
			],
		];

		if ($metadata->hasAssociation('updatedBy') && !$metadata->hasField('updatedBy')) {
			$metadata->setAssociationOverride('updatedBy', $association);

		} elseif (!$metadata->hasAssociation('updatedBy') && !$metadata->hasField('updatedBy')) {
			$metadata->mapManyToOne($association);

		} else {
			throw new Exceptions\InvalidStateException('Field "updatedBy" is already associated and cannot be overridden.');
		}
	}

}
