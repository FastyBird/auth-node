<?php declare(strict_types = 1);

/**
 * Privilege.php
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

namespace FastyBird\AccountsNode\Entities\Privileges;

use Doctrine\ORM\Mapping as ORM;
use FastyBird\AccountsNode\Entities;
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
 *     indexes={
 *       @ORM\Index(name="privilege_key_name_idx", columns={"privilege_key_name"})
 *     }
 * )
 */
class Privilege extends Entities\Entity implements IPrivilege
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
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\Column(type="string", name="privilege_key_name", length=100, nullable=false)
	 */
	private $keyName;

	/**
	 * @var string
	 *
	 * @IPubDoctrine\Crud(is="writable")
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
	 * @ORM\ManyToOne(targetEntity="FastyBird\AccountsNode\Entities\Resources\Resource", inversedBy="privileges")
	 * @ORM\JoinColumn(name="resource_id", referencedColumnName="resource_id", onDelete="cascade", nullable=false)
	 */
	private $resource;

	/**
	 * @param Entities\Resources\IResource $resource
	 * @param string $keyName
	 * @param string $name
	 *
	 * @throws Throwable
	 */
	public function __construct(
		Entities\Resources\IResource $resource,
		string $keyName,
		string $name
	) {
		$this->id = Uuid\Uuid::uuid4();

		$this->resource = $resource;
		$this->keyName = $keyName;
		$this->name = $name;
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
	public function getPrivilegeId(): string
	{
		return $this->keyName;
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
		return $this->keyName;
	}

}
