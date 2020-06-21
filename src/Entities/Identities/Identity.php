<?php declare(strict_types = 1);

/**
 * Identity.php
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

namespace FastyBird\AuthNode\Entities\Identities;

use Consistence\Doctrine\Enum\EnumAnnotation as Enum;
use Doctrine\ORM\Mapping as ORM;
use FastyBird\AuthNode\Entities;
use FastyBird\AuthNode\Types;
use FastyBird\NodeDatabase\Entities as NodeDatabaseEntities;
use IPub\DoctrineCrud\Mapping\Annotation as IPubDoctrine;
use IPub\DoctrineTimestampable;
use Nette\Utils;
use Ramsey\Uuid;
use Throwable;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="fb_identities",
 *     options={
 *       "collate"="utf8mb4_general_ci",
 *       "charset"="utf8mb4",
 *       "comment"="Accounts identities"
 *     },
 *     indexes={
 *       @ORM\Index(name="identity_uid_idx", columns={"identity_uid"}),
 *       @ORM\Index(name="identity_status_idx", columns={"identity_status"})
 *     }
 * )
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="identity_type", type="string", length=15)
 * @ORM\DiscriminatorMap({
 *      "identity"   = "FastyBird\AuthNode\Entities\Identities\Identity",
 *      "user"       = "FastyBird\AuthNode\Entities\Identities\UserAccountIdentity",
 *      "machine"    = "FastyBird\AuthNode\Entities\Identities\MachineAccountIdentity"
 * })
 * @ORM\MappedSuperclass
 */
abstract class Identity extends NodeDatabaseEntities\Entity implements IIdentity
{

	use NodeDatabaseEntities\TEntityParams;
	use DoctrineTimestampable\Entities\TEntityCreated;
	use DoctrineTimestampable\Entities\TEntityUpdated;

	/**
	 * @var Uuid\UuidInterface
	 *
	 * @ORM\Id
	 * @ORM\Column(type="uuid_binary", name="identity_id")
	 * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
	 */
	protected $id;

	/**
	 * @var Entities\Accounts\IAccount
	 *
	 * @ORM\ManyToOne(targetEntity="FastyBird\AuthNode\Entities\Accounts\Account", inversedBy="identities", cascade={"persist", "remove"})
	 * @ORM\JoinColumn(name="account_id", referencedColumnName="account_id", onDelete="cascade", nullable=false)
	 */
	protected $account;

	/**
	 * @var string
	 *
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\Column(type="string", name="identity_uid", length=50, nullable=false)
	 */
	protected $uid;

	/**
	 * @var Types\IdentityStatusType
	 *
	 * @Enum(class=Types\IdentityStatusType::class)
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\Column(type="string_enum", name="identity_status", nullable=false, options={"default": "active"})
	 */
	protected $status;

	/**
	 * @param Entities\Accounts\IAccount $account
	 * @param string $uid
	 *
	 * @throws Throwable
	 */
	public function __construct(
		Entities\Accounts\IAccount $account,
		string $uid
	) {
		$this->id = Uuid\Uuid::uuid4();

		$this->account = $account;
		$this->uid = $uid;

		$this->status = Types\IdentityStatusType::get(Types\IdentityStatusType::STATE_ACTIVE);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getAccount(): Entities\Accounts\IAccount
	{
		return $this->account;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getUid(): string
	{
		return $this->uid;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setStatus(Types\IdentityStatusType $status): void
	{
		$this->status = $status;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getStatus(): Types\IdentityStatusType
	{
		return $this->status;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isActive(): bool
	{
		return $this->status === Types\IdentityStatusType::get(Types\IdentityStatusType::STATE_ACTIVE);
	}

	/**
	 * {@inheritDoc}
	 */
	public function isBlocked(): bool
	{
		return $this->status === Types\IdentityStatusType::get(Types\IdentityStatusType::STATE_BLOCKED);
	}

	/**
	 * {@inheritDoc}
	 */
	public function isDeleted(): bool
	{
		return $this->status === Types\IdentityStatusType::get(Types\IdentityStatusType::STATE_DELETED);
	}

	/**
	 * {@inheritDoc}
	 */
	public function isInvalid(): bool
	{
		return $this->status === Types\IdentityStatusType::get(Types\IdentityStatusType::STATE_INVALID);
	}

	/**
	 * @return Entities\Roles\IRole[]
	 */
	public function getRoles(): array
	{
		return $this->account->getRoles();
	}

	/**
	 * {@inheritDoc}
	 */
	public function invalidate(): void
	{
		$this->status = Types\IdentityStatusType::get(Types\IdentityStatusType::STATE_INVALID);
	}

	/**
	 * @return void
	 *
	 * @throws Throwable
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
	 */
	public function __clone()
	{
		$this->id = Uuid\Uuid::uuid4();
		$this->createdAt = new Utils\DateTime();
		$this->status = Types\IdentityStatusType::get(Types\IdentityStatusType::STATE_ACTIVE);
	}

}
