<?php declare(strict_types = 1);

/**
 * IRoleRepository.php
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

namespace FastyBird\AccountsNode\Models\Roles;

use Doctrine\Common;
use Doctrine\ORM;
use FastyBird\AccountsNode\Entities;
use FastyBird\AccountsNode\Queries;
use Nette;
use Throwable;

/**
 * ACL role repository
 *
 * @package        FastyBird:AccountsNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class RoleRepository implements IRoleRepository
{

	use Nette\SmartObject;

	/** @var Common\Persistence\ManagerRegistry */
	private $managerRegistry;

	/** @var ORM\EntityRepository<Entities\Roles\Role>|null */
	private $repository;

	public function __construct(
		Common\Persistence\ManagerRegistry $managerRegistry
	) {
		$this->managerRegistry = $managerRegistry;
	}

	/**
	 * {@inheritDoc}
	 */
	public function findOneByKeyName(string $keyName): ?Entities\Roles\IRole
	{
		$findQuery = new Queries\FindRolesQuery();
		$findQuery->byKeyName($keyName);

		return $this->findOneBy($findQuery);
	}

	/**
	 * {@inheritDoc}
	 */
	public function findOneBy(Queries\FindRolesQuery $queryObject): ?Entities\Roles\IRole
	{
		/** @var Entities\Roles\IRole|null $role */
		$role = $queryObject->fetchOne($this->getRepository());

		return $role;
	}

	/**
	 * @return Entities\Roles\IRole[]
	 *
	 * @throws Throwable
	 */
	public function findAll(): array
	{
		$queryObject = new Queries\FindRolesQuery();

		$result = $queryObject->fetch($this->getRepository());

		return is_array($result) ? $result : $result->toArray();
	}

	/**
	 * @return ORM\EntityRepository<Entities\Roles\Role>
	 */
	private function getRepository(): ORM\EntityRepository
	{
		if ($this->repository === null) {
			$this->repository = $this->managerRegistry->getRepository(Entities\Roles\Role::class);
		}

		return $this->repository;
	}

}
