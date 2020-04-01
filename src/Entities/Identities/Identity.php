<?php declare(strict_types = 1);

/**
 * Identity.php
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

namespace FastyBird\AccountsNode\Entities\Identities;

use Consistence\Doctrine\Enum\EnumAnnotation as Enum;
use Doctrine\ORM\Mapping as ORM;
use FastyBird\AccountsNode\Entities;
use FastyBird\AccountsNode\Types;
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
 *       @ORM\Index(name="identity_email_idx", columns={"identity_email"}),
 *       @ORM\Index(name="identity_status_idx", columns={"identity_status"})
 *     }
 * )
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="identity_type", type="string", length=15)
 * @ORM\MappedSuperclass
 */
abstract class Identity extends Entities\Entity implements IIdentity
{

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
	 * @ORM\ManyToOne(targetEntity="FastyBird\AccountsNode\Entities\Accounts\Account", inversedBy="identities", cascade={"persist", "remove"})
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
	 * @var string|null
	 *
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\Column(type="string", name="identity_email", length=100, nullable=true)
	 */
	protected $email = null;

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
	 * @param string|null $email
	 *
	 * @throws Throwable
	 */
	public function __construct(
		Entities\Accounts\IAccount $account,
		string $uid,
		?string $email = null
	) {
		$this->id = Uuid\Uuid::uuid4();

		$this->account = $account;
		$this->uid = $uid;
		$this->email = $email;

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
	public function setEmail(?string $email = null): void
	{
		$this->email = $email;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getEmail(): ?string
	{
		return $this->email;
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
