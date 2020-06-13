<?php declare(strict_types = 1);

/**
 * IEmailsManager.php
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

namespace FastyBird\AuthNode\Models\Emails;

use FastyBird\AuthNode\Entities;
use FastyBird\AuthNode\Models;
use Nette\Utils;

/**
 * Accounts emails address entities manager interface
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IEmailsManager
{

	/**
	 * @param Utils\ArrayHash $values
	 *
	 * @return Entities\Emails\IEmail
	 */
	public function create(
		Utils\ArrayHash $values
	): Entities\Emails\IEmail;

	/**
	 * @param Entities\Emails\IEmail $entity
	 * @param Utils\ArrayHash $values
	 *
	 * @return Entities\Emails\IEmail
	 */
	public function update(
		Entities\Emails\IEmail $entity,
		Utils\ArrayHash $values
	): Entities\Emails\IEmail;

	/**
	 * @param Entities\Emails\IEmail $entity
	 *
	 * @return bool
	 */
	public function delete(
		Entities\Emails\IEmail $entity
	): bool;

}
