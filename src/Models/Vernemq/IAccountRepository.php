<?php declare(strict_types = 1);

/**
 * IAuthenticationRepository.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Models
 * @since          0.1.0
 *
 * @date           13.06.20
 */

namespace FastyBird\AuthNode\Models\Vernemq;

use FastyBird\AuthNode\Entities;
use FastyBird\AuthNode\Models;
use FastyBird\AuthNode\Queries;

/**
 * VerneMQ account repository interface
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IAccountRepository
{

	/**
	 * @param Queries\FindVerneMqAccountsQuery $queryObject
	 *
	 * @return Entities\Vernemq\IAccount|null
	 *
	 * @phpstan-template T of Entities\Vernemq\Account
	 * @phpstan-param    Queries\FindVerneMqAccountsQuery<T> $queryObject
	 */
	public function findOneBy(Queries\FindVerneMqAccountsQuery $queryObject): ?Entities\Vernemq\IAccount;

	/**
	 * @param Queries\FindVerneMqAccountsQuery $queryObject
	 *
	 * @return Entities\Vernemq\IAccount[]
	 *
	 * @phpstan-template T of Entities\Vernemq\Account
	 * @phpstan-param    Queries\FindVerneMqAccountsQuery<T> $queryObject
	 */
	public function findAllBy(Queries\FindVerneMqAccountsQuery $queryObject): array;

}
