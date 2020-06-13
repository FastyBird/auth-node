<?php declare(strict_types = 1);

/**
 * FindEmailsQuery.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
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
use FastyBird\AuthNode\Exceptions;
use FastyBird\AuthNode\Types;
use IPub\DoctrineOrmQuery;
use Ramsey\Uuid;

/**
 * Find identities entities query
 *
 * @package          FastyBird:AuthNode!
 * @subpackage       Queries
 *
 * @author           Adam Kadlec <adam.kadlec@fastybird.com>
 *
 * @phpstan-template T of Entities\Identities\Identity
 * @phpstan-extends  DoctrineOrmQuery\QueryObject<T>
 */
class FindIdentitiesQuery extends DoctrineOrmQuery\QueryObject
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
			$qb->andWhere('i.id = :id')->setParameter('id', $id->getBytes());
		};
	}

	/**
	 * @param string $uid
	 *
	 * @return void
	 */
	public function byUid(string $uid): void
	{
		$this->filter[] = function (ORM\QueryBuilder $qb) use ($uid): void {
			$qb->andWhere('i.uid = :uid')->setParameter('uid', $uid);
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
			$qb->join('i.account', 'account');
		};

		$this->filter[] = function (ORM\QueryBuilder $qb) use ($account): void {
			$qb->andWhere('account.id = :account')->setParameter('account', $account->getId(), Uuid\Doctrine\UuidBinaryType::NAME);
		};
	}

	/**
	 * @param string $status
	 *
	 * @return void
	 *
	 * @throw Exceptions\InvalidArgumentException
	 */
	public function inStatus(string $status): void
	{
		if (!Types\IdentityStatusType::isValidValue($status)) {
			throw new Exceptions\InvalidArgumentException('Invalid identity status given');
		}

		$this->filter[] = function (ORM\QueryBuilder $qb) use ($status): void {
			$qb->andWhere('i.status = :status')->setParameter('status', $status);
		};
	}

	/**
	 * @param ORM\EntityRepository<Entities\Identities\Identity> $repository
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
	 * @param ORM\EntityRepository<Entities\Identities\Identity> $repository
	 *
	 * @return ORM\QueryBuilder
	 *
	 * @phpstan-param ORM\EntityRepository<T> $repository
	 */
	protected function doCreateCountQuery(ORM\EntityRepository $repository): ORM\QueryBuilder
	{
		$qb = $this->createBasicDql($repository)->select('COUNT(i.id)');

		foreach ($this->select as $modifier) {
			$modifier($qb);
		}

		return $qb;
	}

	/**
	 * @param ORM\EntityRepository<Entities\Identities\Identity> $repository
	 *
	 * @return ORM\QueryBuilder
	 *
	 * @phpstan-param ORM\EntityRepository<T> $repository
	 */
	private function createBasicDql(ORM\EntityRepository $repository): ORM\QueryBuilder
	{
		$qb = $repository->createQueryBuilder('i');

		foreach ($this->filter as $modifier) {
			$modifier($qb);
		}

		return $qb;
	}

}
