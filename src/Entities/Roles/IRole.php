<?php declare(strict_types = 1);

/**
 * IRole.php
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

namespace FastyBird\AccountsNode\Entities\Roles;

use FastyBird\AccountsNode\Entities;
use FastyBird\NodeDatabase\Entities as NodeDatabaseEntities;
use IPub\DoctrineTimestampable;
use Nette\Security as NS;

/**
 * ACL role entity interface
 *
 * @package        FastyBird:AccountsNode!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IRole extends NS\IRole,
	NodeDatabaseEntities\IEntity,
	DoctrineTimestampable\Entities\IEntityCreated,
	DoctrineTimestampable\Entities\IEntityUpdated
{

	// The identifier of the anonymous role
	public const ROLE_ANONYMOUS = 'guest';
	// The identifier of the authenticated role
	public const ROLE_AUTHENTICATED = 'authenticated';
	// The identifier of the administrator role
	public const ROLE_ADMINISTRATOR = 'administrator';

	/**
	 * @param string $name
	 *
	 * @return void
	 */
	public function setName(string $name): void;

	/**
	 * @return string|null
	 */
	public function getName(): ?string;

	/**
	 * @param string|null $comment
	 *
	 * @return void
	 */
	public function setComment(?string $comment): void;

	/**
	 * @return string|null
	 */
	public function getComment(): ?string;

	/**
	 * @param IRole|null $parent
	 *
	 * @return void
	 */
	public function setParent(?IRole $parent = null): void;

	/**
	 * @return IRole|null
	 */
	public function getParent(): ?IRole;

	/**
	 * @param IRole[] $children
	 *
	 * @return void
	 */
	public function setChildren(array $children): void;

	/**
	 * @param IRole $child
	 *
	 * @return void
	 */
	public function addChild(IRole $child): void;

	/**
	 * @return IRole[]
	 */
	public function getChildren(): array;

	/**
	 * @param int $priority
	 *
	 * @return void
	 */
	public function setPriority(int $priority): void;

	/**
	 * @return int
	 */
	public function getPriority(): int;

	/**
	 * @return Entities\Rules\IRule[]
	 */
	public function getRules(): array;

	/**
	 * @param Entities\Resources\IResource $resource
	 * @param Entities\Privileges\IPrivilege $privilege
	 *
	 * @return bool
	 */
	public function hasAccess(
		Entities\Resources\IResource $resource,
		Entities\Privileges\IPrivilege $privilege
	): bool;

	/**
	 * Check if role is one from system roles
	 *
	 * @return bool
	 */
	public function isLocked(): bool;

	/**
	 * Check if role is guest
	 *
	 * @return bool
	 */
	public function isAnonymous(): bool;

	/**
	 * Check if role is authenticated
	 *
	 * @return bool
	 */
	public function isAuthenticated(): bool;

	/**
	 * Check if role is administrator
	 *
	 * @return bool
	 */
	public function isAdministrator(): bool;

}
