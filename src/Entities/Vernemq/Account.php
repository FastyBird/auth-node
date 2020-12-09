<?php declare(strict_types = 1);

/**
 * Account.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Entities
 * @since          0.1.0
 *
 * @date           13.06.20
 */

namespace FastyBird\AuthNode\Entities\Vernemq;

use FastyBird\AuthModule\Entities as AuthModuleEntities;
use FastyBird\Database\Entities as DatabaseEntities;
use IPub\DoctrineTimestampable;
use Ramsey\Uuid;
use stdClass;
use Throwable;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="vmq_auth_acl",
 *     options={
 *       "collate"="utf8mb4_general_ci",
 *       "charset"="utf8mb4",
 *       "comment"="VerneMQ ACL"
 *     },
 *     uniqueConstraints={
 *       @ORM\UniqueConstraint(name="account_unique", columns={"mountpoint", "client_id", "username"})
 *     }
 * )
 */
class Account implements IAccount
{

	use DatabaseEntities\TEntity;
	use DoctrineTimestampable\Entities\TEntityCreated;
	use DoctrineTimestampable\Entities\TEntityUpdated;

	/**
	 * @var Uuid\UuidInterface
	 *
	 * @ORM\Id
	 * @ORM\Column(type="uuid_binary", name="id")
	 * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
	 */
	protected $id;

	/**
	 * @var AuthModuleEntities\Identities\IIdentity|null
	 *
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\ManyToOne(targetEntity="FastyBird\AuthModule\Entities\Identities\Identity")
	 * @ORM\JoinColumn(name="identity_id", referencedColumnName="identity_id", onDelete="cascade", nullable=true)
	 */
	private $identity;

	/**
	 * @var string
	 *
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\Column(type="string", name="mountpoint", length=10, nullable=false, options={"default": ""})
	 */
	private $mountpoint;

	/**
	 * @var string
	 *
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\Column(type="string", name="client_id", length=128, nullable=false, options={"default": ""})
	 */
	private $clientId;

	/**
	 * @var string
	 *
	 * @IPubDoctrine\Crud(is={"required", "writable"})
	 * @ORM\Column(type="string", name="username", length=128, nullable=false)
	 */
	private $username;

	/**
	 * @var string
	 *
	 * @IPubDoctrine\Crud(is={"required", "writable"})
	 * @ORM\Column(type="string", name="password", length=128, nullable=false)
	 */
	private $password;

	/**
	 * @var mixed[]
	 *
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\Column(type="json", name="publish_acl", nullable=true)
	 */
	private $publishAcl = [];

	/**
	 * @var mixed[]
	 *
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\Column(type="json", name="subscribe_acl", nullable=true)
	 */
	private $subscribeAcl = [];

	/**
	 * @param string $username
	 * @param string $password
	 * @param AuthModuleEntities\Identities\IIdentity|null $identity
	 *
	 * @throws Throwable
	 */
	public function __construct(
		string $username,
		string $password,
		?AuthModuleEntities\Identities\IIdentity $identity = null
	) {
		$this->id = Uuid\Uuid::uuid4();

		$this->username = $username;
		$this->setPassword($password);

		$this->identity = $identity;

		// Fill defaults
		$this->mountpoint = '';
		$this->clientId = '';
	}

	/**
	 * {@inheritDoc}
	 */
	public function getIdentity(): ?AuthModuleEntities\Identities\IIdentity
	{
		return $this->identity;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getMountpoint(): string
	{
		return $this->mountpoint;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setMountpoint(string $mountpoint): void
	{
		$this->mountpoint = $mountpoint;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getClientId(): string
	{
		return $this->clientId;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setClientId(string $clientId): void
	{
		$this->clientId = $clientId;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getUsername(): string
	{
		return $this->username;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setUsername(string $username): void
	{
		$this->username = $username;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getPassword(): string
	{
		return $this->password;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setPassword(string $password): void
	{
		$this->password = hash('sha256', $password, false);
	}

	/**
	 * {@inheritDoc}
	 */
	public function addPublishAcl(string $pattern): void
	{
		$rule = new stdClass();
		$rule->pattern = $pattern;

		$this->publishAcl[] = $rule;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getPublishAcl(): array
	{
		return $this->publishAcl;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setPublishAcl(array $publishAcl): void
	{
		$this->publishAcl = [];

		foreach ($publishAcl as $pattern) {
			$rule = new stdClass();
			$rule->pattern = $pattern;

			$this->publishAcl[] = $rule;
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function addSubscribeAcl(string $pattern): void
	{
		$rule = new stdClass();
		$rule->pattern = $pattern;

		$this->subscribeAcl[] = $rule;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getSubscribeAcl(): array
	{
		return $this->subscribeAcl;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setSubscribeAcl(array $subscribeAcl): void
	{
		$this->subscribeAcl = [];

		foreach ($subscribeAcl as $pattern) {
			$rule = new stdClass();
			$rule->pattern = $pattern;

			$this->subscribeAcl[] = $rule;
		}
	}

}
