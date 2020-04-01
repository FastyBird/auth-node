<?php declare(strict_types = 1);

/**
 * IIdentity.php
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

namespace FastyBird\AccountsNode\Entities\Identities;

use FastyBird\AccountsNode\Entities;
use FastyBird\AccountsNode\Types;
use IPub\DoctrineCrud;
use IPub\DoctrineTimestampable;
use Nette\Security as NS;

/**
 * Identity entity interface
 *
 * @package        FastyBird:AccountsNode!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IIdentity extends NS\IIdentity,
	DoctrineCrud\Entities\IEntity,
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
	 * @param string|null $email
	 *
	 * @return void
	 */
	public function setEmail(?string $email = null): void;

	/**
	 * @return string|null
	 */
	public function getEmail(): ?string;

	/**
	 * @param Types\IdentityStatusType $status
	 *
	 * @return void
	 */
	public function setStatus(Types\IdentityStatusType $status): void;

	/**
	 * @return Types\IdentityStatusType
	 */
	public function getStatus(): Types\IdentityStatusType;

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

}
