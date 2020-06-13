<?php declare(strict_types = 1);

/**
 * IResourceRepository.php
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

namespace FastyBird\AuthNode\Models\Resources;

use FastyBird\AuthNode\Entities;
use FastyBird\AuthNode\Models;
use FastyBird\AuthNode\Queries;

/**
 * ACL resource repository interface
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IResourceRepository
{

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
