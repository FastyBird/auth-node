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
	 * @return Entities\Tokens\IAccessToken|null
	 */
	public function read(): ?Entities\Tokens\IAccessToken
	{
		// HEADERS
		if (function_exists('apache_request_headers')) {
			$headers = apache_request_headers();

		} else {
			$headers = [];

			foreach ($_SERVER as $k => $v) {
				if (strncmp($k, 'HTTP_', 5) === 0) {
					$k = substr($k, 5);

				} elseif (strncmp($k, 'CONTENT_', 8)) {
					continue;
				}

				$headers[strtr($k, '_', '-')] = $v;
			}
		}

		$headers = array_change_key_case((array) $headers, CASE_LOWER);

		/** @var string|null $headerJWT */
		$headerJWT = $headers[self::TOKEN_HEADER_NAME] ?? null;

		if ($headerJWT !== null && preg_match(self::TOKEN_HEADER_REGEXP, $headerJWT, $matches)) {
			/** @var Entities\Tokens\IAccessToken|null $token */
			$token = $this->tokenRepository->findOneByToken($matches[1], Entities\Tokens\AccessToken::class);

			return $token;
		}

		return null;
	}

}
