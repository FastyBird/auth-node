<?php declare(strict_types = 1);

/**
 * IPrivilegesManager.php
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

namespace FastyBird\AuthNode\Models\Privileges;

use FastyBird\AuthNode\Entities;
use FastyBird\AuthNode\Models;
use Nette\Utils;

/**
 * ACL privileges entities manager interface
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IPrivilegesManager
{

	/**
	 * @param Utils\ArrayHash $values
	 *
	 * @return Entities\Privileges\IPrivilege
	 */
	public function create(
		Utils\ArrayHash $values
	): Entities\Privileges\IPrivilege;

	/**
	 * @param Entities\Privileges\IPrivilege $entity
	 * @param Utils\ArrayHash $values
	 *
	 * @return Entities\Privileges\IPrivilege
	 */
	public function update(
		Entities\Privileges\IPrivilege $entity,
		Utils\ArrayHash $values
	): Entities\Privileges\IPrivilege;

	/**
	 * @param Entities\Privileges\IPrivilege $entity
	 *
	 * @return bool
	 */
	public function delete(
		Entities\Privileges\IPrivilege $entity
	): bool;

}
