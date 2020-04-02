<?php declare(strict_types = 1);

/**
 * TokenReader.php
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

use FastyBird\AccountsNode\Entities;
use FastyBird\AccountsNode\Models;
use Nette;
use Psr\Http\Message\ServerRequestInterface;

/**
 * JW token header reader
 *
 * @package        FastyBird:AccountsNode!
 * @subpackage     Security
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class TokenReader
{

	use Nette\SmartObject;

	private const TOKEN_HEADER_NAME = 'authorization';
	private const TOKEN_HEADER_REGEXP = '/Bearer\s+(.*)$/i';

	/** @var Models\Tokens\ITokenRepository */
	private $tokenRepository;

	public function __construct(
		Models\Tokens\ITokenRepository $tokenRepository
	) {
		$this->tokenRepository = $tokenRepository;
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

		if (is_string($headerJWT) && preg_match(self::TOKEN_HEADER_REGEXP, $headerJWT, $matches)) {
			/** @var Entities\Tokens\IAccessToken|null $token */
			$token = $this->tokenRepository->findOneByToken($matches[1], Entities\Tokens\AccessToken::class);

			return $token;
		}

		return null;
	}

}
