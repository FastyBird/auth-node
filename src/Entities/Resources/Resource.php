<?php declare(strict_types = 1);

/**
 * Resource.php
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

namespace FastyBird\AccountsNode\Entities\Resources;

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
 *     name="fb_acl_resources",
 *     options={
 *       "collate"="utf8mb4_general_ci",
 *       "charset"="utf8mb4",
 *       "comment"="ACL resources"
 *     },
 *     indexes={
 *       @ORM\Index(name="resource_key_name_idx", columns={"resource_key_name"})
 *     }
 * )
 */
class Resource extends NodeDatabaseEntities\Entity implements IResource
{

	use DoctrineTimestampable\Entities\TEntityCreated;
	use DoctrineTimestampable\Entities\TEntityUpdated;

	/**
	 * @var Uuid\UuidInterface
	 *
	 * @ORM\Id
	 * @ORM\Column(type="uuid_binary", name="resource_id")
	 * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
	 */
	protected $id;

	/**
	 * @var string
	 *
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\Column(type="string", name="resource_key_name", length=100, nullable=false)
	 */
	protected $keyName;

	/**
	 * @var string
	 *
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\Column(type="string", name="resource_name", length=100, nullable=false)
	 */
	protected $name;

	/**
	 * @var string|null
	 *
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\Column(type="text", name="resource_comment", nullable=true, options={"default": null})
	 */
	protected $comment = null;

	/**
	 * @var Entities\Resources\IResource|null
	 *
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\ManyToOne(targetEntity="Resource", inversedBy="children")
	 * @ORM\JoinColumn(name="parent_id", referencedColumnName="resource_id", nullable=true, onDelete="set null")
	 */
	protected $parent = null;

	/**
	 * @var Common\Collections\Collection<int, IResource>
	 *
	 * @ORM\OneToMany(targetEntity="Resource", mappedBy="parent")
	 */
	protected $children;

	/**
	 * @var Common\Collections\Collection<int, Entities\Privileges\IPrivilege>
	 *
	 * @ORM\OneToMany(targetEntity="FastyBird\AccountsNode\Entities\Privileges\Privilege", mappedBy="resource", cascade={"persist", "remove"}, orphanRemoval=true)
	 */
	protected $privileges;

	/**
	 * @param string $keyName
	 * @param string $name
	 *
	 * @throws Throwable
	 */
	public function __construct(
		string $keyName,
		string $name
	) {
		$id = Uuid\Uuid::uuid4();

		$this->id = $id;

		$this->keyName = $keyName;
		$this->name = $name;

		$this->children = new Common\Collections\ArrayCollection();
		$this->privileges = new Common\Collections\ArrayCollection();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getResourceId(): string
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
	public function setComment(string $comment): void
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
	public function setParent(?IResource $parent = null): void
	{
		$this->parent = $parent;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getParent(): ?IResource
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
		/** @var IResource $entity */
		foreach ($children as $entity) {
			if (!$this->children->contains($entity)) {
				// ...and assign them to collection
				$this->children->add($entity);
			}
		}

		/** @var IResource $entity */
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
	public function addChild(IResource $child): void
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
	public function setPrivileges(array $privileges): void
	{
		$this->privileges = new Common\Collections\ArrayCollection();

		// Process all passed entities...
		/** @var Entities\Privileges\IPrivilege $entity */
		foreach ($privileges as $entity) {
			if (!$this->privileges->contains($entity)) {
				// ...and assign them to collection
				$this->privileges->add($entity);
			}
		}

		/** @var Entities\Privileges\IPrivilege $entity */
		foreach ($this->privileges as $entity) {
			if (!in_array($entity, $privileges, true)) {
				// ...and remove it from collection
				$this->privileges->removeElement($entity);
			}
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function addPrivilege(Entities\Privileges\IPrivilege $privilege): void
	{
		// Check if collection does not contain inserting entity
		if (!$this->privileges->contains($privilege)) {
			// ...and assign it to collection
			$this->privileges->add($privilege);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function getPrivileges(): array
	{
		return $this->privileges->toArray();
	}

	/**
	 * Convert resource object to string
	 *
	 * @return string
	 */
	public function __toString(): string
	{
		return $this->keyName;
	}

}
