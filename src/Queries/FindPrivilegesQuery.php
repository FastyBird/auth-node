<?php declare(strict_types = 1);

/**
 * FindPrivilegesQuery.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Queries
 * @since          0.1.0
 *
 * @date           31.03.20
 */

namespace FastyBird\AuthNode\Queries;

use Closure;
use Doctrine\ORM;
use FastyBird\AuthNode\Entities;
use IPub\DoctrineOrmQuery;
use Ramsey\Uuid;

/**
 * Find privileges entities query
 *
 * @package          FastyBird:AuthNode!
 * @subpackage       Queries
 *
 * @author           Adam Kadlec <adam.kadlec@fastybird.com>
 *
 * @phpstan-template T of Entities\Privileges\Privilege
 * @phpstan-extends  DoctrineOrmQuery\QueryObject<T>
 */
class FindPrivilegesQuery extends DoctrineOrmQuery\QueryObject
{

	/** @var Closure[] */
	private $filter = [];

	/** @var Closure[] */
	private $select = [];

	/**
	 * @param Uuid\UuidInterface $id
	 *
	 * @return void
	 */
	public function byId(Uuid\UuidInterface $id): void
	{
		$this->filter[] = function (ORM\QueryBuilder $qb) use ($id): void {
			$qb->andWhere('p.id = :id')->setParameter('id', $id->getBytes());
		};
	}

	/**
	 * @param string $name
	 *
	 * @return void
	 */
	public function byName(string $name): void
	{
		$this->filter[] = function (ORM\QueryBuilder $qb) use ($name): void {
			$qb->andWhere('p.name = :name')->setParameter('name', $name);
		};
	}

	/**
	 * @param Entities\Resources\IResource $resource
	 *
	 * @return void
	 */
	public function forResource(Entities\Resources\IResource $resource): void
	{
		$this->select[] = function (ORM\QueryBuilder $qb): void {
			$qb->join('p.resource', 'resource');
		};

		$this->filter[] = function (ORM\QueryBuilder $qb) use ($resource): void {
			$qb->andWhere('resource.id = :resource')->setParameter('resource', $resource->getId(), Uuid\Doctrine\UuidBinaryType::NAME);
		};
	}

	/**
	 * @param ORM\EntityRepository<Entities\Privileges\Privilege> $repository
	 *
	 * @return ORM\QueryBuilder
	 *
	 * @phpstan-param ORM\EntityRepository<T> $repository
	 */
	protected function doCreateQuery(ORM\EntityRepository $repository): ORM\QueryBuilder
	{
		return $this->createBasicDql($repository);
	}

	/**
	 * @param ORM\EntityRepository<Entities\Privileges\Privilege> $repository
	 *
	 * @return ORM\QueryBuilder
	 *
	 * @phpstan-param ORM\EntityRepository<T> $repository
	 */
	protected function doCreateCountQuery(ORM\EntityRepository $repository): ORM\QueryBuilder
	{
		return $this->createBasicDql($repository)->select('COUNT(p.id)');
	}

	/**
	 * @param ORM\EntityRepository<Entities\Privileges\Privilege> $repository
	 *
	 * @return ORM\QueryBuilder
	 *
	 * @phpstan-param ORM\EntityRepository<T> $repository
	 */
	private function createBasicDql(ORM\EntityRepository $repository): ORM\QueryBuilder
	{
		$qb = $repository->createQueryBuilder('p');

		foreach ($this->select as $modifier) {
			$modifier($qb);
		}

		foreach ($this->filter as $modifier) {
			$modifier($qb);
		}

		return $qb;
	}

}
