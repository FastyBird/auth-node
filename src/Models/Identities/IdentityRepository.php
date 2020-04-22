<?php declare(strict_types = 1);

/**
 * IIdentityRepository.php
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

namespace FastyBird\AccountsNode\Models\Identities;

use Doctrine\Common;
use Doctrine\ORM;
use FastyBird\AccountsNode\Entities;
use FastyBird\AccountsNode\Exceptions;
use FastyBird\AccountsNode\Queries;
use FastyBird\AccountsNode\Types;
use IPub\DoctrineOrmQuery;
use Nette;
use Nette\Utils;
use Ramsey\Uuid;
use Throwable;

/**
 * Account identity facade
 *
 * @package        FastyBird:AccountsNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class IdentityRepository implements IIdentityRepository
{

	use Nette\SmartObject;

	/** @var Common\Persistence\ManagerRegistry */
	private $managerRegistry;

	/** @var ORM\EntityRepository<Entities\Identities\Identity>[] */
	private $repository = [];

	public function __construct(Common\Persistence\ManagerRegistry $managerRegistry)
	{
		$this->managerRegistry = $managerRegistry;
	}

	/**
	 * {@inheritDoc}
	 */
	public function findOneForAccount(
		Entities\Accounts\IAccount $account,
		string $type = Entities\Identities\Identity::class
	): ?Entities\Identities\IIdentity {
		$findQuery = new Queries\FindIdentitiesQuery();
		$findQuery->forAccount($account);
		$findQuery->inStatus(Types\IdentityStatusType::STATE_ACTIVE);

		return $this->findOneBy($findQuery, $type);
	}

	/**
	 * {@inheritDoc}
	 */
	public function findOneByIdentifier(
		string $identifier,
		string $type = Entities\Identities\Identity::class
	): ?Entities\Identities\IIdentity {
		$findQuery = new Queries\FindIdentitiesQuery();
		$findQuery->byId(Uuid\Uuid::fromString($identifier));
		$findQuery->inStatus(Types\IdentityStatusType::STATE_ACTIVE);

		return $this->findOneBy($findQuery, $type);
	}

	/**
	 * {@inheritDoc}
	 */
	public function findOneByEmail(
		string $email,
		string $type = Entities\Identities\Identity::class
	): ?Entities\Identities\IIdentity {
		if (!Utils\Validators::isEmail($email)) {
			throw new Exceptions\EmailIsNotValidException('Provided email address is not valid.');
		}

		$findQuery = new Queries\FindIdentitiesQuery();
		$findQuery->byEmail($email);
		$findQuery->inStatus(Types\IdentityStatusType::STATE_ACTIVE);

		return $this->findOneBy($findQuery, $type);
	}

	/**
	 * {@inheritDoc}
	 */
	public function findOneByUid(
		string $uid,
		string $type = Entities\Identities\Identity::class
	): ?Entities\Identities\IIdentity {
		$findQuery = new Queries\FindIdentitiesQuery();
		$findQuery->byUid($uid);
		$findQuery->inStatus(Types\IdentityStatusType::STATE_ACTIVE);

		return $this->findOneBy($findQuery, $type);
	}

	/**
	 * {@inheritDoc}
	 */
	public function findOneBy(
		Queries\FindIdentitiesQuery $queryObject,
		string $type = Entities\Identities\Identity::class
	): ?Entities\Identities\IIdentity {
		/** @var Entities\Identities\IIdentity|null $identity */
		$identity = $queryObject->fetchOne($this->getRepository($type));

		return $identity;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws Throwable
	 */
	public function getResultSet(
		Queries\FindIdentitiesQuery $queryObject,
		string $type = Entities\Identities\Identity::class
	): DoctrineOrmQuery\ResultSet {
		$result = $queryObject->fetch($this->getRepository($type));

		if (!$result instanceof DoctrineOrmQuery\ResultSet) {
			throw new Exceptions\InvalidStateException('Result set for given query could not be loaded.');
		}

		return $result;
	}

	/**
	 * @param string $type
	 *
	 * @return ORM\EntityRepository<Entities\Identities\Identity>
	 *
	 * @phpstan-template T of Entities\Identities\Identity
	 * @phpstan-param    class-string<T> $type
	 */
	private function getRepository(string $type): ORM\EntityRepository
	{
		if (!isset($this->repository[$type])) {
			$this->repository[$type] = $this->managerRegistry->getRepository($type);
		}

		return $this->repository[$type];
	}

}
