<?php declare(strict_types = 1);

/**
 * IRuleRepository.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Models
 * @since          0.1.0
 *
 * @date           14.06.20
 */

namespace FastyBird\AuthNode\Models\Rules;

use FastyBird\AuthNode\Entities;
use FastyBird\AuthNode\Models;
use FastyBird\AuthNode\Queries;
use IPub\DoctrineOrmQuery;

/**
 * ACL rules repository interface
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IRuleRepository
{

	/**
	 * @param Queries\FindRulesQuery $queryObject
	 *
	 * @return Entities\Rules\IRule|null
	 *
	 * @phpstan-template T of Entities\Rules\Rule
	 * @phpstan-param    Queries\FindRulesQuery<T> $queryObject
	 */
	public function findOneBy(Queries\FindRulesQuery $queryObject): ?Entities\Rules\IRule;

	/**
	 * @return Entities\Rules\IRule[]
	 */
	public function findAll(): array;

	/**
	 * @param Queries\FindRulesQuery $queryObject
	 *
	 * @return Entities\Rules\IRule[]
	 *
	 * @phpstan-template T of Entities\Rules\Rule
	 * @phpstan-param    Queries\FindRulesQuery<T> $queryObject
	 */
	public function findAllBy(Queries\FindRulesQuery $queryObject): array;

	/**
	 * @param Queries\FindRulesQuery $queryObject
	 *
	 * @return DoctrineOrmQuery\ResultSet
	 *
	 * @phpstan-template T of Entities\Rules\Rule
	 * @phpstan-param    Queries\FindRulesQuery<T> $queryObject
	 * @phpstan-return   DoctrineOrmQuery\ResultSet<T>
	 */
	public function getResultSet(Queries\FindRulesQuery $queryObject): DoctrineOrmQuery\ResultSet;

}
