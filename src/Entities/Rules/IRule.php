<?php declare(strict_types = 1);

/**
 * IRule.php
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

namespace FastyBird\AuthNode\Entities\Rules;

use FastyBird\AuthNode\Entities;
use FastyBird\NodeDatabase\Entities as NodeDatabaseEntities;
use IPub\DoctrineTimestampable;

/**
 * ACL rule entity interface
 *
 * @package        FastyBird:AuthNode!
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
	public function hasAccess(): bool;

	/**
	 * @return Entities\Roles\IRole
	 */
	public function getRole(): Entities\Roles\IRole;

	/**
	 * @return Entities\Privileges\IPrivilege
	 */
	public function getPrivilege(): Entities\Privileges\IPrivilege;

	/**
	 * @return Entities\Resources\IResource
	 */
	public function getResource(): Entities\Resources\IResource;

}
