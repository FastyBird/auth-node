<?php declare(strict_types = 1);

/**
 * IMachineAccount.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Entities
 * @since          0.1.0
 *
 * @date           11.06.20
 */

namespace FastyBird\AuthNode\Entities\Accounts;

use FastyBird\AuthNode\Entities;

/**
 * Machine account entity interface
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IMachineAccount extends IAccount
{

	/**
	 * @return Entities\Accounts\IUserAccount
	 */
	public function getOwner(): IUserAccount;

	/**
	 * @return string
	 */
	public function getDevice(): string;

}
