<?php declare(strict_types = 1);

/**
 * IAccessToken.php
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

use FastyBird\AccountsNode\Entities;

/**
 * Account profile access token entity interface
 *
 * @package        FastyBird:AccountsNode!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IAccessToken extends IToken
{

	public const TOKEN_EXPIRATION = '+6 hours';

	/**
	 * @param IRefreshToken $refreshToken
	 *
	 * @return void
	 */
	public function setRefreshToken(IRefreshToken $refreshToken): void;

	/**
	 * @return IRefreshToken|null
	 */
	public function getRefreshToken(): ?IRefreshToken;

	/**
	 * @return Entities\Identities\IIdentity
	 */
	public function getIdentity(): Entities\Identities\IIdentity;

}
