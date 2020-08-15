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
	 * @return string
	 */
	public function getDevice(): string;

	/**
	 * @param Entities\Accounts\IMachineAccount $account
	 *
	 * @return void
	 */
	public function setParent(Entities\Accounts\IMachineAccount $account): void;

	/**
	 * @return Entities\Accounts\IMachineAccount|null
	 */
	public function getParent(): ?Entities\Accounts\IMachineAccount;

	/**
	 * @return void
	 */
	public function removeParent(): void;

	/**
	 * @param Entities\Accounts\IMachineAccount[] $children
	 *
	 * @return void
	 */
	public function setChildren(array $children): void;

	/**
	 * @param Entities\Accounts\IMachineAccount $child
	 *
	 * @return void
	 */
	public function addChild(Entities\Accounts\IMachineAccount $child): void;

	/**
	 * @return Entities\Accounts\IMachineAccount[]
	 */
	public function getChildren(): array;

	/**
	 * @param Entities\Accounts\IMachineAccount $child
	 *
	 * @return void
	 */
	public function removeChild(Entities\Accounts\IMachineAccount $child): void;

}
