<?php declare(strict_types = 1);

/**
 * EmailVisibilityType.php
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

namespace FastyBird\AuthNode\Types;

use Consistence;

/**
 * Doctrine2 DB type for email visibility type column
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Types
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class EmailVisibilityType extends Consistence\Enum\Enum
{

	/**
	 * Define states
	 */
	public const VISIBILITY_PUBLIC = 'public';
	public const VISIBILITY_PRIVATE = 'private';

	/**
	 * @return string
	 */
	public function __toString(): string
	{
		return (string) self::getValue();
	}

}
