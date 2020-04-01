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
use IPub\DoctrineCrud\Crud;
use Nette;
use Nette\Utils;

/**
 * Account profile security questions entities manager
 *
 * @package        FastyBird:AccountsNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class QuestionsManager implements IQuestionsManager
{

	use Nette\SmartObject;

	/** @var Crud\IEntityCrud */
	private $entityCrud;

	public function __construct(
		Crud\IEntityCrud $entityCrud
	) {
		// Entity CRUD for handling entities
		$this->entityCrud = $entityCrud;
	}

	/**
	 * {@inheritDoc}
	 */
	public function create(
		Utils\ArrayHash $values
	): Entities\SecurityQuestions\IQuestion {
		/** @var Entities\SecurityQuestions\IQuestion $entity */
		$entity = $this->entityCrud->getEntityCreator()->create($values);

		return $entity;
	}

	/**
	 * {@inheritDoc}
	 */
	public function update(
		Entities\SecurityQuestions\IQuestion $entity,
		Utils\ArrayHash $values
	): Entities\SecurityQuestions\IQuestion {
		/** @var Entities\SecurityQuestions\IQuestion $entity */
		$entity = $this->entityCrud->getEntityUpdater()->update($values, $entity);

		return $entity;
	}

	/**
	 * {@inheritDoc}
	 */
	public function delete(
		Entities\SecurityQuestions\IQuestion $entity
	): bool {
		// Delete entity from database
		return $this->entityCrud->getEntityDeleter()->delete($entity);
	}

}
