<?php declare(strict_types = 1);

/**
 * TokenBuilder.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AccountsNode!
 * @subpackage     Security
 * @since          0.1.0
 *
 * @date           31.03.20
 */

namespace FastyBird\AccountsNode\Security;

use DateTimeInterface;
use FastyBird\NodeLibs\Helpers as NodeLibsHelpers;
use Lcobucci\JWT;
use Nette;
use Ramsey\Uuid;
use Throwable;

/**
 * JW token builder
 *
 * @package        FastyBird:AccountsNode!
 * @subpackage     Security
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class TokenBuilder
{

	use Nette\SmartObject;

	/** @var string */
	private $tokenSignature;

	/** @var JWT\Signer */
	private $signer;

	/** @var NodeLibsHelpers\DateFactory */
	private $dateTimeFactory;

	public function __construct(
		string $tokenSignature,
		JWT\Signer $signer,
		NodeLibsHelpers\DateFactory $dateTimeFactory
	) {
		$this->tokenSignature = $tokenSignature;

		$this->signer = $signer;
		$this->dateTimeFactory = $dateTimeFactory;
	}

	/**
	 * @param DateTimeInterface|null $expirationTime
	 *
	 * @return string
	 */
	public function build(?DateTimeInterface $expirationTime = null): string
	{
		$timestamp = $this->dateTimeFactory->getNow()->getTimestamp();

		try {
			$tokenId = Uuid\Uuid::uuid4()->toString();

		} catch (Throwable $ex) {
			$tokenId = $this->generateRandomString();
		}

		$jwtBuilder = new JWT\Builder();
		$jwtBuilder->issuedAt($timestamp);

		if ($expirationTime !== null) {
			$jwtBuilder->expiresAt($expirationTime->getTimestamp());
		}

		$jwtBuilder->identifiedBy($tokenId);

		return (string) $jwtBuilder->getToken($this->signer, new JWT\Signer\Key($this->tokenSignature));
	}

	/**
	 * @param int $length
	 *
	 * @return string
	 */
	private function generateRandomString(
		int $length = 10
	): string {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

		$charactersLength = strlen($characters);

		$randomString = '';

		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}

		return $randomString;
	}

}
