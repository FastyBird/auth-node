<?php declare(strict_types = 1);

/**
 * SystemIdentityV1Controller.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AccountsNode!
 * @subpackage     Controllers
 * @since          0.1.0
 *
 * @date           31.03.20
 */

namespace FastyBird\AccountsNode\Controllers;

use Doctrine;
use FastyBird\AccountsNode\Entities;
use FastyBird\AccountsNode\Helpers;
use FastyBird\AccountsNode\Models;
use FastyBird\AccountsNode\Queries;
use FastyBird\AccountsNode\Router;
use FastyBird\AccountsNode\Schemas;
use FastyBird\NodeWebServer\Exceptions as NodeWebServerExceptions;
use FastyBird\NodeWebServer\Http as NodeWebServerHttp;
use Fig\Http\Message\StatusCodeInterface;
use Nette\Utils;
use Psr\Http\Message;
use Throwable;

/**
 * System identity controller
 *
 * @package        FastyBird:AccountsNode!
 * @subpackage     Controllers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class SystemIdentityV1Controller extends BaseV1Controller
{

	/** @var Models\Identities\IIdentityRepository */
	private $identityRepository;

	/** @var Models\Identities\IIdentitiesManager */
	private $identitiesManager;

	/** @var Models\Accounts\IAccountsManager */
	private $accountsManager;

	/** @var Helpers\SecurityHash */
	private $securityHash;

	/** @var string */
	protected $translationDomain = 'node.systemIdentity';

	public function __construct(
		Models\Identities\IIdentityRepository $identityRepository,
		Models\Identities\IIdentitiesManager $identitiesManager,
		Models\Accounts\IAccountsManager $accountsManager,
		Helpers\SecurityHash $securityHash
	) {
		$this->identityRepository = $identityRepository;
		$this->identitiesManager = $identitiesManager;
		$this->accountsManager = $accountsManager;

		$this->securityHash = $securityHash;
	}

	/**
	 * @param Message\ServerRequestInterface $request
	 * @param NodeWebServerHttp\Response $response
	 *
	 * @return NodeWebServerHttp\Response
	 *
	 * @throws NodeWebServerExceptions\IJsonApiException
	 */
	public function index(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		if (
			$this->user->getAccount() === null
			|| $this->user->getAccount()->getPlainId() !== $request->getAttribute(Router\Router::URL_ACCOUNT_ID)
		) {
			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_FORBIDDEN,
				$this->translator->translate('//node.base.messages.forbidden.heading'),
				$this->translator->translate('//node.base.messages.forbidden.message')
			);
		}

		$findQuery = new Queries\FindIdentitiesQuery();
		$findQuery->forAccount($this->user->getAccount());

		$identities = $this->identityRepository->getResultSet($findQuery);

		return $response
			->withEntity(NodeWebServerHttp\ScalarEntity::from($identities));
	}

	/**
	 * @param Message\ServerRequestInterface $request
	 * @param NodeWebServerHttp\Response $response
	 *
	 * @return NodeWebServerHttp\Response
	 *
	 * @throws NodeWebServerExceptions\IJsonApiException
	 */
	public function read(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		if (
			$this->user->getAccount() === null
			|| $this->user->getAccount()->getPlainId() !== $request->getAttribute(Router\Router::URL_ACCOUNT_ID)
		) {
			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_FORBIDDEN,
				$this->translator->translate('//node.base.messages.forbidden.heading'),
				$this->translator->translate('//node.base.messages.forbidden.message')
			);
		}

		// Find identity
		$identity = $this->findIdentity($request->getAttribute(Router\Router::URL_ITEM_ID));

		if ($identity === null) {
			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_NOT_FOUND,
				$this->translator->translate('messages.notFound.heading'),
				$this->translator->translate('messages.notFound.message')
			);
		}

		return $response
			->withEntity(NodeWebServerHttp\ScalarEntity::from($identity));
	}

	/**
	 * @param Message\ServerRequestInterface $request
	 * @param NodeWebServerHttp\Response $response
	 *
	 * @return NodeWebServerHttp\Response
	 *
	 * @throws NodeWebServerExceptions\IJsonApiException
	 * @throws Doctrine\DBAL\ConnectionException
	 */
	public function update(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		if (
			$this->user->getAccount() === null
			|| $this->user->getAccount()->getPlainId() !== $request->getAttribute(Router\Router::URL_ACCOUNT_ID)
		) {
			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_FORBIDDEN,
				$this->translator->translate('//node.base.messages.forbidden.heading'),
				$this->translator->translate('//node.base.messages.forbidden.message')
			);
		}

		/** @var Entities\Identities\System|null $identity */
		$identity = $this->identityRepository->findOneForAccount($this->user->getAccount(), Entities\Identities\System::class);

		if ($identity === null) {
			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_NOT_FOUND,
				$this->translator->translate('messages.notFound.heading'),
				$this->translator->translate('messages.notFound.message')
			);
		}

		$document = $this->createDocument($request);

		if ($request->getAttribute(Router\Router::URL_ITEM_ID) !== $document->getResource()->getIdentifier()->getId()) {
			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_BAD_REQUEST,
				$this->translator->translate('//node.base.messages.invalid.heading'),
				$this->translator->translate('//node.base.messages.invalid.message')
			);
		}

		$attributes = $document->getResource()->getAttributes();

		if (
			!$attributes->has('password')
			|| !$attributes->get('password')->has('current')
		) {
			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('//node.base.messages.missingRequired.heading'),
				$this->translator->translate('//node.base.messages.missingRequired.message'),
				[
					'pointer' => '/data/attributes/password/current',
				]
			);
		}

		if (
			!$attributes->has('password')
			|| !$attributes->get('password')->has('new')
		) {
			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('//node.base.messages.missingRequired.heading'),
				$this->translator->translate('//node.base.messages.missingRequired.message'),
				[
					'pointer' => '/data/attributes/password/new',
				]
			);
		}

		if (!$identity->verifyPassword((string) $attributes->get('password')->get('current'))) {
			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('messages.invalidPassword.heading'),
				$this->translator->translate('messages.invalidPassword.message'),
				[
					'pointer' => '/data/attributes/password/current',
				]
			);
		}

		try {
			// Start transaction connection to the database
			$this->getOrmConnection()->beginTransaction();

			if ($document->getResource()->getType() === Schemas\SystemIdentitySchema::SCHEMA_TYPE) {
				$update = new Utils\ArrayHash();
				$update->offsetSet('password', (string) $attributes->get('password')->get('new'));

				// Update item in database
				$this->identitiesManager->update($identity, $update);

			} else {
				throw new NodeWebServerExceptions\JsonApiErrorException(
					StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
					$this->translator->translate('messages.invalidType.heading'),
					$this->translator->translate('messages.invalidType.message'),
					[
						'pointer' => '/data/type',
					]
				);
			}

			// Commit all changes into database
			$this->getOrmConnection()->commit();

		} catch (NodeWebServerExceptions\IJsonApiException $ex) {
			// Revert all changes when error occur
			$this->getOrmConnection()->rollBack();

			throw $ex;

		} catch (Throwable $ex) {
			// Revert all changes when error occur
			$this->getOrmConnection()->rollBack();

			// Log catched exception
			$this->logger->error('[CONTROLLER] ' . $ex->getMessage(), [
				'exception' => [
					'message' => $ex->getMessage(),
					'code'    => $ex->getCode(),
				],
			]);

			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('messages.notUpdated.heading'),
				$this->translator->translate('messages.notUpdated.message')
			);
		}

		/** @var NodeWebServerHttp\Response $response */
		$response = $response
			->withStatus(StatusCodeInterface::STATUS_NO_CONTENT);

		return $response;
	}

	/**
	 * @param Message\ServerRequestInterface $request
	 * @param NodeWebServerHttp\Response $response
	 *
	 * @return NodeWebServerHttp\Response
	 *
	 * @throws NodeWebServerExceptions\IJsonApiException
	 * @throws Doctrine\DBAL\ConnectionException
	 */
	public function requestPassword(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		$document = $this->createDocument($request);

		$attributes = $document->getResource()->getAttributes();

		if (!$attributes->has('uid')) {
			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('//node.base.messages.missingRequired.heading'),
				$this->translator->translate('//node.base.messages.missingRequired.message'),
				[
					'pointer' => '/data/attributes/uid',
				]
			);
		}

		if ($document->getResource()->getType() !== Schemas\SystemIdentitySchema::SCHEMA_TYPE) {
			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('messages.invalidType.heading'),
				$this->translator->translate('messages.invalidType.message'),
				[
					'pointer' => '/data/type',
				]
			);
		}

		$identity = $this->findIdentity((string) $attributes->get('uid'));

		if ($identity === null) {
			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_NOT_FOUND,
				$this->translator->translate('messages.notFound.heading'),
				$this->translator->translate('messages.notFound.message'),
				[
					'pointer' => '/data/attributes/uid',
				]
			);
		}

		$account = $identity->getAccount();

		if ($account->isDeleted()) {
			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_NOT_FOUND,
				$this->translator->translate('messages.notFound.heading'),
				$this->translator->translate('messages.notFound.message'),
				[
					'pointer' => '/data/attributes/uid',
				]
			);

		} elseif ($account->isNotActivated()) {
			$hash = $account->getRequestHash();

			if ($hash === null || !$this->securityHash->isValid($hash)) {
				// Verification hash is expired, create new one for user
				$this->accountsManager->update($account, Utils\ArrayHash::from([
					'requestHash' => $this->securityHash->createKey(),
				]));
			}

			// TODO: Send new user email

			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('messages.notActivated.heading'),
				$this->translator->translate('messages.notActivated.message'),
				[
					'pointer' => '/data/attributes/uid',
				]
			);

		} elseif ($account->isBlocked()) {
			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('messages.blocked.heading'),
				$this->translator->translate('messages.blocked.message'),
				[
					'pointer' => '/data/attributes/uid',
				]
			);
		}

		try {
			// Start transaction connection to the database
			$this->getOrmConnection()->beginTransaction();

			// Update entity
			$this->accountsManager->update($account, Utils\ArrayHash::from([
				'requestHash' => $this->securityHash->createKey(),
			]));

			// TODO: Send reset password email

			// Commit all changes into database
			$this->getOrmConnection()->commit();

		} catch (NodeWebServerExceptions\IJsonApiException $ex) {
			// Revert all changes when error occur
			$this->getOrmConnection()->rollBack();

			throw $ex;

		} catch (Throwable $ex) {
			// Revert all changes when error occur
			$this->getOrmConnection()->rollBack();

			// Log catched exception
			$this->logger->error('[CONTROLLER] ' . $ex->getMessage(), [
				'exception' => [
					'message' => $ex->getMessage(),
					'code'    => $ex->getCode(),
				],
			]);

			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('messages.requestNotSent.heading'),
				$this->translator->translate('messages.requestNotSent.message'),
				[
					'pointer' => '/data/attributes/uid',
				]
			);
		}

		/** @var NodeWebServerHttp\Response $response */
		$response = $response
			->withStatus(StatusCodeInterface::STATUS_NO_CONTENT);

		return $response;
	}

	/**
	 * @param Message\ServerRequestInterface $request
	 * @param NodeWebServerHttp\Response $response
	 *
	 * @return NodeWebServerHttp\Response
	 *
	 * @throws NodeWebServerExceptions\IJsonApiException
	 */
	public function readRelationship(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		if (
			$this->user->getAccount() === null
			|| $this->user->getAccount()->getPlainId() !== $request->getAttribute(Router\Router::URL_ACCOUNT_ID)
		) {
			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_FORBIDDEN,
				$this->translator->translate('//node.base.messages.forbidden.heading'),
				$this->translator->translate('//node.base.messages.forbidden.message')
			);
		}

		// At first, try to load identity
		$identity = $this->findIdentity($request->getAttribute(Router\Router::URL_ITEM_ID));

		if ($identity === null) {
			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_NOT_FOUND,
				$this->translator->translate('messages.notFound.heading'),
				$this->translator->translate('messages.notFound.message')
			);
		}

		// & relation entity name
		$relationEntity = strtolower($request->getAttribute(Router\Router::RELATION_ENTITY));

		if ($relationEntity === Schemas\EmailSchema::RELATIONSHIPS_ACCOUNT) {
			return $response
				->withEntity(NodeWebServerHttp\ScalarEntity::from($identity->getAccount()));
		}

		$this->throwUnknownRelation($relationEntity);

		return $response;
	}

	/**
	 * @param Message\ServerRequestInterface $request
	 * @param NodeWebServerHttp\Response $response
	 *
	 * @return NodeWebServerHttp\Response
	 *
	 * @throws NodeWebServerExceptions\IJsonApiException
	 */
	public function validate(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		if (
			$this->user->getAccount() === null
			|| $this->user->getAccount()->getPlainId() !== $request->getAttribute(Router\Router::URL_ACCOUNT_ID)
		) {
			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_FORBIDDEN,
				$this->translator->translate('//node.base.messages.forbidden.heading'),
				$this->translator->translate('//node.base.messages.forbidden.message')
			);
		}

		/** @var Entities\Identities\System|null $identity */
		$identity = $this->identityRepository->findOneForAccount($this->user->getAccount(), Entities\Identities\System::class);

		if ($identity === null) {
			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_NOT_FOUND,
				$this->translator->translate('messages.notFound.heading'),
				$this->translator->translate('messages.notFound.message')
			);
		}

		$document = $this->createDocument($request);

		if ($request->getAttribute(Router\Router::URL_ITEM_ID) !== $document->getResource()->getIdentifier()->getId()) {
			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_BAD_REQUEST,
				$this->translator->translate('//node.base.messages.invalid.heading'),
				$this->translator->translate('//node.base.messages.invalid.message')
			);
		}

		$attributes = $document->getResource()->getAttributes();

		if ($document->getResource()->getType() !== Schemas\SystemIdentitySchema::SCHEMA_TYPE) {
			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('messages.invalidType.heading'),
				$this->translator->translate('messages.invalidType.message'),
				[
					'pointer' => '/data/type',
				]
			);
		}

		if (
			!$attributes->has('password')
			|| !$attributes->get('password')->has('current')
		) {
			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('//node.base.messages.missingRequired.heading'),
				$this->translator->translate('//node.base.messages.missingRequired.message'),
				[
					'pointer' => '/data/attributes/password/current',
				]
			);
		}

		if (!$identity->verifyPassword((string) $attributes->get('password')->get('current'))) {
			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('messages.invalidPassword.heading'),
				$this->translator->translate('messages.invalidPassword.message'),
				[
					'pointer' => '/data/attributes/password/current',
				]
			);
		}

		/** @var NodeWebServerHttp\Response $response */
		$response = $response
			->withStatus(StatusCodeInterface::STATUS_NO_CONTENT);

		return $response;
	}

	/**
	 * @param string $uid
	 *
	 * @return Entities\Identities\System|null
	 */
	private function findIdentity(string $uid): ?Entities\Identities\System
	{
		/** @var Entities\Identities\System|null $identity */
		$identity = $this->identityRepository->findOneByUid($uid, Entities\Identities\System::class);

		if ($identity === null) {
			/** @var Entities\Identities\System|null $identity */
			$identity = $this->identityRepository->findOneByEmail($uid, Entities\Identities\System::class);
		}

		return $identity;
	}

}
