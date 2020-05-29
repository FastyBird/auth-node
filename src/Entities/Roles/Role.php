<?php declare(strict_types = 1);

/**
 * Role.php
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

namespace FastyBird\AccountsNode\Entities\Roles;

use Doctrine\Common;
use Doctrine\ORM\Mapping as ORM;
use FastyBird\AccountsNode\Entities;
use FastyBird\NodeDatabase\Entities as NodeDatabaseEntities;
use IPub\DoctrineCrud\Mapping\Annotation as IPubDoctrine;
use IPub\DoctrineTimestampable;
use Ramsey\Uuid;
use Throwable;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="fb_acl_roles",
 *     options={
 *       "collate"="utf8mb4_general_ci",
 *       "charset"="utf8mb4",
 *       "comment"="ACL roles"
 *     },
 *     uniqueConstraints={
 *       @ORM\UniqueConstraint(name="role_key_name_unique", columns={"parent_id", "role_key_name"}),
 *       @ORM\UniqueConstraint(name="role_name_unique", columns={"parent_id", "role_name"})
 *     },
 *     indexes={
 *       @ORM\Index(name="role_key_name_idx", columns={"role_key_name"})
 *     }
 * )
 */
class Role extends NodeDatabaseEntities\Entity implements IRole
{

	use DoctrineTimestampable\Entities\TEntityCreated;
	use DoctrineTimestampable\Entities\TEntityUpdated;

	/**
	 * @var Uuid\UuidInterface
	 *
	 * @ORM\Id
	 * @ORM\Column(type="uuid_binary", name="role_id")
	 * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
	 */
	protected $id;

	/**
	 * @var string
	 *
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\Column(type="string", name="role_key_name", length=100, nullable=false)
	 */
	private $keyName;

	/**
	 * @var string
	 *
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\Column(type="string", name="role_name", length=100, nullable=false)
	 */
	private $name;

	/**
	 * @var string|null
	 *
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\Column(type="text", name="role_comment", nullable=true, options={"default": null})
	 */
	private $comment = null;

	/**
	 * @var IRole|null
	 *
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\ManyToOne(targetEntity="Role", inversedBy="children")
	 * @ORM\JoinColumn(name="parent_id", referencedColumnName="role_id", nullable=true, onDelete="set null")
	 */
	private $parent;

	/**
	 * @var Common\Collections\Collection<int, IRole>
	 *
	 * @ORM\OneToMany(targetEntity="Role", mappedBy="parent")
	 */
	private $children;

	/**
	 * @var int
	 *
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\Column(type="integer", name="priority", length=15, nullable=false, options={"default" = 0})
	 */
	private $priority = 0;

	/**
	 * @var Common\Collections\Collection<int, Entities\Rules\IRule>
	 *
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\OneToMany(targetEntity="FastyBird\AccountsNode\Entities\Rules\Rule", mappedBy="role", cascade={"persist", "remove"}, orphanRemoval=true)
	 */
	private $rules;

	/**
	 * @param string $keyName
	 * @param string $name
	 *
	 * @throws Throwable
	 */
	public function __construct(string $keyName, string $name)
	{
		$id = Uuid\Uuid::uuid4();

		$this->id = $id;

		$this->keyName = $keyName;
		$this->name = $name;

		$this->children = new Common\Collections\ArrayCollection();
		$this->rules = new Common\Collections\ArrayCollection();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getRoleId(): string
	{
		return $this->keyName;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setName(string $name): void
	{
		$this->name = $name;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getName(): ?string
	{
		return $this->name;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setComment(?string $comment): void
	{
		$this->comment = $comment;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getComment(): ?string
	{
		return $this->comment;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setPriority(int $priority): void
	{
		$this->priority = $priority;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getPriority(): int
	{
		return $this->priority;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setParent(?IRole $parent = null): void
	{
		if ($parent !== null) {
			$parent->addChild($this);
		}

		$this->parent = $parent;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getParent(): ?IRole
	{
		return $this->parent;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setChildren(array $children): void
	{
		$this->children = new Common\Collections\ArrayCollection();

		// Process all passed entities...
		/** @var IRole $entity */
		foreach ($children as $entity) {
			if (!$this->children->contains($entity)) {
				// ...and assign them to collection
				$this->children->add($entity);
			}
		}

		/** @var IRole $entity */
		foreach ($this->children as $entity) {
			if (!in_array($entity, $children, true)) {
				// ...and remove it from collection
				$this->children->removeElement($entity);
			}
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function addChild(IRole $child): void
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
	 * @param Entities\Rules\IRule[] $rules
	 *
	 * @return void
	 */
	public function setRules(array $rules): void
	{
		$this->rules = new Common\Collections\ArrayCollection();

		// Process all passed entities...
		/** @var Entities\Rules\IRule $entity */
		foreach ($rules as $entity) {
			if (!$this->rules->contains($entity)) {
				// ...and assign them to collection
				$this->rules->add($entity);
			}
		}

		/** @var Entities\Rules\IRule $entity */
		foreach ($this->rules as $entity) {
			if (!in_array($entity, $rules, true)) {
				// ...and remove it from collection
				$this->rules->removeElement($entity);
			}
		}
	}

	/**
	 * @param Entities\Rules\IRule $rule
	 *
	 * @return void
	 */
	public function addRule(Entities\Rules\IRule $rule): void
	{
		if (!$this->rules->contains($rule)) {
			$this->rules->add($rule);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function getRules(): array
	{
		return $this->rules->toArray();
	}

	/**
	 * {@inheritDoc}
	 */
	public function hasAccess(
		Entities\Resources\IResource $resource,
		Entities\Privileges\IPrivilege $privilege
	): bool {
		/** @var Entities\Rules\IRule $rule */
		foreach ($this->rules as $rule) {
			if (
				$rule->getResource()->getId()->equals($resource->getId())
				&& $rule->getPrivilege()->getId()->equals($privilege->getId())
				&& $rule->hasAccess()
			) {
				return true;
			}
		}

		return false;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isLocked(): bool
	{
		return in_array($this->keyName, [IRole::ROLE_ANONYMOUS, IRole::ROLE_AUTHENTICATED, IRole::ROLE_ADMINISTRATOR], true);
	}

	/**
	 * {@inheritDoc}
	 */
	public function isAnonymous(): bool
	{
		return $this->keyName === IRole::ROLE_ANONYMOUS;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isAuthenticated(): bool
	{
		return $this->keyName === IRole::ROLE_AUTHENTICATED;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isAdministrator(): bool
	{
		return $this->keyName === IRole::ROLE_ADMINISTRATOR;
	}

	/**
	 * Convert role object to string
	 *
	 * @return string
	 */
	public function __toString(): string
	{
		return $this->keyName;
	}

}
