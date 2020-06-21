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
	 * @param string $id
	 * @param DateTimeInterface|null $expirationTime
	 *
	 * @return string
	 */
	public function build(string $id, ?DateTimeInterface $expirationTime = null): string
	{
		$timestamp = $this->dateTimeFactory->getNow()->getTimestamp();

		$jwtBuilder = new JWT\Builder();
		$jwtBuilder->issuedAt($timestamp);

		if ($expirationTime !== null) {
			$jwtBuilder->expiresAt($expirationTime->getTimestamp());
		}

		$jwtBuilder->identifiedBy($id);

		return (string) $jwtBuilder->getToken($this->signer, new JWT\Signer\Key($this->tokenSignature));
	}

}
