<?php declare(strict_types = 1);

/**
 * IRefreshToken.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Entities
 * @since          0.1.0
 *
 * @date           30.03.20
 */

namespace FastyBird\AuthNode\Entities\Tokens;

/**
 * Security refresh token entity interface
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IRefreshToken extends IToken
{

	public const TOKEN_EXPIRATION = '+3 days';

	/**
	 * @return IAccessToken
	 */
	public function getAccessToken(): IAccessToken;

}
