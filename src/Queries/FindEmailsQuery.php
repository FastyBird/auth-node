<?php declare(strict_types = 1);

/**
 * FindEmailsQuery.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Queries
 * @since          0.1.0
 *
 * @date           30.03.20
 */

namespace FastyBird\AuthNode\Queries;

use Closure;
use Doctrine\ORM;
use FastyBird\AuthNode\Entities;
use IPub\DoctrineOrmQuery;
use Ramsey\Uuid;

/**
 * Find accounts entities query
 *
 * @package          FastyBird:AuthNode!
 * @subpackage       Queries
 *
 * @author           Adam Kadlec <adam.kadlec@fastybird.com>
 *
 * @phpstan-template T of Entities\Emails\Email
 * @phpstan-extends  DoctrineOrmQuery\QueryObject<T>
 */
class FindEmailsQuery extends DoctrineOrmQuery\QueryObject
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
			$qb->andWhere('e.id = :id')->setParameter('id', $id->getBytes());
		};
	}

	/**
	 * @param string $address
	 *
	 * @return void
	 */
	public function byAddress(string $address): void
	{
		$this->filter[] = function (ORM\QueryBuilder $qb) use ($address): void {
			$qb->andWhere('e.address = :address')->setParameter('address', $address);
		};
	}

	/**
	 * @param Entities\Accounts\IAccount $account
	 *
	 * @return void
	 */
	public function forAccount(Entities\Accounts\IAccount $account): void
	{
		$this->select[] = function (ORM\QueryBuilder $qb): void {
			$qb->join('e.account', 'account');
		};

		$this->filter[] = function (ORM\QueryBuilder $qb) use ($account): void {
			$qb->andWhere('account.id = :account')->setParameter('account', $account->getId(), Uuid\Doctrine\UuidBinaryType::NAME);
		};
	}

	/**
	 * @param ORM\EntityRepository<Entities\Emails\Email> $repository
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
	 * @param ORM\EntityRepository<Entities\Emails\Email> $repository
	 *
	 * @return ORM\QueryBuilder
	 *
	 * @phpstan-param ORM\EntityRepository<T> $repository
	 */
	protected function doCreateCountQuery(ORM\EntityRepository $repository): ORM\QueryBuilder
	{
		return $this->createBasicDql($repository)->select('COUNT(e.id)');
	}

	/**
	 * @param ORM\EntityRepository<Entities\Emails\Email> $repository
	 *
	 * @return ORM\QueryBuilder
	 *
	 * @phpstan-param ORM\EntityRepository<T> $repository
	 */
	private function createBasicDql(ORM\EntityRepository $repository): ORM\QueryBuilder
	{
		$qb = $repository->createQueryBuilder('e');

		foreach ($this->select as $modifier) {
			$modifier($qb);
		}

		foreach ($this->filter as $modifier) {
			$modifier($qb);
		}

		return $qb;
	}

}
