<?php declare(strict_types = 1);

/**
 * IAccountRepository.php
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

use FastyBird\AccountsNode\Entities;
use FastyBird\AccountsNode\Queries;

/**
 * Account repository interface
 *
 * @package        FastyBird:AccountsNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IAccountRepository
{

	/**
	 * @param string $identifier
	 *
	 * @return Entities\Accounts\IAccount|null
	 */
	public function findOneByIdentifier(string $identifier): ?Entities\Accounts\IAccount;

	/**
	 * @param string $hash
	 *
	 * @return Entities\Accounts\IAccount|null
	 */
	public function findOneByHash(string $hash): ?Entities\Accounts\IAccount;

	/**
	 * @param Queries\FindAccountsQuery $queryObject
	 *
	 * @return Entities\Accounts\IAccount|null
	 *
	 * @phpstan-template T of Entities\Accounts\Account
	 * @phpstan-param    Queries\FindAccountsQuery<T> $queryObject
	 */
	public function findOneBy(Queries\FindAccountsQuery $queryObject): ?Entities\Accounts\IAccount;

	/**
	 * @param Queries\FindAccountsQuery $queryObject
	 *
	 * @return Entities\Accounts\IAccount[]
	 *
	 * @phpstan-template T of Entities\Accounts\Account
	 * @phpstan-param    Queries\FindAccountsQuery<T> $queryObject
	 */
	public function findAllBy(Queries\FindAccountsQuery $queryObject): array;

}
