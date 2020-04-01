<?php declare(strict_types = 1);

/**
 * RefreshToken.php
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

namespace FastyBird\AccountsNode\Entities\Tokens;

use Doctrine\ORM\Mapping as ORM;
use FastyBird\AccountsNode\Exceptions;
use Throwable;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="fb_security_tokens_refresh",
 *     options={
 *       "collate"="utf8mb4_general_ci",
 *       "charset"="utf8mb4",
 *       "comment"="Account refresh tokens"
 *     }
 * )
 */
class RefreshToken extends Token implements IRefreshToken
{

	/**
	 * @param IAccessToken $accessToken
	 * @param string $token
	 *
	 * @throws Throwable
	 */
	public function __construct(
		IAccessToken $accessToken,
		string $token
	) {
		parent::__construct($token);

		$this->setParent($accessToken);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getAccessToken(): IAccessToken
	{
		$token = parent::getParent();

		if (!$token instanceof IAccessToken) {
			throw new Exceptions\InvalidStateException(
				sprintf(
					'Access token for refresh token is not valid type. Instance of %s expected, %s provided',
					IAccessToken::class,
					$token !== null ? get_class($token) : 'null'
				)
			);
		}

		return $token;
	}

}
