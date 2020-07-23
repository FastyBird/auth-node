<?php declare(strict_types = 1);

/**
 * IAccount.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Entities
 * @since          0.1.0
 *
 * @date           30.03.20
 */

namespace FastyBird\AuthNode\Entities\Accounts;

use DateTimeInterface;
use FastyBird\AuthNode\Entities;
use FastyBird\AuthNode\Types;
use FastyBird\NodeDatabase\Entities as NodeDatabaseEntities;
use IPub\DoctrineBlameable;
use IPub\DoctrineTimestampable;

/**
 * Application account entity interface
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IAccount extends NodeDatabaseEntities\IEntity,
	NodeDatabaseEntities\IEntityParams,
	DoctrineTimestampable\Entities\IEntityCreated,
	DoctrineBlameable\Entities\IEntityCreator,
	DoctrineTimestampable\Entities\IEntityUpdated,
	DoctrineBlameable\Entities\IEntityEditor
{

	/**
	 * @param Types\AccountStatusType $status
	 *
	 * @return void
	 */
	public function setStatus(Types\AccountStatusType $status): void;

	/**
	 * @return Types\AccountStatusType
	 */
	public function getStatus(): Types\AccountStatusType;

	/**
	 * @return bool
	 */
	public function isActivated(): bool;

	/**
	 * @return bool
	 */
	public function isBlocked(): bool;

	/**
	 * @return bool
	 */
	public function isDeleted(): bool;

	/**
	 * @return bool
	 */
	public function isNotActivated(): bool;

	/**
	 * @return bool
	 */
	public function isApprovalRequired(): bool;

	/**
	 * @param DateTimeInterface $lastVisit
	 *
	 * @return void
	 */
	public function setLastVisit(DateTimeInterface $lastVisit): void;

	/**
	 * @return DateTimeInterface|null
	 */
	public function getLastVisit(): ?DateTimeInterface;

	/**
	 * @return Entities\Identities\IIdentity[]
	 */
	public function getIdentities(): array;

	/**
	 * @param Entities\Roles\IRole[] $roles
	 *
	 * @return void
	 */
	public function setRoles(array $roles): void;

	/**
	 * @param Entities\Roles\IRole $role
	 *
	 * @return void
	 */
	public function addRole(Entities\Roles\IRole $role): void;

	/**
	 * @return Entities\Roles\IRole[]
	 */
	public function getRoles(): array;

	/**
	 * @param Entities\Roles\IRole $role
	 *
	 * @return void
	 */
	public function removeRole(Entities\Roles\IRole $role): void;

	/**
	 * @param Entities\Roles\IRole $role
	 *
	 * @return bool
	 */
	public function hasRole(Entities\Roles\IRole $role): bool;

}
