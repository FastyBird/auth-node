<?php declare(strict_types = 1);

/**
 * AccountStatusType.php
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
 * Doctrine2 DB type for account status column
 *
 * @package        FastyBird:AccountsNode!
 * @subpackage     Types
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class AccountStatusType extends Consistence\Enum\Enum
{

	/**
	 * Define states
	 */
	public const STATE_ACTIVATED = 'activated';
	public const STATE_BLOCKED = 'blocked';
	public const STATE_DELETED = 'deleted';
	public const STATE_NOT_ACTIVATED = 'notActivated';
	public const STATE_APPROVAL_WAITING = 'approvalWaiting';

	/**
	 * @return string
	 */
	public function __toString(): string
	{
		return (string) self::getValue();
	}

	/**
	 * List of allowed statuses
	 *
	 * @var string[]
	 */
	public static $allowedStates = [
		self::STATE_ACTIVATED,
		self::STATE_BLOCKED,
		self::STATE_DELETED,
	];

}
