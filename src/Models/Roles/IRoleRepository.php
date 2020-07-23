<?php declare(strict_types = 1);

/**
 * IRoleRepository.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Models
 * @since          0.1.0
 *
 * @date           30.03.20
 */

namespace FastyBird\AuthNode\Models\Roles;

use FastyBird\AuthNode\Entities;
use FastyBird\AuthNode\Models;
use FastyBird\AuthNode\Queries;
use IPub\DoctrineOrmQuery;

/**
 * ACL role repository interface
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IRoleRepository
{

	/**
	 * @param string $keyName
	 *
	 * @return Entities\Roles\IRole|null
	 */
	public function findOneByName(string $keyName): ?Entities\Roles\IRole;

	/**
	 * @param Queries\FindRolesQuery $queryObject
	 *
	 * @return Entities\Roles\IRole|null
	 *
	 * @phpstan-template T of Entities\Roles\Role
	 * @phpstan-param    Queries\FindRolesQuery<T> $queryObject
	 */
	public function findOneBy(Queries\FindRolesQuery $queryObject): ?Entities\Roles\IRole;

	/**
	 * @return Entities\Roles\IRole[]
	 */
	public function findAll(): array;

	/**
	 * @param Queries\FindRolesQuery $queryObject
	 *
	 * @return Entities\Roles\IRole[]
	 *
	 * @phpstan-template T of Entities\Roles\Role
	 * @phpstan-param    Queries\FindRolesQuery<T> $queryObject
	 */
	public function findAllBy(Queries\FindRolesQuery $queryObject): array;

	/**
	 * @param Queries\FindRolesQuery $queryObject
	 *
	 * @return DoctrineOrmQuery\ResultSet
	 *
	 * @phpstan-template T of Entities\Roles\Role
	 * @phpstan-param    Queries\FindRolesQuery<T> $queryObject
	 * @phpstan-return   DoctrineOrmQuery\ResultSet<T>
	 */
	public function getResultSet(Queries\FindRolesQuery $queryObject): DoctrineOrmQuery\ResultSet;

}
