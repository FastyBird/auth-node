<?php declare(strict_types = 1);

/**
 * IPrivilege.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Entities
 * @since          0.1.0
 *
 * @date           30.03.20
 */

namespace FastyBird\AuthNode\Entities\Privileges;

use FastyBird\AuthNode\Entities;
use FastyBird\NodeDatabase\Entities as NodeDatabaseEntities;
use IPub\DoctrineBlameable;
use IPub\DoctrineTimestampable;

/**
 * ACL privilege entity interface
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IPrivilege extends NodeDatabaseEntities\IEntity,
	DoctrineTimestampable\Entities\IEntityCreated,
	DoctrineBlameable\Entities\IEntityCreator,
	DoctrineTimestampable\Entities\IEntityUpdated,
	DoctrineBlameable\Entities\IEntityEditor
{

	/**
	 * @return string
	 */
	public function getPrivilegeId(): string;

	/**
	 * @param string $name
	 *
	 * @return void
	 */
	public function setName(string $name): void;

	/**
	 * @return string
	 */
	public function getName(): string;

	/**
	 * @param string $comment
	 *
	 * @return void
	 */
	public function setDescription(string $comment): void;

	/**
	 * @return string
	 */
	public function getDescription(): string;

	/**
	 * @return Entities\Resources\IResource
	 */
	public function getResource(): Entities\Resources\IResource;

}
