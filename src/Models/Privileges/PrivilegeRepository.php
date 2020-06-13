<?php declare(strict_types = 1);

/**
 * PrivilegeRepository.php
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

use Doctrine\Common;
use Doctrine\Persistence;
use FastyBird\AuthNode\Entities;
use FastyBird\AuthNode\Queries;
use Nette;
use Throwable;

/**
 * ACL privilege repository
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class PrivilegeRepository implements IPrivilegeRepository
{

	use Nette\SmartObject;

	/** @var Common\Persistence\ManagerRegistry */
	private $managerRegistry;

	/** @var Persistence\ObjectRepository<Entities\Privileges\Privilege>|null */
	private $repository;

	public function __construct(
		Common\Persistence\ManagerRegistry $managerRegistry
	) {
		$this->managerRegistry = $managerRegistry;
	}

	/**
	 * {@inheritDoc}
	 */
	public function findOneBy(Queries\FindPrivilegesQuery $queryObject): ?Entities\Privileges\IPrivilege
	{
		/** @var Entities\Privileges\IPrivilege|null $privilege */
		$privilege = $queryObject->fetchOne($this->getRepository());

		return $privilege;
	}

	/**
	 * @return Entities\Privileges\IPrivilege[]
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
	 * @return Persistence\ObjectRepository<Entities\Privileges\Privilege>
	 */
	private function getRepository(): Persistence\ObjectRepository
	{
		if ($this->repository === null) {
			$this->repository = $this->managerRegistry->getRepository(Entities\Privileges\Privilege::class);
		}

		return $this->repository;
	}

}
