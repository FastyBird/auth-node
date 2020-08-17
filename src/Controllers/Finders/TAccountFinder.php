<?php declare(strict_types = 1);

/**
 * TAccountFinder.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Controllers
 * @since          0.1.0
 *
 * @date           22.06.20
 */

namespace FastyBird\AuthNode\Controllers\Finders;

use FastyBird\AuthNode\Entities;
use FastyBird\AuthNode\Models;
use FastyBird\AuthNode\Queries;
use FastyBird\AuthNode\Router;
use FastyBird\AuthNode\Security;
use FastyBird\NodeJsonApi\Exceptions as NodeJsonApiExceptions;
use Fig\Http\Message\StatusCodeInterface;
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
		if (!Uuid\Uuid::isValid($request->getAttribute(Router\Router::URL_ACCOUNT_ID, null))) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_NOT_FOUND,
				$this->translator->translate('//node.accounts.messages.notFound.heading'),
				$this->translator->translate('//node.accounts.messages.notFound.message')
			);
		}

		$findQuery = new Queries\FindAccountsQuery();
		$findQuery->byId(Uuid\Uuid::fromString($request->getAttribute(Router\Router::URL_ACCOUNT_ID, null)));

		$account = $this->accountRepository->findOneBy($findQuery);

		if ($account === null) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_NOT_FOUND,
				$this->translator->translate('//node.accounts.messages.notFound.heading'),
				$this->translator->translate('//node.accounts.messages.notFound.message')
			);
		}

		return $account;
	}

}
