<?php declare(strict_types = 1);

/**
 * IUserAccount.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Entities
 * @since          0.1.0
 *
 * @date           30.03.20
 */

namespace FastyBird\AuthNode\Entities\Accounts;

use FastyBird\AuthNode\Entities;

/**
 * User account entity interface
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IUserAccount extends IAccount
{

	/**
	 * @param Entities\Accounts\IUserAccount $account
	 *
	 * @return void
	 */
	public function setParent(Entities\Accounts\IUserAccount $account): void;

	/**
	 * @return Entities\Accounts\IUserAccount|null
	 */
	public function getParent(): ?Entities\Accounts\IUserAccount;

	/**
	 * @return void
	 */
	public function removeParent(): void;

	/**
	 * @param Entities\Accounts\IUserAccount[] $children
	 *
	 * @return void
	 */
	public function setChildren(array $children): void;

	/**
	 * @param Entities\Accounts\IUserAccount $child
	 *
	 * @return void
	 */
	public function addChild(Entities\Accounts\IUserAccount $child): void;

	/**
	 * @return Entities\Accounts\IUserAccount[]
	 */
	public function getChildren(): array;

	/**
	 * @param Entities\Accounts\IUserAccount $child
	 *
	 * @return void
	 */
	public function removeChild(Entities\Accounts\IUserAccount $child): void;

	/**
	 * @return Entities\Details\IDetails
	 */
	public function getDetails(): Entities\Details\IDetails;

	/**
	 * @param string $requestHash
	 *
	 * @return void
	 */
	public function setRequestHash(string $requestHash): void;

	/**
	 * @return string|null
	 */
	public function getRequestHash(): ?string;

	/**
	 * @param Entities\Emails\IEmail[] $emails
	 *
	 * @return void
	 */
	public function setEmails(array $emails): void;

	/**
	 * @param Entities\Emails\IEmail $email
	 *
	 * @return void
	 */
	public function addEmail(Entities\Emails\IEmail $email): void;

	/**
	 * @return Entities\Emails\IEmail[]
	 */
	public function getEmails(): array;

	/**
	 * @param Entities\Emails\IEmail $email
	 *
	 * @return void
	 */
	public function removeEmail(Entities\Emails\IEmail $email): void;

	/**
	 * @param string|null $id
	 *
	 * @return Entities\Emails\IEmail|null
	 */
	public function getEmail(?string $id = null): ?Entities\Emails\IEmail;

	/**
	 * @return string
	 */
	public function getName(): string;

	/**
	 * @return string
	 */
	public function getLanguage(): string;

}
