<?php declare(strict_types = 1);

/**
 * IIdentity.php
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

namespace FastyBird\AuthNode\Entities\Identities;

use FastyBird\AuthNode\Entities;
use FastyBird\AuthNode\Types;
use FastyBird\NodeAuth\Security as NodeAuthSecurity;
use FastyBird\NodeDatabase\Entities as NodeDatabaseEntities;
use IPub\DoctrineTimestampable;

/**
 * Identity entity interface
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IIdentity extends NodeDatabaseEntities\IEntity,
	NodeAuthSecurity\IIdentity,
	NodeDatabaseEntities\IEntityParams,
	DoctrineTimestampable\Entities\IEntityCreated,
	DoctrineTimestampable\Entities\IEntityUpdated
{

	/**
	 * @return Entities\Accounts\IAccount
	 */
	public function getAccount(): Entities\Accounts\IAccount;

	/**
	 * @return string
	 */
	public function getUid(): string;

	/**
	 * @param Types\IdentityStateType $state
	 *
	 * @return void
	 */
	public function setState(Types\IdentityStateType $state): void;

	/**
	 * @return Types\IdentityStateType
	 */
	public function getState(): Types\IdentityStateType;

	/**
	 * @return bool
	 */
	public function isActive(): bool;

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
	public function isInvalid(): bool;

	/**
	 * @return void
	 */
	public function invalidate(): void;

	/**
	 * @return mixed[]
	 */
	public function toArray(): array;

}
