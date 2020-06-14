<?php declare(strict_types = 1);

/**
 * Rule.php
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

namespace FastyBird\AuthNode\Entities\Rules;

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
 *     name="fb_acl_rules",
 *     options={
 *       "collate"="utf8mb4_general_ci",
 *       "charset"="utf8mb4",
 *       "comment"="ACL roles & resources mappings"
 *     },
 *     uniqueConstraints={
 *       @ORM\UniqueConstraint(name="role_rule_unique", columns={"role_id", "resource_id", "privilege_id"}),
 *     }
 * )
 */
class Rule extends NodeDatabaseEntities\Entity implements IRule
{

	use DoctrineTimestampable\Entities\TEntityCreated;
	use DoctrineTimestampable\Entities\TEntityUpdated;

	/**
	 * @var Uuid\UuidInterface
	 *
	 * @ORM\Id
	 * @ORM\Column(type="uuid_binary", name="rule_id")
	 * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
	 */
	protected $id;

	/**
	 * @var bool
	 *
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\Column(type="boolean", name="access", nullable=false, options={"default": true})
	 */
	private $access = self::ALLOWED;

	/**
	 * @var Entities\Roles\IRole
	 *
	 * @IPubDoctrine\Crud(is="required")
	 * @ORM\ManyToOne(targetEntity="FastyBird\AuthNode\Entities\Roles\Role", inversedBy="rules")
	 * @ORM\JoinColumn(name="role_id", referencedColumnName="role_id", onDelete="cascade")
	 */
	private $role;

	/**
	 * @var Entities\Privileges\IPrivilege
	 *
	 * @IPubDoctrine\Crud(is="required")
	 * @ORM\ManyToOne(targetEntity="FastyBird\AuthNode\Entities\Privileges\Privilege")
	 * @ORM\JoinColumn(name="privilege_id", referencedColumnName="privilege_id", onDelete="cascade")
	 */
	private $privilege;

	/**
	 * @param Entities\Roles\IRole $role
	 * @param Entities\Privileges\IPrivilege $privilege
	 * @param bool $access
	 * @param Uuid\UuidInterface|null $id
	 *
	 * @throws Throwable
	 */
	public function __construct(
		Entities\Roles\IRole $role,
		Entities\Privileges\IPrivilege $privilege,
		bool $access,
		?Uuid\UuidInterface $id = null
	) {
		$this->id = $id ?? Uuid\Uuid::uuid4();

		$this->role = $role;
		$this->privilege = $privilege;

		$this->access = $access;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setAccess(bool $access): void
	{
		$this->access = $access ? self::ALLOWED : self::DISALLOWED;
	}

	/**
	 * {@inheritDoc}
	 */
	public function hasAccess(): bool
	{
		return $this->access === self::ALLOWED;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getRole(): Entities\Roles\IRole
	{
		return $this->role;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getResource(): Entities\Resources\IResource
	{
		return $this->privilege->getResource();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getPrivilege(): Entities\Privileges\IPrivilege
	{
		return $this->privilege;
	}

}
