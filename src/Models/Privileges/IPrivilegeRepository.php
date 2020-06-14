<?php declare(strict_types = 1);

/**
 * IPrivilegeRepository.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Models
 * @since          0.1.0
 *
 * @date           30.03.20
 */

namespace FastyBird\AuthNode\Models\Privileges;

use FastyBird\AuthNode\Entities;
use FastyBird\AuthNode\Models;
use FastyBird\AuthNode\Queries;
use IPub\DoctrineOrmQuery;

/**
 * ACL privileges repository interface
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IPrivilegeRepository
{

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

	/**
	 * @param Queries\FindPrivilegesQuery $queryObject
	 *
	 * @return Entities\Privileges\IPrivilege[]
	 *
	 * @phpstan-template T of Entities\Privileges\Privilege
	 * @phpstan-param    Queries\FindPrivilegesQuery<T> $queryObject
	 */
	public function findAllBy(Queries\FindPrivilegesQuery $queryObject): array;

	/**
	 * @param Queries\FindPrivilegesQuery $queryObject
	 *
	 * @return DoctrineOrmQuery\ResultSet
	 *
	 * @phpstan-template T of Entities\Privileges\Privilege
	 * @phpstan-param    Queries\FindPrivilegesQuery<T> $queryObject
	 * @phpstan-return   DoctrineOrmQuery\ResultSet<T>
	 */
	public function getResultSet(Queries\FindPrivilegesQuery $queryObject): DoctrineOrmQuery\ResultSet;

}
