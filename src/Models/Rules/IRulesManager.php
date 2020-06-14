<?php declare(strict_types = 1);

/**
 * IRulesManager.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Models
 * @since          0.1.0
 *
 * @date           14.06.20
 */

namespace FastyBird\AuthNode\Models\Rules;

use FastyBird\AuthNode\Entities;
use FastyBird\AuthNode\Models;
use Nette\Utils;

/**
 * ACL rules entities manager interface
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IRulesManager
{

	/**
	 * @param Utils\ArrayHash $values
	 *
	 * @return Entities\Rules\IRule
	 */
	public function create(
		Utils\ArrayHash $values
	): Entities\Rules\IRule;

	/**
	 * @param Entities\Rules\IRule $entity
	 * @param Utils\ArrayHash $values
	 *
	 * @return Entities\Rules\IRule
	 */
	public function update(
		Entities\Rules\IRule $entity,
		Utils\ArrayHash $values
	): Entities\Rules\IRule;

	/**
	 * @param Entities\Rules\IRule $entity
	 *
	 * @return bool
	 */
	public function delete(
		Entities\Rules\IRule $entity
	): bool;

}
