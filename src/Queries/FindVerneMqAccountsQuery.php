<?php declare(strict_types = 1);

/**
 * FindVerneMqAccountsQuery.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Queries
 * @since          0.1.0
 *
 * @date           13.06.20
 */

namespace FastyBird\AuthNode\Queries;

use Closure;
use Doctrine\ORM;
use FastyBird\AuthModule\Entities as AuthModuleEntities;
use FastyBird\AuthNode\Entities;
use IPub\DoctrineOrmQuery;
use Ramsey\Uuid;

/**
 * Find roles entities query
 *
 * @package          FastyBird:AuthNode!
 * @subpackage       Queries
 *
 * @author           Adam Kadlec <adam.kadlec@fastybird.com>
 *
 * @phpstan-template T of Entities\Vernemq\Account
 * @phpstan-extends  DoctrineOrmQuery\QueryObject<T>
 */
class FindVerneMqAccountsQuery extends DoctrineOrmQuery\QueryObject
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
			$qb->andWhere('a.id = :id')->setParameter('id', $id->getBytes());
		};
	}

	/**
	 * @param string $username
	 *
	 * @return void
	 */
	public function byUsername(string $username): void
	{
		$this->filter[] = function (ORM\QueryBuilder $qb) use ($username): void {
			$qb->andWhere('a.username = :username')->setParameter('username', $username);
		};
	}

	/**
	 * @param AuthModuleEntities\Identities\IIdentity $identity
	 *
	 * @return void
	 */
	public function forIdentity(AuthModuleEntities\Identities\IIdentity $identity): void
	{
		$this->select[] = function (ORM\QueryBuilder $qb): void {
			$qb->join('a.identity', 'identity');
		};

		$this->filter[] = function (ORM\QueryBuilder $qb) use ($identity): void {
			$qb->andWhere('identity.id = :identity')->setParameter('identity', $identity->getId(), Uuid\Doctrine\UuidBinaryType::NAME);
		};
	}

	/**
	 * @param AuthModuleEntities\Accounts\IAccount $account
	 *
	 * @return void
	 */
	public function forAccount(AuthModuleEntities\Accounts\IAccount $account): void
	{
		$this->select[] = function (ORM\QueryBuilder $qb): void {
			$qb->join('a.identity', 'identity');
			$qb->join('identity.account', 'account');
		};

		$this->filter[] = function (ORM\QueryBuilder $qb) use ($account): void {
			$qb->andWhere('account.id = :account')->setParameter('account', $account->getId(), Uuid\Doctrine\UuidBinaryType::NAME);
		};
	}

	/**
	 * @param ORM\EntityRepository<Entities\Vernemq\Account> $repository
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
	 * @param ORM\EntityRepository<Entities\Vernemq\Account> $repository
	 *
	 * @return ORM\QueryBuilder
	 *
	 * @phpstan-param ORM\EntityRepository<T> $repository
	 */
	private function createBasicDql(ORM\EntityRepository $repository): ORM\QueryBuilder
	{
		$qb = $repository->createQueryBuilder('a');

		foreach ($this->select as $modifier) {
			$modifier($qb);
		}

		foreach ($this->filter as $modifier) {
			$modifier($qb);
		}

		return $qb;
	}

	/**
	 * @param ORM\EntityRepository<Entities\Vernemq\Account> $repository
	 *
	 * @return ORM\QueryBuilder
	 *
	 * @phpstan-param ORM\EntityRepository<T> $repository
	 */
	protected function doCreateCountQuery(ORM\EntityRepository $repository): ORM\QueryBuilder
	{
		return $this->createBasicDql($repository)->select('COUNT(a.id)');
	}

}
