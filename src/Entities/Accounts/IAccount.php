<?php declare(strict_types = 1);

/**
 * IAccount.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AccountsNode!
 * @subpackage     Entities
 * @since          0.1.0
 *
 * @date           30.03.20
 */

namespace FastyBird\AccountsNode\Entities\Accounts;

use DateTimeInterface;
use FastyBird\AccountsNode\Entities;
use FastyBird\AccountsNode\Types;
use FastyBird\NodeDatabase\Entities as NodeDatabaseEntities;
use IPub\DoctrineTimestampable;

/**
 * Account entity interface
 *
 * @package        FastyBird:AccountsNode!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IAccount extends NodeDatabaseEntities\IEntity,
	NodeDatabaseEntities\IEntityParams,
	DoctrineTimestampable\Entities\IEntityCreated,
	DoctrineTimestampable\Entities\IEntityUpdated
{

	/**
	 * @param IAccount $account
	 *
	 * @return void
	 */
	public function setParent(IAccount $account): void;

	/**
	 * @return IAccount|null
	 */
	public function getParent(): ?IAccount;

	/**
	 * @return void
	 */
	public function removeParent(): void;

	/**
	 * @param IAccount[] $children
	 *
	 * @return void
	 */
	public function setChildren(array $children): void;

	/**
	 * @param IAccount $child
	 *
	 * @return void
	 */
	public function addChild(IAccount $child): void;

	/**
	 * @return IAccount[]
	 */
	public function getChildren(): array;

	/**
	 * @param IAccount $child
	 *
	 * @return void
	 */
	public function removeChild(IAccount $child): void;

	/**
	 * @return Entities\Details\IDetails
	 */
	public function getDetails(): Entities\Details\IDetails;

	/**
	 * @param Types\AccountStatusType $status
	 *
	 * @return void
	 */
	public function setStatus(Types\AccountStatusType $status): void;

	/**
	 * @return Types\AccountStatusType
	 */
	public function getStatus(): Types\AccountStatusType;

	/**
	 * @return void
	 */
	public function setVerified(): void;

	/**
	 * @return bool
	 */
	public function isActivated(): bool;

	/**
	 * @return bool
	 */
	public function isBlocked(): bool;

	/**
	 * @return bool
	 */
	public function isDeleted(): bool;

	/**
	 * @return bool
	 */
	public function isNotActivated(): bool;

	/**
	 * @return bool
	 */
	public function isApprovalRequired(): bool;

	/**
	 * @param DateTimeInterface $lastVisit
	 *
	 * @return void
	 */
	public function setLastVisit(DateTimeInterface $lastVisit): void;

	/**
	 * @return DateTimeInterface|null
	 */
	public function getLastVisit(): ?DateTimeInterface;

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
	 * @param Entities\SecurityQuestions\IQuestion|null $securityQuestion
	 *
	 * @return void
	 */
	public function setSecurityQuestion(?Entities\SecurityQuestions\IQuestion $securityQuestion): void;

	/**
	 * @return Entities\SecurityQuestions\IQuestion|null
	 */
	public function getSecurityQuestion(): ?Entities\SecurityQuestions\IQuestion;

	/**
	 * @return bool
	 */
	public function hasSecurityQuestion(): bool;

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
	 * @return Entities\Identities\IIdentity[]
	 */
	public function getIdentities(): array;

	/**
	 * @param Entities\Roles\IRole[] $roles
	 *
	 * @return void
	 */
	public function setRoles(array $roles): void;

	/**
	 * @param Entities\Roles\IRole $role
	 *
	 * @return void
	 */
	public function addRole(Entities\Roles\IRole $role): void;

	/**
	 * @return Entities\Roles\IRole[]
	 */
	public function getRoles(): array;

	/**
	 * @param Entities\Roles\IRole $role
	 *
	 * @return void
	 */
	public function removeRole(Entities\Roles\IRole $role): void;

	/**
	 * @param Entities\Roles\IRole $role
	 *
	 * @return bool
	 */
	public function hasRole(Entities\Roles\IRole $role): bool;

	/**
	 * @return string
	 */
	public function getName(): string;

	/**
	 * @return string
	 */
	public function getUsername(): string;

	/**
	 * @return string
	 */
	public function getLanguage(): string;

}
