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

use Doctrine\Common;
use Doctrine\ORM;
use FastyBird\AccountsNode\Entities;
use FastyBird\AccountsNode\Queries;
use Nette;
use Throwable;

/**
 * ACL resource repository
 *
 * @package        FastyBird:AccountsNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class ResourceRepository implements IResourceRepository
{

	use Nette\SmartObject;

	/** @var Common\Persistence\ManagerRegistry */
	private $managerRegistry;

	/** @var ORM\EntityRepository<Entities\Resources\Resource>|null */
	private $repository;

	public function __construct(
		Common\Persistence\ManagerRegistry $managerRegistry
	) {
		$this->managerRegistry = $managerRegistry;
	}

	/**
	 * {@inheritDoc}
	 */
	public function findOneByKeyName(string $keyName): ?Entities\Resources\IResource
	{
		$findQuery = new Queries\FindResourcesQuery();
		$findQuery->byKeyName($keyName);

		return $this->findOneBy($findQuery);
	}

	/**
	 * {@inheritDoc}
	 */
	public function findOneBy(Queries\FindResourcesQuery $queryObject): ?Entities\Resources\IResource
	{
		/** @var Entities\Resources\IResource|null $privilege */
		$privilege = $queryObject->fetchOne($this->getRepository());

		return $privilege;
	}

	/**
	 * @return Entities\Resources\IResource[]
	 *
	 * @throws Throwable
	 */
	public function findAll(): array
	{
		$queryObject = new Queries\FindPrivilegesQuery();

		$result = $queryObject->fetch($this->getRepository());

		return is_array($result) ? $result : $result->toArray();
	}

	/**
	 * @return ORM\EntityRepository<Entities\Resources\Resource>
	 */
	private function getRepository(): ORM\EntityRepository
	{
		if ($this->repository === null) {
			$this->repository = $this->managerRegistry->getRepository(Entities\Resources\Resource::class);
		}

		return $this->repository;
	}

}
