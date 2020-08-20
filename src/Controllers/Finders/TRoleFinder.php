<?php declare(strict_types = 1);

/**
 * TRoleFinder.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Controllers
 * @since          0.1.0
 *
 * @date           03.06.20
 */

namespace FastyBird\AuthNode\Controllers\Finders;

use FastyBird\AuthNode\Entities;
use FastyBird\AuthNode\Models;
use FastyBird\AuthNode\Queries;
use FastyBird\AuthNode\Router;
use FastyBird\NodeJsonApi\Exceptions as NodeJsonApiExceptions;
use Fig\Http\Message\StatusCodeInterface;
use Nette\Localization;
use Psr\Http\Message;
use Ramsey\Uuid;

/**
 * @property-read Localization\ITranslator $translator
 * @property-read Models\Roles\IRoleRepository $roleRepository
 */
trait TRoleFinder
{

	/**
	 * @param Message\ServerRequestInterface $request
	 *
	 * @return Entities\Roles\IRole
	 *
	 * @throws NodeJsonApiExceptions\IJsonApiException
	 */
	protected function findRole(
		Message\ServerRequestInterface $request
	): Entities\Roles\IRole {
		if (!Uuid\Uuid::isValid($request->getAttribute(Router\Router::URL_ITEM_ID, null))) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_NOT_FOUND,
				$this->translator->translate('//node.base.messages.notFound.heading'),
				$this->translator->translate('//node.base.messages.notFound.message')
			);
		}

		$findQuery = new Queries\FindRolesQuery();
		$findQuery->byId(Uuid\Uuid::fromString($request->getAttribute(Router\Router::URL_ITEM_ID, null)));

		$role = $this->roleRepository->findOneBy($findQuery);

		if ($role === null) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_NOT_FOUND,
				$this->translator->translate('//node.base.messages.notFound.heading'),
				$this->translator->translate('//node.base.messages.notFound.message')
			);
		}

		return $role;
	}

}
