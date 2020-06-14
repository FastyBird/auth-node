<?php declare(strict_types = 1);

/**
 * TResourceFinder.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Controllers
 * @since          0.1.0
 *
 * @date           13.06.20
 */

namespace FastyBird\AuthNode\Controllers\Finders;

use FastyBird\AuthNode\Entities;
use FastyBird\AuthNode\Models;
use FastyBird\AuthNode\Queries;
use FastyBird\NodeJsonApi\Exceptions as NodeJsonApiExceptions;
use Fig\Http\Message\StatusCodeInterface;
use Nette\Localization;
use Ramsey\Uuid;

/**
 * @property-read Localization\ITranslator $translator
 * @property-read Models\Resources\IResourceRepository $resourceRepository
 */
trait TResourceFinder
{

	/**
	 * @param string $id
	 *
	 * @return Entities\Resources\IResource
	 *
	 * @throws NodeJsonApiExceptions\IJsonApiException
	 */
	protected function findResource(string $id): Entities\Resources\IResource
	{
		try {
			$findQuery = new Queries\FindResourcesQuery();
			$findQuery->byId(Uuid\Uuid::fromString($id));

			$resource = $this->resourceRepository->findOneBy($findQuery);

			if ($resource === null) {
				throw new NodeJsonApiExceptions\JsonApiErrorException(
					StatusCodeInterface::STATUS_NOT_FOUND,
					$this->translator->translate('//node.base.messages.resourceNotFound.heading'),
					$this->translator->translate('//node.base.messages.resourceNotFound.message')
				);
			}

		} catch (Uuid\Exception\InvalidUuidStringException $ex) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_NOT_FOUND,
				$this->translator->translate('//node.base.messages.resourceNotFound.heading'),
				$this->translator->translate('//node.base.messages.resourceNotFound.message')
			);
		}

		return $resource;
	}

}
