<?php declare(strict_types = 1);

/**
 * Privilege.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Entities
 * @since          0.1.0
 *
 * @date           30.03.20
 */

namespace FastyBird\AuthNode\Entities\Privileges;

use Doctrine\ORM\Mapping as ORM;
use FastyBird\AuthNode\Entities;
use FastyBird\NodeDatabase\Entities as NodeDatabaseEntities;
use IPub\DoctrineCrud\Mapping\Annotation as IPubDoctrine;
use IPub\DoctrineTimestampable;
use Ramsey\Uuid;
use Throwable;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="fb_acl_privileges",
 *     options={
 *       "collate"="utf8mb4_general_ci",
 *       "charset"="utf8mb4",
 *       "comment"="ACL privileges"
 *     },
 *     uniqueConstraints={
 *       @ORM\UniqueConstraint(name="privilege_name_unique", columns={"privilege_name"})
 *     }
 * )
 */
class Privilege extends NodeDatabaseEntities\Entity implements IPrivilege
{

	use DoctrineTimestampable\Entities\TEntityCreated;
	use DoctrineTimestampable\Entities\TEntityUpdated;

	/**
	 * @var Uuid\UuidInterface
	 *
	 * @ORM\Id
	 * @ORM\Column(type="uuid_binary", name="privilege_id")
	 * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
	 */
	protected $id;

	/**
	 * @var string
	 *
	 * @IPubDoctrine\Crud(is={"required", "writable"})
	 * @ORM\Column(type="string", name="privilege_name", length=100, nullable=false)
	 */
	private $name;

	/**
	 * @var string|null
	 *
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\Column(type="text", name="privilege_comment", nullable=true, options={"default": null})
	 */
	private $comment = null;

	/**
	 * @var Entities\Resources\IResource
	 *
	 * @ORM\ManyToOne(targetEntity="FastyBird\AuthNode\Entities\Resources\Resource", inversedBy="privileges")
	 * @ORM\JoinColumn(name="resource_id", referencedColumnName="resource_id", onDelete="cascade", nullable=false)
	 */
	private $resource;

	/**
	 * @param Entities\Resources\IResource $resource
	 * @param string $name
	 *
	 * @throws Throwable
	 */
	public function __construct(
		Entities\Resources\IResource $resource,
		string $name
	) {
		$this->id = Uuid\Uuid::uuid4();

		$this->resource = $resource;
		$this->name = $name;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getPrivilegeId(): string
	{
		return $this->getName();
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
	public function getName(): string
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
	public function getResource(): Entities\Resources\IResource
	{
		return $this->resource;
	}

	/**
	 * @return string
	 */
	public function __toString(): string
	{
		return $this->name;
	}

}
