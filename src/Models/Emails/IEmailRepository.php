<?php declare(strict_types = 1);

/**
 * IEmailRepository.php
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

namespace FastyBird\AccountsNode\Models\Emails;

use FastyBird\AccountsNode\Entities;
use FastyBird\AccountsNode\Models;
use FastyBird\AccountsNode\Queries;
use IPub\DoctrineOrmQuery;

/**
 * Account email address repository interface
 *
 * @package        FastyBird:AccountsNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IEmailRepository
{

	/**
	 * @param string $identifier
	 *
	 * @return Entities\Emails\IEmail|null
	 */
	public function findOneByIdentifier(string $identifier): ?Entities\Emails\IEmail;

	/**
	 * @param string $address
	 *
	 * @return Entities\Emails\IEmail|null
	 */
	public function findOneByAddress(string $address): ?Entities\Emails\IEmail;

	/**
	 * @param Queries\FindEmailsQuery $queryObject
	 *
	 * @return Entities\Emails\IEmail|null
	 *
	 * @phpstan-template T of Entities\Emails\Email
	 * @phpstan-param    Queries\FindEmailsQuery<T> $queryObject
	 */
	public function findOneBy(Queries\FindEmailsQuery $queryObject): ?Entities\Emails\IEmail;

	/**
	 * @param Queries\FindEmailsQuery $queryObject
	 *
	 * @return DoctrineOrmQuery\ResultSet
	 *
	 * @phpstan-template T of Entities\Emails\Email
	 * @phpstan-param    Queries\FindEmailsQuery<T> $queryObject
	 * @phpstan-return   DoctrineOrmQuery\ResultSet<T>
	 */
	public function getResultSet(
		Queries\FindEmailsQuery $queryObject
	): DoctrineOrmQuery\ResultSet;

	/**
	 * @param string $address
	 * @param Entities\Accounts\IAccount $account
	 *
	 * @return bool
	 */
	public function isEmailAvailable(string $address, Entities\Accounts\IAccount $account): bool;

}
