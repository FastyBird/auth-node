<?php declare(strict_types = 1);

/**
 * IResourcesManager.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AccountsNode!
 * @subpackage     Models
 * @since          0.1.0
 *
 * @date           30.03.20
 */

namespace FastyBird\AccountsNode\Models\Resources;

use FastyBird\AccountsNode\Entities;
use FastyBird\AccountsNode\Models;
use Nette\Utils;

/**
 * ACL resources entities manager interface
 *
 * @package        FastyBird:AccountsNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IResourcesManager
{

	/**
	 * @param Utils\ArrayHash $values
	 *
	 * @return Entities\Resources\IResource
	 */
	public function create(
		Utils\ArrayHash $values
	): Entities\Resources\IResource;

	/**
	 * @param Entities\Resources\IResource $entity
	 * @param Utils\ArrayHash $values
	 *
	 * @return Entities\Resources\IResource
	 */
	public function update(
		Entities\Resources\IResource $entity,
		Utils\ArrayHash $values
	): Entities\Resources\IResource;

	/**
	 * @param Entities\Resources\IResource $entity
	 *
	 * @return bool
	 */
	public function delete(
		Entities\Resources\IResource $entity
	): bool;

}
