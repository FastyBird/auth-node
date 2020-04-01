<?php declare(strict_types = 1);

/**
 * IPrivilegeRepository.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AccountsNode!
 * @subpackage     Models
 * @since          0.1.0
 *
 * @date           30.03.20
 */

namespace FastyBird\AccountsNode\Models\Privileges;

use FastyBird\AccountsNode\Entities;
use FastyBird\AccountsNode\Models;
use FastyBird\AccountsNode\Queries;

/**
 * ACL privileges repository interface
 *
 * @package        FastyBird:AccountsNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IPrivilegeRepository
{

	/**
	 * @param string $keyName
	 *
	 * @return Entities\Privileges\IPrivilege|null
	 */
	public function findOneByKeyName(string $keyName): ?Entities\Privileges\IPrivilege;

	/**
	 * @param Queries\FindPrivilegesQuery $queryObject
	 *
	 * @return Entities\Privileges\IPrivilege|null
	 *
	 * @phpstan-template T of Entities\Privileges\Privilege
	 * @phpstan-param    Queries\FindPrivilegesQuery<T> $queryObject
	 */
	public function findOneBy(Queries\FindPrivilegesQuery $queryObject): ?Entities\Privileges\IPrivilege;

	/**
	 * @return Entities\Privileges\IPrivilege[]
	 */
	public function findAll(): array;

}
