<?php declare(strict_types = 1);

/**
 * AccountRepository.php
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

namespace FastyBird\AccountsNode\Models\Accounts;

use Doctrine\Common;
use Doctrine\ORM;
use FastyBird\AccountsNode\Entities;
use FastyBird\AccountsNode\Queries;
use Nette;
use Ramsey\Uuid;
use Throwable;

/**
 * Account repository
 *
 * @package        FastyBird:AccountsNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class AccountRepository implements IAccountRepository
{

	use Nette\SmartObject;

	/** @var Common\Persistence\ManagerRegistry */
	private $managerRegistry;

	/** @var ORM\EntityRepository<Entities\Accounts\Account>|null */
	private $repository;

	public function __construct(
		Common\Persistence\ManagerRegistry $managerRegistry
	) {
		$this->managerRegistry = $managerRegistry;
	}

	/**
	 * {@inheritDoc}
	 */
	public function findOneByIdentifier(string $identifier): ?Entities\Accounts\IAccount
	{
		$findQuery = new Queries\FindAccountsQuery();
		$findQuery->byId(Uuid\Uuid::fromString($identifier));

		return $this->findOneBy($findQuery);
	}

	/**
	 * {@inheritDoc}
	 */
	public function findOneByHash(string $hash): ?Entities\Accounts\IAccount
	{
		$findQuery = new Queries\FindAccountsQuery();
		$findQuery->byHash($hash);

		return $this->findOneBy($findQuery);
	}

	/**
	 * {@inheritDoc}
	 */
	public function findOneBy(Queries\FindAccountsQuery $queryObject): ?Entities\Accounts\IAccount
	{
		/** @var Entities\Accounts\IAccount|null $account */
		$account = $queryObject->fetchOne($this->getRepository());

		return $account;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws Throwable
	 */
	public function findAllBy(Queries\FindAccountsQuery $queryObject): array
	{
		$result = $queryObject->fetch($this->getRepository());

		return is_array($result) ? $result : $result->toArray();
	}

	/**
	 * @return ORM\EntityRepository<Entities\Accounts\Account>
	 */
	private function getRepository(): ORM\EntityRepository
	{
		if ($this->repository === null) {
			$this->repository = $this->managerRegistry->getRepository(Entities\Accounts\Account::class);
		}

		return $this->repository;
	}

}
