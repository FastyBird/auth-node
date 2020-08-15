<?php declare(strict_types = 1);

/**
 * Account.php
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

use Consistence\Doctrine\Enum\EnumAnnotation as Enum;
use DateTimeInterface;
use Doctrine\Common;
use Doctrine\ORM\Mapping as ORM;
use FastyBird\AuthNode\Entities;
use FastyBird\AuthNode\Types;
use FastyBird\NodeDatabase\Entities as NodeDatabaseEntities;
use IPub\DoctrineBlameable;
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
 *       "comment"="Application accounts"
 *     }
 * )
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="account_type", type="string", length=20)
 * @ORM\DiscriminatorMap({
 *      "account"   = "FastyBird\AuthNode\Entities\Accounts\Account",
 *      "user"      = "FastyBird\AuthNode\Entities\Accounts\UserAccount",
 *      "machine"   = "FastyBird\AuthNode\Entities\Accounts\MachineAccount",
 *      "node"      = "FastyBird\AuthNode\Entities\Accounts\NodeAccount"
 * })
 * @ORM\MappedSuperclass
 */
abstract class Account implements IAccount
{

	use NodeDatabaseEntities\TEntity;
	use NodeDatabaseEntities\TEntityParams;
	use DoctrineTimestampable\Entities\TEntityCreated;
	use DoctrineTimestampable\Entities\TEntityUpdated;
	use DoctrineBlameable\Entities\TEntityCreator;
	use DoctrineBlameable\Entities\TEntityEditor;

	/**
	 * @var Uuid\UuidInterface
	 *
	 * @ORM\Id
	 * @ORM\Column(type="uuid_binary", name="account_id")
	 * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
	 */
	protected $id;

	/**
	 * @var Types\AccountStateType
	 *
	 * @Enum(class=Types\AccountStateType::class)
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\Column(type="string_enum", name="account_state", nullable=false, options={"default": "notActivated"})
	 */
	protected $state;

	/**
	 * @var DateTimeInterface
	 *
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\Column(type="datetime", name="account_last_visit", nullable=true, options={"default": null})
	 */
	protected $lastVisit = null;

	/**
	 * @var Common\Collections\Collection<int, Entities\Identities\IIdentity>
	 *
	 * @ORM\OneToMany(targetEntity="FastyBird\AuthNode\Entities\Identities\Identity", mappedBy="account")
	 */
	protected $identities;

	/**
	 * @var Common\Collections\Collection<int, Entities\Roles\IRole>
	 *
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\ManyToMany(targetEntity="FastyBird\AuthNode\Entities\Roles\Role")
	 * @ORM\JoinTable(name="fb_accounts_roles",
	 *    joinColumns={
	 *       @ORM\JoinColumn(name="account_id", referencedColumnName="account_id", onDelete="cascade")
	 *    },
	 *    inverseJoinColumns={
	 *       @ORM\JoinColumn(name="role_id", referencedColumnName="role_id", onDelete="cascade")
	 *    }
	 * )
	 */
	protected $roles;

	/**
	 * @param Uuid\UuidInterface|null $id
	 *
	 * @throws Throwable
	 */
	public function __construct(
		?Uuid\UuidInterface $id = null
	) {
		$this->id = $id ?? Uuid\Uuid::uuid4();

		$this->state = Types\AccountStateType::get(Types\AccountStateType::STATE_NOT_ACTIVATED);

		$this->identities = new Common\Collections\ArrayCollection();
		$this->roles = new Common\Collections\ArrayCollection();
	}

	/**
	 * {@inheritDoc}
	 */
	public function setState(Types\AccountStateType $state): void
	{
		$this->state = $state;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getState(): Types\AccountStateType
	{
		return $this->state;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isActivated(): bool
	{
		return $this->state->equalsValue(Types\AccountStateType::STATE_ACTIVATED);
	}

	/**
	 * {@inheritDoc}
	 */
	public function isBlocked(): bool
	{
		return $this->state->equalsValue(Types\AccountStateType::STATE_BLOCKED);
	}

	/**
	 * {@inheritDoc}
	 */
	public function isDeleted(): bool
	{
		return $this->state->equalsValue(Types\AccountStateType::STATE_DELETED);
	}

	/**
	 * {@inheritDoc}
	 */
	public function isNotActivated(): bool
	{
		return $this->state->equalsValue(Types\AccountStateType::STATE_NOT_ACTIVATED);
	}

	/**
	 * {@inheritDoc}
	 */
	public function isApprovalRequired(): bool
	{
		return $this->state->equalsValue(Types\AccountStateType::STATE_APPROVAL_WAITING);
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
	public function hasRole(string $role): bool
	{
		if ($this->roles !== null) {
			$role = $this->roles
				->filter(function (Entities\Roles\IRole $row) use ($role): bool {
					return $role === $row->getRoleId();
				})
				->first();

			return $role !== false;
		}

		return false;
	}

	/**
	 * {@inheritDoc}
	 */
	public function toArray(): array
	{
		return [
			'id'         => $this->getPlainId(),
			'state'      => $this->getState()->getValue(),
			'registered' => $this->getCreatedAt() !== null ? $this->getCreatedAt()->format(DATE_ATOM) : null,
			'last_visit' => $this->getLastVisit() !== null ? $this->getLastVisit()->format(DATE_ATOM) : null,
			'roles'      => array_map(function (Entities\Roles\IRole $role): string {
				return $role->getRoleId();
			}, $this->getRoles()),
		];
	}

}
