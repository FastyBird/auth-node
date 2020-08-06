<?php declare(strict_types = 1);

/**
 * IUserAccountIdentity.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Entities
 * @since          0.1.0
 *
 * @date           10.06.20
 */

namespace FastyBird\AuthNode\Entities\Identities;

use FastyBird\AuthNode\Entities;
use FastyBird\AuthNode\Helpers;

/**
 * System identity entity interface
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IUserAccountIdentity extends IIdentity
{

	/**
	 * @param string|Helpers\Password $password
	 *
	 * @return void
	 */
	public function setPassword($password): void;

	/**
	 * @return Helpers\Password
	 */
	public function getPassword(): Helpers\Password;

	/**
	 * @param string $rawPassword
	 *
	 * @return bool
	 */
	public function verifyPassword(string $rawPassword): bool;

	/**
	 * @param string $salt
	 *
	 * @return void
	 */
	public function setSalt(string $salt): void;

	/**
	 * @return string|null
	 */
	public function getSalt(): ?string;

}
