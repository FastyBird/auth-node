<?php declare(strict_types = 1);

/**
 * IIdentity.php
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

namespace FastyBird\AuthNode\Entities\Identities;

use FastyBird\AuthNode\Entities;
use FastyBird\AuthNode\Types;
use FastyBird\NodeDatabase\Entities as NodeDatabaseEntities;
use IPub\DoctrineCrud;
use IPub\DoctrineTimestampable;
use Nette\Security as NS;

/**
 * Identity entity interface
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IIdentity extends NS\IIdentity,
	NodeDatabaseEntities\IEntityParams,
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
