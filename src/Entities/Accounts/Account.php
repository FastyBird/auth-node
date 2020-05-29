<?php declare(strict_types = 1);

/**
 * Account.php
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

use Consistence\Doctrine\Enum\EnumAnnotation as Enum;
use DateTimeInterface;
use Doctrine\Common;
use Doctrine\ORM\Mapping as ORM;
use FastyBird\AccountsNode\Entities;
use FastyBird\AccountsNode\Types;
use FastyBird\NodeDatabase\Entities as NodeDatabaseEntities;
use IPub\DoctrineCrud\Mapping\Annotation as IPubDoctrine;
use IPub\DoctrineTimestampable;
use Ramsey\Uuid;
use Throwable;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="fb_accounts",
 *     options={
 *       "collate"="utf8mb4_general_ci",
 *       "charset"="utf8mb4",
 *       "comment"="User accounts"
 *     },
 *     indexes={
 *       @ORM\Index(name="account_request_hash_idx", columns={"account_request_hash"})
 *     }
 * )
 */
class Account extends NodeDatabaseEntities\Entity implements IAccount
{

	use NodeDatabaseEntities\TEntityParams;
	use DoctrineTimestampable\Entities\TEntityCreated;
	use DoctrineTimestampable\Entities\TEntityUpdated;

	/**
	 * @var Uuid\UuidInterface
	 *
	 * @ORM\Id
	 * @ORM\Column(type="uuid_binary", name="account_id")
	 * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
	 */
	protected $id;

	/**
	 * @var IAccount|null
	 *
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\ManyToOne(targetEntity="Account", inversedBy="children")
	 * @ORM\JoinColumn(name="parent_id", referencedColumnName="account_id", nullable=true, onDelete="set null")
	 */
	private $parent;

	/**
	 * @var Common\Collections\Collection<int, IAccount>
	 *
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\OneToMany(targetEntity="Account", mappedBy="parent")
	 */
	private $children;

	/**
	 * @var Entities\Details\IDetails
	 *
	 * @IPubDoctrine\Crud(is={"required", "writable"})
	 * @ORM\OneToOne(targetEntity="FastyBird\AccountsNode\Entities\Details\Details", mappedBy="account", cascade={"persist", "remove"})
	 */
	private $details;

	/**
	 * @var Types\AccountStatusType
	 *
	 * @Enum(class=Types\AccountStatusType::class)
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\Column(type="string_enum", name="account_status", nullable=false, options={"default": "notActivated"})
	 */
	private $status;

	/**
	 * @var DateTimeInterface
	 *
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\Column(type="datetime", name="account_last_visit", nullable=true, options={"default": null})
	 */
	private $lastVisit = null;

	/**
	 * @var string|null
	 *
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\Column(type="string", name="account_request_hash", nullable=true, options={"default": null})
	 */
	private $requestHash = null;

	/**
	 * @var Entities\SecurityQuestions\IQuestion|null
	 *
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\OneToOne(targetEntity="FastyBird\AccountsNode\Entities\SecurityQuestions\Question", mappedBy="account", cascade={"persist", "remove"})
	 */
	private $securityQuestion = null;

	/**
	 * @var Common\Collections\Collection<int, Entities\Emails\IEmail>
	 *
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\OneToMany(targetEntity="FastyBird\AccountsNode\Entities\Emails\Email", mappedBy="account", cascade={"persist", "remove"}, orphanRemoval=true)
	 */
	private $emails;

	/**
	 * @var Common\Collections\Collection<int, Entities\Identities\IIdentity>
	 *
	 * @ORM\OneToMany(targetEntity="FastyBird\AccountsNode\Entities\Identities\Identity", mappedBy="account")
	 */
	private $identities;

	/**
	 * @var Common\Collections\Collection<int, Entities\Roles\IRole>
	 *
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\ManyToMany(targetEntity="FastyBird\AccountsNode\Entities\Roles\Role")
	 * @ORM\JoinTable(name="fb_accounts_roles",
	 *    joinColumns={
	 *       @ORM\JoinColumn(name="account_id", referencedColumnName="account_id", onDelete="cascade")
	 *    },
	 *    inverseJoinColumns={
	 *       @ORM\JoinColumn(name="role_id", referencedColumnName="role_id", onDelete="cascade")
	 *    }
	 * )
	 */
	private $roles;

	/**
	 * @param Uuid\UuidInterface|null $id
	 *
	 * @throws Throwable
	 */
	public function __construct(
		?Uuid\UuidInterface $id = null
	) {
		$this->id = $id ?? Uuid\Uuid::uuid4();

		$this->status = Types\AccountStatusType::get(Types\AccountStatusType::STATE_NOT_ACTIVATED);

		$this->emails = new Common\Collections\ArrayCollection();
		$this->identities = new Common\Collections\ArrayCollection();
		$this->children = new Common\Collections\ArrayCollection();
		$this->roles = new Common\Collections\ArrayCollection();
	}

	/**
	 * {@inheritDoc}
	 */
	public function setParent(IAccount $account): void
	{
		$this->parent = $account;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getParent(): ?IAccount
	{
		return $this->parent;
	}

	/**
	 * {@inheritDoc}
	 */
	public function removeParent(): void
	{
		$this->parent = null;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setChildren(array $children): void
	{
		$this->children = new Common\Collections\ArrayCollection();

		// Process all passed entities...
		/** @var IAccount $entity */
		foreach ($children as $entity) {
			if (!$this->children->contains($entity)) {
				// ...and assign them to collection
				$this->children->add($entity);
			}
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function addChild(IAccount $child): void
	{
		// Check if collection does not contain inserting entity
		if (!$this->children->contains($child)) {
			// ...and assign it to collection
			$this->children->add($child);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function getChildren(): array
	{
		return $this->children->toArray();
	}

	/**
	 * {@inheritDoc}
	 */
	public function removeChild(IAccount $child): void
	{
		// Check if collection contain removing entity...
		if ($this->children->contains($child)) {
			// ...and remove it from collection
			$this->children->removeElement($child);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDetails(): Entities\Details\IDetails
	{
		return $this->details;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setStatus(Types\AccountStatusType $status): void
	{
		$this->status = $status;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getStatus(): Types\AccountStatusType
	{
		return $this->status;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setVerified(): void
	{
		$this->status->equalsValue(Types\AccountStatusType::STATE_ACTIVATED);
	}

	/**
	 * {@inheritDoc}
	 */
	public function isActivated(): bool
	{
		return $this->status->equalsValue(Types\AccountStatusType::STATE_ACTIVATED);
	}

	/**
	 * {@inheritDoc}
	 */
	public function isBlocked(): bool
	{
		return $this->status->equalsValue(Types\AccountStatusType::STATE_BLOCKED);
	}

	/**
	 * {@inheritDoc}
	 */
	public function isDeleted(): bool
	{
		return $this->status->equalsValue(Types\AccountStatusType::STATE_DELETED);
	}

	/**
	 * {@inheritDoc}
	 */
	public function isNotActivated(): bool
	{
		return $this->status->equalsValue(Types\AccountStatusType::STATE_NOT_ACTIVATED);
	}

	/**
	 * {@inheritDoc}
	 */
	public function isApprovalRequired(): bool
	{
		return $this->status->equalsValue(Types\AccountStatusType::STATE_APPROVAL_WAITING);
	}

	/**
	 * {@inheritDoc}
	 */
	public function setLastVisit(DateTimeInterface $lastVisit): void
	{
		$this->lastVisit = $lastVisit;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getLastVisit(): ?DateTimeInterface
	{
		return $this->lastVisit;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setRequestHash(string $requestHash): void
	{
		$this->requestHash = $requestHash;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getRequestHash(): ?string
	{
		return $this->requestHash;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setSecurityQuestion(?Entities\SecurityQuestions\IQuestion $securityQuestion): void
	{
		$this->securityQuestion = $securityQuestion;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getSecurityQuestion(): ?Entities\SecurityQuestions\IQuestion
	{
		return $this->securityQuestion;
	}

	/**
	 * {@inheritDoc}
	 */
	public function hasSecurityQuestion(): bool
	{
		return $this->securityQuestion !== null;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setEmails(array $emails): void
	{
		$this->emails = new Common\Collections\ArrayCollection();

		// Process all passed entities...
		/** @var Entities\Emails\IEmail $entity */
		foreach ($emails as $entity) {
			if (!$this->emails->contains($entity)) {
				// ...and assign them to collection
				$this->emails->add($entity);
			}
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function addEmail(Entities\Emails\IEmail $email): void
	{
		// Check if collection does not contain inserting entity
		if (!$this->emails->contains($email)) {
			// ...and assign it to collection
			$this->emails->add($email);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function getEmails(): array
	{
		return $this->emails->toArray();
	}

	/**
	 * {@inheritDoc}
	 */
	public function removeEmail(Entities\Emails\IEmail $email): void
	{
		// Check if collection contain removing entity...
		if ($this->emails->contains($email)) {
			// ...and remove it from collection
			$this->emails->removeElement($email);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function getEmail(?string $id = null): ?Entities\Emails\IEmail
	{
		if ($this->emails !== null) {
			$email = $this->emails
				->filter(function (Entities\Emails\IEmail $row) use ($id): bool {
					return $id !== null ? $row->getId()->equals(Uuid\Uuid::fromString($id)) : $row->isDefault();
				})
				->first();

			return $email !== false ? $email : null;
		}

		return null;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getIdentities(): array
	{
		return $this->identities->toArray();
	}

	/**
	 * {@inheritDoc}
	 */
	public function setRoles(array $roles): void
	{
		$this->roles = new Common\Collections\ArrayCollection();

		// Process all passed entities...
		/** @var Entities\Roles\IRole $entity */
		foreach ($roles as $entity) {
			if (!$this->roles->contains($entity)) {
				// ...and assign them to collection
				$this->roles->add($entity);
			}
		}

		/** @var Entities\Roles\IRole $entity */
		foreach ($this->roles as $entity) {
			if (!in_array($entity, $roles, true)) {
				// ...and remove it from collection
				$this->roles->removeElement($entity);
			}
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function addRole(Entities\Roles\IRole $role): void
	{
		// Check if collection does not contain inserting entity
		if (!$this->roles->contains($role)) {
			// ...and assign it to collection
			$this->roles->add($role);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function getRoles(): array
	{
		return $this->roles->toArray();
	}

	/**
	 * {@inheritDoc}
	 */
	public function removeRole(Entities\Roles\IRole $role): void
	{
		// Check if collection contain removing entity...
		if ($this->roles->contains($role)) {
			// ...and remove it from collection
			$this->roles->removeElement($role);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function hasRole(Entities\Roles\IRole $role): bool
	{
		if ($this->roles !== null) {
			$role = $this->roles
				->filter(function (Entities\Roles\IRole $row) use ($role): bool {
					return $role->getId() === $row->getId();
				})
				->first();

			return $role !== false;
		}

		return false;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getName(): string
	{
		return $this->details->getLastName() . ' ' . $this->details->getFirstName();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getUsername(): string
	{
		return 'account_' . $this->id->toString();
	}

	/**
	 * {@inheritDoc}
	 *
	 * TODO: Should be refactored
	 */
	public function getLanguage(): string
	{
		return 'en';
	}

}
