<?php declare(strict_types = 1);

/**
 * TIdentityFinder.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Controllers
 * @since          0.1.0
 *
 * @date           25.06.20
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
 * @property-read Models\Identities\IIdentityRepository $identityRepository
 */
trait TIdentityFinder
{

	/**
	 * @param Message\ServerRequestInterface $request
	 * @param Entities\Accounts\IAccount $account
	 *
	 * @return Entities\Identities\IIdentity
	 *
	 * @throws NodeJsonApiExceptions\IJsonApiException
	 */
	private function findIdentity(
		Message\ServerRequestInterface $request,
		Entities\Accounts\IAccount $account
	): Entities\Identities\IIdentity {
		if (!Uuid\Uuid::isValid($request->getAttribute(Router\Router::URL_ITEM_ID, null))) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_NOT_FOUND,
				$this->translator->translate('messages.notFound.heading'),
				$this->translator->translate('messages.notFound.message')
			);
		}

		$findQuery = new Queries\FindIdentitiesQuery();
		$findQuery->byId(Uuid\Uuid::fromString($request->getAttribute(Router\Router::URL_ITEM_ID, null)));
		$findQuery->forAccount($account);

		$identity = $this->identityRepository->findOneBy($findQuery);

		if ($identity === null) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_NOT_FOUND,
				$this->translator->translate('messages.notFound.heading'),
				$this->translator->translate('messages.notFound.message')
			);
		}

		return $identity;
	}

}
