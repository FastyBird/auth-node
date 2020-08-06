<?php declare(strict_types = 1);

/**
 * IdentityFactory.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Security
 * @since          0.1.0
 *
 * @date           15.07.20
 */

namespace FastyBird\AuthNode\Security;

use FastyBird\AuthNode\Entities;
use FastyBird\NodeAuth\Models as NodeAuthModels;
use FastyBird\NodeAuth\Queries as NodeAuthQueries;
use FastyBird\NodeAuth\Security as NodeAuthSecurity;
use Lcobucci\JWT;
use Nette\Security as NS;

/**
 * Application identity factory
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Security
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class IdentityFactory implements NodeAuthSecurity\IIdentityFactory
{

	/** @var NodeAuthModels\Tokens\ITokenRepository */
	private $tokenRepository;

	public function __construct(
		NodeAuthModels\Tokens\ITokenRepository $tokenRepository
	) {
		$this->tokenRepository = $tokenRepository;
	}

	/**
	 * {@inheritDoc}
	 */
	public function create(JWT\Token $token): ?NS\IIdentity
	{
		$findToken = new NodeAuthQueries\FindTokensQuery();
		$findToken->byToken((string) $token);

		$accessToken = $this->tokenRepository->findOneBy($findToken, Entities\Tokens\AccessToken::class);

		if ($accessToken instanceof Entities\Tokens\IAccessToken) {
			return $accessToken->getIdentity();
		}

		return null;
	}

}
