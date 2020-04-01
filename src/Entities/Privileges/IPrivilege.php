<?php declare(strict_types = 1);

/**
 * IPrivilege.php
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

namespace FastyBird\AccountsNode\Entities\Privileges;

use FastyBird\AccountsNode\Entities;
use IPub\DoctrineTimestampable;

/**
 * ACL privilege entity interface
 *
 * @package        FastyBird:AccountsNode!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IPrivilege extends Entities\IEntity,
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
	 * @return string
	 */
	public function getPrivilegeId(): string;

	/**
	 * @return Entities\Resources\IResource
	 */
	public function getResource(): Entities\Resources\IResource;

}
