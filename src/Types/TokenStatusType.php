<?php declare(strict_types = 1);

/**
 * TokenStatusType.php
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

namespace FastyBird\AccountsNode\Types;

use Consistence;

/**
 * Doctrine2 DB type for token status column
 *
 * @package        FastyBird:AccountsNode!
 * @subpackage     Types
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class TokenStatusType extends Consistence\Enum\Enum
{

	/**
	 * Define statuses
	 */
	public const STATE_ACTIVE = 'active';
	public const STATE_BLOCKED = 'blocked';
	public const STATE_DELETED = 'deleted';
	public const STATE_EXPIRED = 'expired';
	public const STATE_REVOKED = 'revoked';

	/**
	 * @return string
	 */
	public function __toString(): string
	{
		return (string) self::getValue();
	}

}
