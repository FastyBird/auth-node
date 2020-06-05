<?php declare(strict_types = 1);

/**
 * TRoleFinder.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AccountsNode!
 * @subpackage     Controllers
 * @since          0.1.0
 *
 * @date           03.06.20
 */

namespace FastyBird\AccountsNode\Controllers\Finders;

use FastyBird\AccountsNode\Entities;
use FastyBird\AccountsNode\Models;
use FastyBird\AccountsNode\Queries;
use FastyBird\NodeWebServer\Exceptions as NodeWebServerExceptions;
use Fig\Http\Message\StatusCodeInterface;
use Nette\Localization;
use Ramsey\Uuid;

/**
 * @property-read Localization\ITranslator $translator
 * @property-read Models\Roles\IRoleRepository $roleRepository
 */
trait TRoleFinder
{

	/**
	 * @param string $id
	 *
	 * @return Entities\Roles\IRole
	 *
	 * @throws NodeWebServerExceptions\IJsonApiException
	 */
	protected function findRole(string $id): Entities\Roles\IRole
	{
		try {
			$findQuery = new Queries\FindRolesQuery();
			$findQuery->byId(Uuid\Uuid::fromString($id));

			$role = $this->roleRepository->findOneBy($findQuery);

			if ($role === null) {
				throw new NodeWebServerExceptions\JsonApiErrorException(
					StatusCodeInterface::STATUS_NOT_FOUND,
					$this->translator->translate('//node.base.messages.roleNotFound.heading'),
					$this->translator->translate('//node.base.messages.roleNotFound.message')
				);
			}

		} catch (Uuid\Exception\InvalidUuidStringException $ex) {
			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_NOT_FOUND,
				$this->translator->translate('//node.base.messages.roleNotFound.heading'),
				$this->translator->translate('//node.base.messages.roleNotFound.message')
			);
		}

		return $role;
	}

}
