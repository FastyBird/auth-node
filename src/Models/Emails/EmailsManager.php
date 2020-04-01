<?php declare(strict_types = 1);

/**
 * EmailsManager.php
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

namespace FastyBird\AccountsNode\Models\Emails;

use FastyBird\AccountsNode\Entities;
use FastyBird\AccountsNode\Exceptions;
use FastyBird\AccountsNode\Models;
use IPub\DoctrineCrud\Crud;
use Nette;
use Nette\Utils;

/**
 * Accounts emails address entities manager
 *
 * @package        FastyBird:AccountsNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class EmailsManager implements IEmailsManager
{

	use Nette\SmartObject;

	/** @var Models\Emails\IEmailRepository */
	private $emailRepository;

	/** @var Crud\IEntityCrud */
	private $entityCrud;

	public function __construct(
		Crud\IEntityCrud $entityCrud,
		Models\Emails\IEmailRepository $emailRepository
	) {
		$this->emailRepository = $emailRepository;

		// Entity CRUD for handling entities
		$this->entityCrud = $entityCrud;
	}

	/**
	 * {@inheritDoc}
	 */
	public function create(
		Utils\ArrayHash $values
	): Entities\Emails\IEmail {
		// Get entity creator
		$creator = $this->entityCrud->getEntityCreator();

		// Service events
		$creator->beforeAction[] = function (Entities\Emails\IEmail $entity): void {
			$this->validateEmail($entity);
		};

		/** @var Entities\Emails\IEmail $entity */
		$entity = $creator->create($values);

		return $entity;
	}

	/**
	 * {@inheritDoc}
	 */
	public function update(
		Entities\Emails\IEmail $entity,
		Utils\ArrayHash $values
	): Entities\Emails\IEmail {
		// Get entity updater
		$updater = $this->entityCrud->getEntityUpdater();

		// Service events
		$updater->beforeAction[] = function (Entities\Emails\IEmail $entity): void {
			$this->validateEmail($entity);
		};

		/** @var Entities\Emails\IEmail $entity */
		$entity = $updater->update($values, $entity);

		return $entity;
	}

	/**
	 * {@inheritDoc}
	 */
	public function delete(
		Entities\Emails\IEmail $entity
	): bool {
		// Delete entity from database
		return $this->entityCrud->getEntityDeleter()->delete($entity);
	}

	/**
	 * @param Entities\Emails\IEmail $email
	 *
	 * @return void
	 *
	 * @throws Exceptions\EmailIsNotValidException
	 * @throws Exceptions\EmailAlreadyTakenException
	 */
	private function validateEmail(Entities\Emails\IEmail $email): void
	{
		if (!Utils\Validators::isEmail($email->getAddress())) {
			throw new Exceptions\EmailIsNotValidException('Given email is not valid');
		}

		$foundEmail = $this->emailRepository->findOneByAddress($email->getAddress());

		if ($foundEmail !== null && $foundEmail !== $email) {
			throw new Exceptions\EmailAlreadyTakenException('Given email is already taken');
		}
	}

}
