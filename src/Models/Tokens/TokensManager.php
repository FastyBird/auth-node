<?php declare(strict_types = 1);

/**
 * TokensManager.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Models
 * @since          0.1.0
 *
 * @date           31.03.20
 */

namespace FastyBird\AuthNode\Models\Tokens;

use FastyBird\AuthNode\Entities;
use FastyBird\AuthNode\Exceptions;
use FastyBird\AuthNode\Models;
use FastyBird\AuthNode\Security;
use IPub\DoctrineCrud\Crud;
use Nette;
use Nette\Utils;
use Ramsey\Uuid;
use Throwable;

/**
 * Access tokens entities manager
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class TokensManager implements ITokensManager
{

	use Nette\SmartObject;

	/** @var Security\TokenBuilder */
	private $tokenBuilder;

	/** @var Crud\IEntityCrud */
	private $entityCrud;

	public function __construct(
		Crud\IEntityCrud $entityCrud,
		Security\TokenBuilder $tokenBuilder
	) {
		$this->tokenBuilder = $tokenBuilder;

		// Entity CRUD for handling entities
		$this->entityCrud = $entityCrud;
	}

	/**
	 * {@inheritDoc}
	 */
	public function create(
		Utils\ArrayHash $values
	): Entities\Tokens\IToken {
		// Get entity creator
		$creator = $this->entityCrud->getEntityCreator();

		if (!$values->offsetExists('token')) {
			try {
				$tokenId = Uuid\Uuid::uuid4();

			} catch (Throwable $ex) {
				throw new Exceptions\InvalidStateException('Token identifier could not be generated');
			}

			$values->id = $tokenId;

			$values->token = $this->tokenBuilder->build(
				$tokenId->toString(),
				$values->offsetExists('validTill') ? $values->validTill : null
			);
		}

		/** @var Entities\Tokens\IToken $entity */
		$entity = $creator->create($values);

		return $entity;
	}

	/**
	 * {@inheritDoc}
	 */
	public function update(
		Entities\Tokens\IToken $entity,
		Utils\ArrayHash $values
	): Entities\Tokens\IToken {
		/** @var Entities\Tokens\IToken $entity */
		$entity = $this->entityCrud->getEntityUpdater()->update($values, $entity);

		return $entity;
	}

	/**
	 * {@inheritDoc}
	 */
	public function delete(
		Entities\Tokens\IToken $entity
	): bool {
		// Delete entity from database
		return $this->entityCrud->getEntityDeleter()->delete($entity);
	}

}
