<?php declare(strict_types = 1);

/**
 * IdentityStatusType.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Entities
 * @since          0.1.0
 *
 * @date           30.03.20
 */

namespace FastyBird\AuthNode\Types;

use Consistence;

/**
 * Doctrine2 DB type for identity status column
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Types
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class IdentityStatusType extends Consistence\Enum\Enum
{

	/**
	 * Define statuses
	 */
	public const STATE_ACTIVE = 'active';
	public const STATE_BLOCKED = 'blocked';
	public const STATE_DELETED = 'deleted';
	public const STATE_INVALID = 'invalid';

	/**
	 * @return string
	 */
	public function __toString(): string
	{
		return (string) self::getValue();
	}

}
