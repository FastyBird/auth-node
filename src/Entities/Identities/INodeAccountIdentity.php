<?php declare(strict_types = 1);

/**
 * INodeAccountIdentity.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Entities
 * @since          0.1.0
 *
 * @date           26.06.20
 */

namespace FastyBird\AuthNode\Entities\Identities;

/**
 * Node account identity entity interface
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface INodeAccountIdentity extends IIdentity
{

	/**
	 * @param string $password
	 *
	 * @return void
	 */
	public function setPassword(string $password): void;

	/**
	 * @return string
	 */
	public function getPassword(): string;

	/**
	 * @param string $password
	 *
	 * @return bool
	 */
	public function verifyPassword(string $password): bool;

}
