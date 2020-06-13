<?php declare(strict_types = 1);

/**
 * IRolesManager.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Models
 * @since          0.1.0
 *
 * @date           30.03.20
 */

namespace FastyBird\AuthNode\Models\Roles;

use FastyBird\AuthNode\Entities;
use FastyBird\AuthNode\Models;
use Nette\Utils;

/**
 * ACL roles entities manager interface
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IRolesManager
{

	/**
	 * @param Utils\ArrayHash $values
	 *
	 * @return Entities\Roles\IRole
	 */
	public function create(
		Utils\ArrayHash $values
	): Entities\Roles\IRole;

	/**
	 * @param Entities\Roles\IRole $entity
	 * @param Utils\ArrayHash $values
	 *
	 * @return Entities\Roles\IRole
	 */
	public function update(
		Entities\Roles\IRole $entity,
		Utils\ArrayHash $values
	): Entities\Roles\IRole;

	/**
	 * @param Entities\Roles\IRole $entity
	 *
	 * @return bool
	 */
	public function delete(
		Entities\Roles\IRole $entity
	): bool;

}
