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
 * @date           13.06.20
 */

namespace FastyBird\AuthNode\Entities\Vernemq;

use FastyBird\AuthNode\Entities;
use FastyBird\NodeDatabase\Entities as NodeDatabaseEntities;
use IPub\DoctrineBlameable;
use IPub\DoctrineTimestampable;

/**
 * ACL rule entity interface
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IAccount extends NodeDatabaseEntities\IEntity,
	DoctrineTimestampable\Entities\IEntityCreated,
	DoctrineBlameable\Entities\IEntityCreator,
	DoctrineTimestampable\Entities\IEntityUpdated,
	DoctrineBlameable\Entities\IEntityEditor
{

	/**
	 * @return Entities\Accounts\IAccount
	 */
	public function getAccount(): Entities\Accounts\IAccount;

	/**
	 * @param string $mountpoint
	 *
	 * @return void
	 */
	public function setMountpoint(string $mountpoint): void;

	/**
	 * @return string
	 */
	public function getMountpoint(): string;

	/**
	 * @param string $clientId
	 *
	 * @return void
	 */
	public function setClientId(string $clientId): void;

	/**
	 * @return string
	 */
	public function getClientId(): string;

	/**
	 * @param string $username
	 *
	 * @return void
	 */
	public function setUsername(string $username): void;

	/**
	 * @return string
	 */
	public function getUsername(): string;

	/**
	 * @param string $password
	 *
	 * @return void
	 */
	public function setPassword(string $password): void;

	/**
	 * @return string
	 */
	public function getPassword(): string;

	/**
	 * @param string $pattern
	 *
	 * @return void
	 */
	public function addPublishAcl(string $pattern): void;

	/**
	 * @return mixed[]
	 */
	public function getPublishAcl(): array;

	/**
	 * @param string $pattern
	 *
	 * @return void
	 */
	public function addSubscribeAcl(string $pattern): void;

	/**
	 * @return mixed[]
	 */
	public function getSubscribeAcl(): array;

}
