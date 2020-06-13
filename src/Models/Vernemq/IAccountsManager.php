<?php declare(strict_types = 1);

/**
 * IAuthenticationManager.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Models
 * @since          0.1.0
 *
 * @date           13.06.20
 */

namespace FastyBird\AuthNode\Models\Vernemq;

use FastyBird\AuthNode\Entities;
use FastyBird\AuthNode\Models;
use Nette\Utils;

/**
 * VerneMQ account entities manager interface
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IAccountsManager
{

	/**
	 * @param Utils\ArrayHash $values
	 *
	 * @return Entities\Vernemq\IAccount
	 */
	public function create(
		Utils\ArrayHash $values
	): Entities\Vernemq\IAccount;

	/**
	 * @param Entities\Vernemq\IAccount $entity
	 * @param Utils\ArrayHash $values
	 *
	 * @return Entities\Vernemq\IAccount
	 */
	public function update(
		Entities\Vernemq\IAccount $entity,
		Utils\ArrayHash $values
	): Entities\Vernemq\IAccount;

	/**
	 * @param Entities\Vernemq\IAccount $entity
	 *
	 * @return bool
	 */
	public function delete(
		Entities\Vernemq\IAccount $entity
	): bool;

}
