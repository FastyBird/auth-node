<?php declare(strict_types = 1);

/**
 * TAccountFinder.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Controllers
 * @since          0.1.0
 *
 * @date           22.06.20
 */

namespace FastyBird\AuthNode\Controllers\Finders;

use FastyBird\AuthNode;
use FastyBird\AuthNode\Entities;
use FastyBird\AuthNode\Models;
use FastyBird\AuthNode\Queries;
use FastyBird\AuthNode\Router;
use FastyBird\AuthNode\Security;
use FastyBird\NodeJsonApi\Exceptions as NodeJsonApiExceptions;
use Fig\Http\Message\RequestMethodInterface;
use Fig\Http\Message\StatusCodeInterface;
use IPub\SlimRouter;
use Nette\Localization;
use Psr\Http\Message;
use Ramsey\Uuid;

/**
 * @property-read Localization\ITranslator $translator
 * @property-read Security\User $user
 * @property-read Models\Accounts\IAccountRepository $accountRepository
 */
trait TAccountFinder
{

	/**
	 * @param Message\ServerRequestInterface $request
	 *
	 * @return Entities\Accounts\IAccount
	 *
	 * @throws NodeJsonApiExceptions\JsonApiErrorException
	 */
	protected function findAccount(
		Message\ServerRequestInterface $request
	): Entities\Accounts\IAccount {
		if ($request->getAttribute(Router\Router::URL_ACCOUNT_ID, null) === null) {
			if (
				$this->user->getAccount() === null
			) {
				throw new NodeJsonApiExceptions\JsonApiErrorException(
					StatusCodeInterface::STATUS_FORBIDDEN,
					$this->translator->translate('//node.base.messages.forbidden.heading'),
					$this->translator->translate('//node.base.messages.forbidden.message')
				);
			}

			return $this->user->getAccount();
		}

		/** @var SlimRouter\Routing\IRoute|null $route */
		$route = $request->getAttribute(Router\Router::ROUTE, null);

		if ($route === null) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_BAD_REQUEST,
				$this->translator->translate('//node.base.messages.invalid.heading'),
				$this->translator->translate('//node.base.messages.invalid.message')
			);
		}

		// Determine resource from route name
		switch ($route->getName()) {
			case AuthNode\Constants::ROUTE_NAME_ACCOUNT_EMAILS:
			case AuthNode\Constants::ROUTE_NAME_ACCOUNT_EMAIL:
			case AuthNode\Constants::ROUTE_NAME_ACCOUNT_EMAIL_RELATIONSHIPS:
				$resource = AuthNode\Constants::ACL_RESOURCE_MANAGE_EMAILS;
				break;

			case AuthNode\Constants::ROUTE_NAME_ACCOUNT_IDENTITIES:
			case AuthNode\Constants::ROUTE_NAME_ACCOUNT_IDENTITY:
			case AuthNode\Constants::ROUTE_NAME_ACCOUNT_IDENTITY_RELATIONSHIPS:
				$resource = AuthNode\Constants::ACL_RESOURCE_MANAGE_IDENTITIES;
				break;

			case AuthNode\Constants::ROUTE_NAME_ACCOUNT:
			case AuthNode\Constants::ROUTE_NAME_ACCOUNT_RELATIONSHIPS:
			case AuthNode\Constants::ROUTE_NAME_ACCOUNT_ROLES:
				$resource = AuthNode\Constants::ACL_RESOURCE_MANAGE_ACCOUNTS;
				break;

			default:
				throw new NodeJsonApiExceptions\JsonApiErrorException(
					StatusCodeInterface::STATUS_BAD_REQUEST,
					$this->translator->translate('//node.base.messages.invalid.heading'),
					$this->translator->translate('//node.base.messages.invalid.message')
				);
		}

		// Determine privilege from request method type
		switch ($request->getMethod()) {
			case RequestMethodInterface::METHOD_GET:
				$privilege = AuthNode\Constants::ACL_PRIVILEGE_READ;
				break;

			case RequestMethodInterface::METHOD_PATCH:
				$privilege = AuthNode\Constants::ACL_PRIVILEGE_UPDATE;
				break;

			case RequestMethodInterface::METHOD_POST:
				$privilege = AuthNode\Constants::ACL_PRIVILEGE_CREATE;
				break;

			case RequestMethodInterface::METHOD_DELETE:
				$privilege = AuthNode\Constants::ACL_PRIVILEGE_DELETE;
				break;

			default:
				throw new NodeJsonApiExceptions\JsonApiErrorException(
					StatusCodeInterface::STATUS_BAD_REQUEST,
					$this->translator->translate('//node.base.messages.invalid.heading'),
					$this->translator->translate('//node.base.messages.invalid.message')
				);
		}

		// Check if logged in user has access rights
		if (!$this->user->isAllowed(
			$resource,
			$privilege
		)) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_FORBIDDEN,
				$this->translator->translate('//node.base.messages.forbidden.heading'),
				$this->translator->translate('//node.base.messages.forbidden.message')
			);
		}

		if (!Uuid\Uuid::isValid($request->getAttribute(Router\Router::URL_ACCOUNT_ID, null))) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_NOT_FOUND,
				$this->translator->translate('messages.notFound.heading'),
				$this->translator->translate('messages.notFound.message')
			);
		}

		$findQuery = new Queries\FindAccountsQuery();
		$findQuery->byId(Uuid\Uuid::fromString($request->getAttribute(Router\Router::URL_ACCOUNT_ID, null)));

		$account = $this->accountRepository->findOneBy($findQuery);

		if ($account === null) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_NOT_FOUND,
				$this->translator->translate('messages.notFound.heading'),
				$this->translator->translate('messages.notFound.message')
			);
		}

		return $account;
	}

}
