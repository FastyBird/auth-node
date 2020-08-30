<?php declare(strict_types = 1);

/**
 * AccessToken.php
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

namespace FastyBird\AuthNode\Entities\Tokens;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use FastyBird\AuthNode\Entities;
use FastyBird\NodeAuth\Entities as NodeAuthEntities;
use FastyBird\NodeDatabase\Entities as NodeDatabaseEntities;
use IPub\DoctrineCrud\Mapping\Annotation as IPubDoctrine;
use IPub\DoctrineTimestampable;
use Ramsey\Uuid;
use Throwable;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="fb_security_tokens_access",
 *     options={
 *       "collate"="utf8mb4_general_ci",
 *       "charset"="utf8mb4",
 *       "comment"="Account access tokens"
 *     }
 * )
 */
class AccessToken extends NodeAuthEntities\Tokens\Token implements IAccessToken
{

	use NodeDatabaseEntities\TEntity;
	use NodeDatabaseEntities\TEntityParams;
	use DoctrineTimestampable\Entities\TEntityCreated;
	use DoctrineTimestampable\Entities\TEntityUpdated;

	/**
	 * @var Entities\Identities\IIdentity
	 *
	 * @IPubDoctrine\Crud(is="required")
	 * @ORM\ManyToOne(targetEntity="FastyBird\AuthNode\Entities\Identities\Identity")
	 * @ORM\JoinColumn(name="identity_id", referencedColumnName="identity_id", onDelete="cascade", nullable=false)
	 */
	private $identity;

	/**
	 * @var DateTimeInterface|null
	 *
	 * @IPubDoctrine\Crud(is={"writable"})
	 * @ORM\Column(name="token_valid_till", type="datetime", nullable=true)
	 */
	private $validTill;

	/**
	 * @param Entities\Identities\IIdentity $identity
	 * @param string $token
	 * @param DateTimeInterface|null $validTill
	 * @param Uuid\UuidInterface|null $id
	 *
	 * @throws Throwable
	 */
	public function __construct(
		Entities\Identities\IIdentity $identity,
		string $token,
		?DateTimeInterface $validTill,
		?Uuid\UuidInterface $id = null
	) {
		parent::__construct($token, $id);

		$this->identity = $identity;
		$this->validTill = $validTill;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setRefreshToken(IRefreshToken $refreshToken): void
	{
		parent::addChild($refreshToken);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getRefreshToken(): ?IRefreshToken
	{
		$token = $this->children->first();

		if ($token instanceof IRefreshToken) {
			return $token;
		}

		return null;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getIdentity(): Entities\Identities\IIdentity
	{
		return $this->identity;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getValidTill(): ?DateTimeInterface
	{
		return $this->validTill;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isValid(DateTimeInterface $dateTime): bool
	{
		if ($this->validTill === null) {
			return true;
		}

		return $this->validTill >= $dateTime;
	}

}
