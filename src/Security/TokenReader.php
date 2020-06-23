<?php declare(strict_types = 1);

/**
 * TokenReader.php
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

use FastyBird\AuthNode\Entities;
use FastyBird\AuthNode\Models;
use FastyBird\NodeLibs\Helpers as NodeLibsHelpers;
use Lcobucci\JWT;
use Nette;
use Psr\Http\Message\ServerRequestInterface;

/**
 * JW token header reader
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Security
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class TokenReader
{

	use Nette\SmartObject;

	public const TOKEN_HEADER_NAME = 'authorization';
	private const TOKEN_HEADER_REGEXP = '/Bearer\s+(.*)$/i';

	/** @var Models\Tokens\ITokenRepository */
	private $tokenRepository;

	/** @var NodeLibsHelpers\IDateFactory */
	private $dateTimeFactory;

	public function __construct(
		Models\Tokens\ITokenRepository $tokenRepository,
		NodeLibsHelpers\IDateFactory $dateTimeFactory
	) {
		$this->tokenRepository = $tokenRepository;

		$this->dateTimeFactory = $dateTimeFactory;
	}

	/**
	 * @param ServerRequestInterface $request
	 *
	 * @return Entities\Tokens\IAccessToken|null
	 */
	public function read(ServerRequestInterface $request): ?Entities\Tokens\IAccessToken
	{
		$headerJWT = $request->hasHeader(self::TOKEN_HEADER_NAME) ? $request->getHeader(self::TOKEN_HEADER_NAME) : null;
		$headerJWT = is_array($headerJWT) ? reset($headerJWT) : $headerJWT;

		if (is_string($headerJWT) && preg_match(self::TOKEN_HEADER_REGEXP, $headerJWT, $matches) !== false) {
			/** @var Entities\Tokens\IAccessToken|null $accessToken */
			$accessToken = $this->tokenRepository->findOneByToken($matches[1], Entities\Tokens\AccessToken::class);

			if ($accessToken !== null) {
				$jwtParser = new JWT\Parser();

				$token = $jwtParser->parse($accessToken->getToken());

				$validationData = new JWT\ValidationData($this->dateTimeFactory->getNow()->getTimestamp());
				$validationData->setId($accessToken->getPlainId());

				$validationData->setSubject($accessToken->getIdentity()->getAccount()->getPlainId());

				if ($token->validate($validationData)) {
					return $accessToken;
				}
			}

			return null;
		}

		return null;
	}

}
