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

use FastyBird\AccountsNode\Entities;
use FastyBird\AccountsNode\Models;
use FastyBird\AccountsNode\Queries;
use IPub\DoctrineOrmQuery;

/**
 * Account identity repository interface
 *
 * @package        FastyBird:AccountsNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IIdentityRepository
{

	/**
	 * @param Entities\Accounts\IAccount $account
	 * @param string $type
	 *
	 * @return Entities\Identities\IIdentity|null
	 *
	 * @phpstan-template T of Entities\Identities\Identity
	 * @phpstan-param    Entities\Accounts\IAccount $account
	 * @phpstan-param    class-string<T> $type
	 */
	public function findOneForAccount(
		Entities\Accounts\IAccount $account,
		string $type = Entities\Identities\Identity::class
	): ?Entities\Identities\IIdentity;

	/**
	 * @param string $identifier
	 * @param string|null $type
	 *
	 * @return Entities\Identities\IIdentity|null
	 *
	 * @phpstan-template T of Entities\Identities\Identity
	 * @phpstan-param    string $identifier
	 * @phpstan-param    class-string<T> $type
	 */
	public function findOneByIdentifier(
		string $identifier,
		string $type = Entities\Identities\Identity::class
	): ?Entities\Identities\IIdentity;

	/**
	 * @param string $email
	 * @param string|null $type
	 *
	 * @return Entities\Identities\IIdentity|null
	 *
	 * @phpstan-template T of Entities\Identities\Identity
	 * @phpstan-param    string $email
	 * @phpstan-param    class-string<T> $type
	 */
	public function findOneByEmail(
		string $email,
		string $type = Entities\Identities\Identity::class
	): ?Entities\Identities\IIdentity;

	/**
	 * @param string $uid
	 * @param string|null $type
	 *
	 * @return Entities\Identities\IIdentity|null
	 *
	 * @phpstan-template T of Entities\Identities\Identity
	 * @phpstan-param    string $uid
	 * @phpstan-param    class-string<T> $type
	 */
	public function findOneByUid(
		string $uid,
		string $type = Entities\Identities\Identity::class
	): ?Entities\Identities\IIdentity;

	/**
	 * @param Queries\FindIdentitiesQuery $queryObject
	 * @param string $type
	 *
	 * @return Entities\Identities\IIdentity|null
	 *
	 * @phpstan-template T of Entities\Identities\Identity
	 * @phpstan-param    Queries\FindIdentitiesQuery<T> $queryObject
	 * @phpstan-param    class-string<T> $type
	 */
	public function findOneBy(
		Queries\FindIdentitiesQuery $queryObject,
		string $type = Entities\Identities\Identity::class
	): ?Entities\Identities\IIdentity;

	/**
	 * @param Queries\FindIdentitiesQuery $queryObject
	 * @param string $type
	 *
	 * @return DoctrineOrmQuery\ResultSet
	 *
	 * @phpstan-template T of Entities\Identities\Identity
	 * @phpstan-param    Queries\FindIdentitiesQuery<T> $queryObject
	 * @phpstan-param    class-string<T> $type
	 * @phpstan-return   DoctrineOrmQuery\ResultSet<T>
	 */
	public function getResultSet(
		Queries\FindIdentitiesQuery $queryObject,
		string $type = Entities\Identities\Identity::class
	): DoctrineOrmQuery\ResultSet;

}
