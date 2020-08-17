<?php declare(strict_types = 1);

/**
 * AccountIdentitiesV1Controller.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Controllers
 * @since          0.1.0
 *
 * @date           31.03.20
 */

namespace FastyBird\AuthNode\Controllers;

use Doctrine;
use FastyBird\AuthNode\Controllers;
use FastyBird\AuthNode\Entities;
use FastyBird\AuthNode\Helpers;
use FastyBird\AuthNode\Models;
use FastyBird\AuthNode\Queries;
use FastyBird\AuthNode\Router;
use FastyBird\AuthNode\Schemas;
use FastyBird\NodeJsonApi\Exceptions as NodeJsonApiExceptions;
use FastyBird\NodeWebServer\Http as NodeWebServerHttp;
use Fig\Http\Message\StatusCodeInterface;
use Nette\Utils;
use Psr\Http\Message;
use Throwable;

/**
 * Account identity controller
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Controllers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class AccountIdentitiesV1Controller extends BaseV1Controller
{

	use Controllers\Finders\TIdentityFinder;

	/** @var Models\Identities\IIdentitiesManager */
	private $identitiesManager;

	/** @var Models\Accounts\IAccountsManager */
	private $accountsManager;

	/** @var Helpers\SecurityHash */
	private $securityHash;

	/** @var Models\Identities\IIdentityRepository */
	protected $identityRepository;

	/** @var string */
	protected $translationDomain = 'node.identities';

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
	 * @throws NodeJsonApiExceptions\IJsonApiException
	 *
	 * @Secured
	 * @Secured\User(loggedIn)
	 */
	public function index(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		$findQuery = new Queries\FindIdentitiesQuery();
		$findQuery->forAccount($this->findAccount());

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
	 * @throws NodeJsonApiExceptions\IJsonApiException
	 *
	 * @Secured
	 * @Secured\User(loggedIn)
	 */
	public function read(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		// Find identity
		$identity = $this->findIdentity($request, $this->findAccount());

		return $response
			->withEntity(NodeWebServerHttp\ScalarEntity::from($identity));
	}

	/**
	 * @param Message\ServerRequestInterface $request
	 * @param NodeWebServerHttp\Response $response
	 *
	 * @return NodeWebServerHttp\Response
	 *
	 * @throws NodeJsonApiExceptions\IJsonApiException
	 * @throws Doctrine\DBAL\ConnectionException
	 *
	 * @Secured
	 * @Secured\User(loggedIn)
	 */
	public function update(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		$document = $this->createDocument($request);

		$identity = $this->findIdentity($request, $this->findAccount());

		if ($request->getAttribute(Router\Router::URL_ITEM_ID) !== $document->getResource()->getIdentifier()->getId()) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_BAD_REQUEST,
				$this->translator->translate('//node.base.messages.identifierInvalid.heading'),
				$this->translator->translate('//node.base.messages.identifierInvalid.message')
			);
		}

		try {
			// Start transaction connection to the database
			$this->getOrmConnection()->beginTransaction();

			if (
				$document->getResource()->getType() === Schemas\Identities\UserAccountIdentitySchema::SCHEMA_TYPE
				&& $identity instanceof Entities\Identities\IUserAccountIdentity
			) {
				$attributes = $document->getResource()->getAttributes();

				if (
					!$attributes->has('password')
					|| !$attributes->get('password')->has('current')
				) {
					throw new NodeJsonApiExceptions\JsonApiErrorException(
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
					throw new NodeJsonApiExceptions\JsonApiErrorException(
						StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
						$this->translator->translate('//node.base.messages.missingRequired.heading'),
						$this->translator->translate('//node.base.messages.missingRequired.message'),
						[
							'pointer' => '/data/attributes/password/new',
						]
					);
				}

				if (!$identity->verifyPassword((string) $attributes->get('password')->get('current'))) {
					throw new NodeJsonApiExceptions\JsonApiErrorException(
						StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
						$this->translator->translate('messages.invalidPassword.heading'),
						$this->translator->translate('messages.invalidPassword.message'),
						[
							'pointer' => '/data/attributes/password/current',
						]
					);
				}

				$update = new Utils\ArrayHash();
				$update->offsetSet('password', (string) $attributes->get('password')->get('new'));

				// Update item in database
				$this->identitiesManager->update($identity, $update);

			} else {
				throw new NodeJsonApiExceptions\JsonApiErrorException(
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

		} catch (NodeJsonApiExceptions\IJsonApiException $ex) {
			// Revert all changes when error occur
			if ($this->getOrmConnection()->isTransactionActive()) {
				$this->getOrmConnection()->rollBack();
			}

			throw $ex;

		} catch (Throwable $ex) {
			// Revert all changes when error occur
			if ($this->getOrmConnection()->isTransactionActive()) {
				$this->getOrmConnection()->rollBack();
			}

			// Log catched exception
			$this->logger->error('[CONTROLLER] ' . $ex->getMessage(), [
				'exception' => [
					'message' => $ex->getMessage(),
					'code'    => $ex->getCode(),
				],
			]);

			throw new NodeJsonApiExceptions\JsonApiErrorException(
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
	 * @throws NodeJsonApiExceptions\IJsonApiException
	 *
	 * @Secured
	 * @Secured\User(loggedIn)
	 */
	public function readRelationship(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		$identity = $this->findIdentity($request, $this->findAccount());

		// & relation entity name
		$relationEntity = strtolower($request->getAttribute(Router\Router::RELATION_ENTITY));

		if ($relationEntity === Schemas\Identities\IdentitySchema::RELATIONSHIPS_ACCOUNT) {
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
	 * @throws NodeJsonApiExceptions\IJsonApiException
	 * @throws Doctrine\DBAL\ConnectionException
	 *
	 * @Secured
	 * @Secured\User(guest)
	 */
	public function requestPassword(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		$document = $this->createDocument($request);

		$attributes = $document->getResource()->getAttributes();

		if (!$attributes->has('uid')) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('//node.base.messages.missingRequired.heading'),
				$this->translator->translate('//node.base.messages.missingRequired.message'),
				[
					'pointer' => '/data/attributes/uid',
				]
			);
		}

		if ($document->getResource()->getType() !== Schemas\Identities\UserAccountIdentitySchema::SCHEMA_TYPE) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('messages.invalidType.heading'),
				$this->translator->translate('messages.invalidType.message'),
				[
					'pointer' => '/data/type',
				]
			);
		}

		$findQuery = new Queries\FindIdentitiesQuery();
		$findQuery->byUid($attributes->get('uid'));

		$identity = $this->identityRepository->findOneBy($findQuery);

		if ($identity === null) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_NOT_FOUND,
				$this->translator->translate('messages.notFound.heading'),
				$this->translator->translate('messages.notFound.message'),
				[
					'pointer' => '/data/attributes/uid',
				]
			);
		}

		$account = $identity->getAccount();

		if (!$account instanceof Entities\Accounts\IUserAccount) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_NOT_FOUND,
				$this->translator->translate('messages.notFound.heading'),
				$this->translator->translate('messages.notFound.message'),
				[
					'pointer' => '/data/attributes/uid',
				]
			);
		}

		if ($account->isDeleted()) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
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

			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('messages.notActivated.heading'),
				$this->translator->translate('messages.notActivated.message'),
				[
					'pointer' => '/data/attributes/uid',
				]
			);

		} elseif ($account->isBlocked()) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
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

		} catch (NodeJsonApiExceptions\IJsonApiException $ex) {
			// Revert all changes when error occur
			if ($this->getOrmConnection()->isTransactionActive()) {
				$this->getOrmConnection()->rollBack();
			}

			throw $ex;

		} catch (Throwable $ex) {
			// Revert all changes when error occur
			if ($this->getOrmConnection()->isTransactionActive()) {
				$this->getOrmConnection()->rollBack();
			}

			// Log catched exception
			$this->logger->error('[CONTROLLER] ' . $ex->getMessage(), [
				'exception' => [
					'message' => $ex->getMessage(),
					'code'    => $ex->getCode(),
				],
			]);

			throw new NodeJsonApiExceptions\JsonApiErrorException(
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
	 * @return Entities\Accounts\IAccount
	 *
	 * @throws NodeJsonApiExceptions\JsonApiErrorException
	 */
	private function findAccount(): Entities\Accounts\IAccount
	{
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

}
