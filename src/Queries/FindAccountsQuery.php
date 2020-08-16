<?php declare(strict_types = 1);

/**
 * FindAccountsQuery.php
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
use FastyBird\AuthNode\Exceptions;
use FastyBird\AuthNode\Types;
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
 * @phpstan-template T of Entities\Accounts\Account
 * @phpstan-extends  DoctrineOrmQuery\QueryObject<T>
 */
class FindAccountsQuery extends DoctrineOrmQuery\QueryObject
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
	 * @param string $state
	 *
	 * @return void
	 *
	 * @throw Exceptions\InvalidArgumentException
	 */
	public function inState(string $state): void
	{
		if (!Types\AccountStateType::isValidValue($state)) {
			throw new Exceptions\InvalidArgumentException('Invalid account state given');
		}

		$this->filter[] = function (ORM\QueryBuilder $qb) use ($state): void {
			$qb->andWhere('a.state = :state')->setParameter('state', $state);
		};
	}

	/**
	 * @param Entities\Roles\IRole $role
	 *
	 * @return void
	 */
	public function inRole(Entities\Roles\IRole $role): void
	{
		$this->select[] = function (ORM\QueryBuilder $qb): void {
			$qb->join('a.roles', 'roles');
		};

		$this->filter[] = function (ORM\QueryBuilder $qb) use ($role): void {
			$qb->andWhere('roles.id = :role')->setParameter('role', $role->getId(), Uuid\Doctrine\UuidBinaryType::NAME);
		};
	}

	/**
	 * @param Entities\Accounts\IMachineAccount $account
	 *
	 * @return void
	 */
	public function forParent(Entities\Accounts\IMachineAccount $account): void
	{
		$this->select[] = function (ORM\QueryBuilder $qb): void {
			$qb->innerJoin(Entities\Accounts\MachineAccount::class, 'ma', ORM\Query\Expr\Join::WITH, 'ma.id = a.id');
			$qb->join('ma.parent', 'parent');
		};

		$this->filter[] = function (ORM\QueryBuilder $qb) use ($account): void {
			$qb->andWhere('parent.id = :parent')->setParameter('parent', $account->getId(), Uuid\Doctrine\UuidBinaryType::NAME);
		};
	}

	/**
	 * @param ORM\EntityRepository<Entities\Accounts\Account> $repository
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
	 * @param ORM\EntityRepository<Entities\Accounts\Account> $repository
	 *
	 * @return ORM\QueryBuilder
	 *
	 * @phpstan-param ORM\EntityRepository<T> $repository
	 */
	protected function doCreateCountQuery(ORM\EntityRepository $repository): ORM\QueryBuilder
	{
		return $this->createBasicDql($repository)->select('COUNT(a.id)');
	}

	/**
	 * @param ORM\EntityRepository<Entities\Accounts\Account> $repository
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

}
