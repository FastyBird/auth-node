<?php declare(strict_types = 1);

/**
 * FindResourcesQuery.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AccountsNode!
 * @subpackage     Queries
 * @since          0.1.0
 *
 * @date           31.03.20
 */

namespace FastyBird\AccountsNode\Queries;

use Closure;
use Doctrine\ORM;
use FastyBird\AccountsNode\Entities;
use IPub\DoctrineOrmQuery;
use Ramsey\Uuid;

/**
 * Find resources entities query
 *
 * @package          FastyBird:AccountsNode!
 * @subpackage       Queries
 *
 * @author           Adam Kadlec <adam.kadlec@fastybird.com>
 *
 * @phpstan-template T of Entities\Resources\Resource
 * @phpstan-extends  DoctrineOrmQuery\QueryObject<T>
 */
class FindResourcesQuery extends DoctrineOrmQuery\QueryObject
{

	/** @var Closure[] */
	private $filter = [];

	/** @var Closure[] */
	private $select = [];

	/**
	 * @param string $keyName
	 *
	 * @return void
	 */
	public function byKeyName(string $keyName): void
	{
		$this->filter[] = function (ORM\QueryBuilder $qb) use ($keyName): void {
			$qb->andWhere('r.keyName = :keyName')->setParameter('keyName', $keyName);
		};
	}

	/**
	 * @param Entities\Resources\IResource $resource
	 *
	 * @return void
	 */
	public function forParent(Entities\Resources\IResource $resource): void
	{
		$this->select[] = function (ORM\QueryBuilder $qb): void {
			$qb->join('r.parent', 'parent');
		};

		$this->filter[] = function (ORM\QueryBuilder $qb) use ($resource): void {
			$qb->andWhere('parent.id = :parent')->setParameter('parent', $resource->getId(), Uuid\Doctrine\UuidBinaryType::NAME);
		};
	}

	/**
	 * @param ORM\EntityRepository<Entities\Resources\Resource> $repository
	 *
	 * @return ORM\QueryBuilder
	 *
	 * @phpstan-param ORM\EntityRepository<T> $repository
	 */
	protected function doCreateQuery(ORM\EntityRepository $repository): ORM\QueryBuilder
	{
		$qb = $this->createBasicDql($repository);

		foreach ($this->select as $modifier) {
			$modifier($qb);
		}

		return $qb;
	}

	/**
	 * @param ORM\EntityRepository<Entities\Resources\Resource> $repository
	 *
	 * @return ORM\QueryBuilder
	 *
	 * @phpstan-param ORM\EntityRepository<T> $repository
	 */
	protected function doCreateCountQuery(ORM\EntityRepository $repository): ORM\QueryBuilder
	{
		$qb = $this->createBasicDql($repository)->select('COUNT(r.id)');

		foreach ($this->select as $modifier) {
			$modifier($qb);
		}

		return $qb;
	}

	/**
	 * @param ORM\EntityRepository<Entities\Resources\Resource> $repository
	 *
	 * @return ORM\QueryBuilder
	 *
	 * @phpstan-param ORM\EntityRepository<T> $repository
	 */
	private function createBasicDql(ORM\EntityRepository $repository): ORM\QueryBuilder
	{
		$qb = $repository->createQueryBuilder('r');

		foreach ($this->filter as $modifier) {
			$modifier($qb);
		}

		return $qb;
	}

}
