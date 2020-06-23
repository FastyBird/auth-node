<?php declare(strict_types = 1);

/**
 * TokenBuilder.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Security
 * @since          0.1.0
 *
 * @date           31.03.20
 */

namespace FastyBird\AuthNode\Security;

use DateTimeInterface;
use FastyBird\AuthNode\Entities;
use FastyBird\AuthNode\Exceptions;
use FastyBird\AuthNode\Security;
use FastyBird\NodeLibs\Helpers as NodeLibsHelpers;
use Lcobucci\JWT;
use Nette;

/**
 * JW token builder
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Security
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class TokenBuilder
{

	use Nette\SmartObject;

	public const TOKEN_TYPE_ACCESS = 'access';
	public const TOKEN_TYPE_REFRESH = 'refresh';

	/** @var string */
	private $tokenSignature;

	/** @var Security\User */
	private $user;

	/** @var JWT\Signer */
	private $signer;

	/** @var NodeLibsHelpers\IDateFactory */
	private $dateTimeFactory;

	public function __construct(
		string $tokenSignature,
		Security\User $user,
		JWT\Signer $signer,
		NodeLibsHelpers\IDateFactory $dateTimeFactory
	) {
		$this->tokenSignature = $tokenSignature;

		$this->user = $user;

		$this->signer = $signer;
		$this->dateTimeFactory = $dateTimeFactory;
	}

	/**
	 * @param string $id
	 * @param string $type
	 * @param DateTimeInterface|null $expirationTime
	 *
	 * @return JWT\Token
	 */
	public function build(
		string $id,
		string $type,
		?DateTimeInterface $expirationTime = null
	): JWT\Token {
		if (!in_array($type, [self::TOKEN_TYPE_ACCESS, self::TOKEN_TYPE_REFRESH], true)) {
			throw new Exceptions\InvalidStateException('Provided token type is not valid type.');
		}

		$timestamp = $this->dateTimeFactory->getNow()->getTimestamp();

		$jwtBuilder = new JWT\Builder();
		$jwtBuilder->issuedAt($timestamp);

		if ($expirationTime !== null) {
			$jwtBuilder->expiresAt($expirationTime->getTimestamp());
		}

		$jwtBuilder->identifiedBy($id);

		if ($this->user->getId() !== null) {
			$jwtBuilder->relatedTo($this->user->getId());
		}

		$jwtBuilder->withClaim('type', $type);

		if ($type === self::TOKEN_TYPE_ACCESS) {
			$jwtBuilder->withClaim('roles', array_map(function (Entities\Roles\IRole $role): string {
				return $role->getRoleId();
			}, $this->user->getRoles()));
		}

		return $jwtBuilder->getToken($this->signer, new JWT\Signer\Key($this->tokenSignature));
	}

}
