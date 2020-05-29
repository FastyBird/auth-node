<?php declare(strict_types = 1);

/**
 * IRule.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AccountsNode!
 * @subpackage     Entities
 * @since          0.1.0
 *
 * @date           30.03.20
 */

namespace FastyBird\AccountsNode\Entities\Rules;

use FastyBird\AccountsNode\Entities;
use FastyBird\NodeDatabase\Entities as NodeDatabaseEntities;
use IPub\DoctrineTimestampable;

/**
 * ACL rule entity interface
 *
 * @package        FastyBird:AccountsNode!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IRule extends NodeDatabaseEntities\IEntity,
	DoctrineTimestampable\Entities\IEntityCreated,
	DoctrineTimestampable\Entities\IEntityUpdated
{

	public const ALLOWED = true;
	public const DISALLOWED = false;

	/**
	 * @param bool $access
	 *
	 * @return void
	 */
	public function setAccess(bool $access): void;

	/**
	 * @return bool
	 */
	public function getAccess(): bool;

	/**
	 * @return bool
	 */
	public function hasAccess(): bool;

	/**
	 * @param Entities\Roles\IRole $role
	 *
	 * @return void
	 */
	public function setRole(Entities\Roles\IRole $role): void;

	/**
	 * @return Entities\Roles\IRole
	 */
	public function getRole(): Entities\Roles\IRole;

	/**
	 * @param Entities\Privileges\IPrivilege $privilege
	 *
	 * @return void
	 */
	public function setPrivilege(Entities\Privileges\IPrivilege $privilege): void;

	/**
	 * @return Entities\Privileges\IPrivilege
	 */
	public function getPrivilege(): Entities\Privileges\IPrivilege;

	/**
	 * @param Entities\Resources\IResource $resource
	 *
	 * @return void
	 */
	public function setResource(Entities\Resources\IResource $resource): void;

	/**
	 * @return Entities\Resources\IResource
	 */
	public function getResource(): Entities\Resources\IResource;

}
