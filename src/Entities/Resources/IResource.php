<?php declare(strict_types = 1);

/**
 * IResource.php
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

namespace FastyBird\AuthNode\Entities\Resources;

use FastyBird\AuthNode\Entities;
use FastyBird\NodeDatabase\Entities as NodeDatabaseEntities;
use IPub\DoctrineTimestampable;
use Nette\Security as NS;

/**
 * ACL resource entity interface
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IResource extends NS\IResource,
	NodeDatabaseEntities\IEntity,
	DoctrineTimestampable\Entities\IEntityCreated,
	DoctrineTimestampable\Entities\IEntityUpdated
{

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
	public function setComment(string $comment): void;

	/**
	 * @return string|null
	 */
	public function getComment(): ?string;

	/**
	 * @param IResource|null $parent
	 *
	 * @return void
	 */
	public function setParent(?IResource $parent = null): void;

	/**
	 * @return IResource|null
	 */
	public function getParent(): ?IResource;

	/**
	 * @param IResource[] $children
	 *
	 * @return void
	 */
	public function setChildren(array $children): void;

	/**
	 * @param IResource $child
	 *
	 * @return void
	 */
	public function addChild(IResource $child): void;

	/**
	 * @return IResource[]
	 */
	public function getChildren(): array;

	/**
	 * @param Entities\Privileges\IPrivilege[] $privileges
	 *
	 * @return void
	 */
	public function setPrivileges(array $privileges): void;

	/**
	 * @param Entities\Privileges\IPrivilege $privilege
	 *
	 * @return void
	 */
	public function addPrivilege(Entities\Privileges\IPrivilege $privilege): void;

	/**
	 * @return Entities\Privileges\IPrivilege[]
	 */
	public function getPrivileges(): array;

}
