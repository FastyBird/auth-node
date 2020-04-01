<?php declare(strict_types = 1);

/**
 * IQuestionsManager.php
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

namespace FastyBird\AccountsNode\Models\SecurityQuestions;

use FastyBird\AccountsNode\Entities;
use FastyBird\AccountsNode\Models;
use Nette\Utils;

/**
 * Account security questions entities manager interface
 *
 * @package        FastyBird:AccountsNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IQuestionsManager
{

	/**
	 * @param Utils\ArrayHash $values
	 *
	 * @return Entities\SecurityQuestions\IQuestion
	 */
	public function create(
		Utils\ArrayHash $values
	): Entities\SecurityQuestions\IQuestion;

	/**
	 * @param Entities\SecurityQuestions\IQuestion $entity
	 * @param Utils\ArrayHash $values
	 *
	 * @return Entities\SecurityQuestions\IQuestion
	 */
	public function update(
		Entities\SecurityQuestions\IQuestion $entity,
		Utils\ArrayHash $values
	): Entities\SecurityQuestions\IQuestion;

	/**
	 * @param Entities\SecurityQuestions\IQuestion $entity
	 *
	 * @return bool
	 */
	public function delete(
		Entities\SecurityQuestions\IQuestion $entity
	): bool;

}
