<?php declare(strict_types = 1);

/**
 * PrivilegeRepository.php
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

use Doctrine\Common;
use Doctrine\ORM;
use FastyBird\AccountsNode\Entities;
use FastyBird\AccountsNode\Queries;
use Nette;
use Throwable;

/**
 * ACL privilege repository
 *
 * @package        FastyBird:AccountsNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class PrivilegeRepository implements IPrivilegeRepository
{

	use Nette\SmartObject;

	/** @var Common\Persistence\ManagerRegistry */
	private $managerRegistry;

	/** @var ORM\EntityRepository<Entities\Privileges\Privilege>|null */
	private $repository;

	public function __construct(
		Common\Persistence\ManagerRegistry $managerRegistry
	) {
		$this->managerRegistry = $managerRegistry;
	}

	/**
	 * {@inheritDoc}
	 */
	public function findOneByKeyName(string $keyName): ?Entities\Privileges\IPrivilege
	{
		$findQuery = new Queries\FindPrivilegesQuery();
		$findQuery->byKeyName($keyName);

		return $this->findOneBy($findQuery);
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
	 * @return ORM\EntityRepository<Entities\Privileges\Privilege>
	 */
	private function getRepository(): ORM\EntityRepository
	{
		if ($this->repository === null) {
			$this->repository = $this->managerRegistry->getRepository(Entities\Privileges\Privilege::class);
		}

		return $this->repository;
	}

}
