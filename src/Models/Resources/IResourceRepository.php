<?php declare(strict_types = 1);

/**
 * IResourceRepository.php
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

namespace FastyBird\AccountsNode\Models\Resources;

use FastyBird\AccountsNode\Entities;
use FastyBird\AccountsNode\Models;
use FastyBird\AccountsNode\Queries;

/**
 * ACL resource repository interface
 *
 * @package        FastyBird:AccountsNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IResourceRepository
{

	/**
	 * @param string $keyName
	 *
	 * @return Entities\Resources\IResource|null
	 */
	public function findOneByKeyName(string $keyName): ?Entities\Resources\IResource;

	/**
	 * @param Queries\FindResourcesQuery $queryObject
	 *
	 * @return Entities\Resources\IResource|null
	 *
	 * @phpstan-template T of Entities\Resources\Resource
	 * @phpstan-param    Queries\FindResourcesQuery<T> $queryObject
	 */
	public function findOneBy(Queries\FindResourcesQuery $queryObject): ?Entities\Resources\IResource;

	/**
	 * @return Entities\Resources\IResource[]
	 */
	public function findAll(): array;

}
