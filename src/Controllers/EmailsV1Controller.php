<?php declare(strict_types = 1);

/**
 * EmailsV1Controller.php
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

namespace FastyBird\AuthNode\Controllers;

use Doctrine;
use FastyBird\AuthNode\Controllers;
use FastyBird\AuthNode\Exceptions;
use FastyBird\AuthNode\Helpers;
use FastyBird\AuthNode\Hydrators;
use FastyBird\AuthNode\Models;
use FastyBird\AuthNode\Queries;
use FastyBird\AuthNode\Router;
use FastyBird\AuthNode\Schemas;
use FastyBird\NodeJsonApi\Exceptions as NodeJsonApiExceptions;
use FastyBird\NodeWebServer\Http as NodeWebServerHttp;
use Fig\Http\Message\StatusCodeInterface;
use IPub\DoctrineCrud\Exceptions as DoctrineCrudExceptions;
use Nette\Utils;
use Psr\Http\Message;
use Throwable;

/**
 * Emails controller
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Controllers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class EmailsV1Controller extends BaseV1Controller
{

	use Controllers\Finders\TAccountFinder;
	use Controllers\Finders\TEmailFinder;

	/** @var Hydrators\Emails\EmailHydrator */
	private $emailHydrator;

	/** @var Models\Emails\IEmailsManager */
	private $emailsManager;

	/** @var Helpers\SecurityHash */
	private $securityHash;

	/** @var Models\Emails\IEmailRepository */
	protected $emailRepository;

	/** @var Models\Accounts\IAccountRepository */
	protected $accountRepository;

	/** @var string */
	protected $translationDomain = 'node.emails';

	public function __construct(
		Hydrators\Emails\EmailHydrator $emailHydrator,
		Models\Emails\IEmailRepository $emailRepository,
		Models\Emails\IEmailsManager $emailsManager,
		Models\Accounts\IAccountRepository $accountRepository,
		Helpers\SecurityHash $securityHash
	) {
		$this->emailHydrator = $emailHydrator;

		$this->emailRepository = $emailRepository;
		$this->emailsManager = $emailsManager;

		$this->accountRepository = $accountRepository;

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
	 * @Secured\Role(manager,administrator)
	 */
	public function index(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		$findQuery = new Queries\FindEmailsQuery();
		$findQuery->forAccount($this->findAccount($request));

		$emails = $this->emailRepository->getResultSet($findQuery);

		return $response
			->withEntity(NodeWebServerHttp\ScalarEntity::from($emails));
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
	 * @Secured\Role(manager,administrator)
	 */
	public function read(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		// Find email
		$email = $this->findEmail($request, $this->findAccount($request));

		return $response
			->withEntity(NodeWebServerHttp\ScalarEntity::from($email));
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
	 * @Secured\Role(manager,administrator)
	 */
	public function create(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		// Get user profile account or url defined account
		$account = $this->findAccount($request);

		$document = $this->createDocument($request);

		try {
			// Start transaction connection to the database
			$this->getOrmConnection()->beginTransaction();

			if ($document->getResource()->getType() === Schemas\Emails\EmailSchema::SCHEMA_TYPE) {
				$createData = $this->emailHydrator->hydrate($document);
				$createData->offsetSet('account', $account);
				$createData->offsetSet('verificationHash', $this->securityHash->createKey());
				$createData->offsetSet('verificationCreated', $this->dateFactory->getNow());

				// Store item into database
				$email = $this->emailsManager->create($createData);

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

		} catch (Exceptions\EmailIsNotValidException $ex) {
			// Revert all changes when error occur
			if ($this->getOrmConnection()->isTransactionActive()) {
				$this->getOrmConnection()->rollBack();
			}

			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('messages.notValid.heading'),
				$this->translator->translate('messages.notValid.message'),
				[
					'pointer' => '/data/attributes/address',
				]
			);

		} catch (Exceptions\EmailAlreadyTakenException $ex) {
			// Revert all changes when error occur
			if ($this->getOrmConnection()->isTransactionActive()) {
				$this->getOrmConnection()->rollBack();
			}

			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('messages.taken.heading'),
				$this->translator->translate('messages.taken.message'),
				[
					'pointer' => '/data/attributes/address',
				]
			);

		} catch (DoctrineCrudExceptions\EntityCreationException $ex) {
			// Revert all changes when error occur
			if ($this->getOrmConnection()->isTransactionActive()) {
				$this->getOrmConnection()->rollBack();
			}

			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('//node.base.messages.missingRequired.heading'),
				$this->translator->translate('//node.base.messages.missingRequired.message'),
				[
					'pointer' => 'data/attributes/' . $ex->getField(),
				]
			);

		} catch (NodeJsonApiExceptions\IJsonApiException $ex) {
			// Revert all changes when error occur
			if ($this->getOrmConnection()->isTransactionActive()) {
				$this->getOrmConnection()->rollBack();
			}

			throw $ex;

		} catch (Doctrine\DBAL\Exception\UniqueConstraintViolationException $ex) {
			// Revert all changes when error occur
			if ($this->getOrmConnection()->isTransactionActive()) {
				$this->getOrmConnection()->rollBack();
			}

			if (preg_match("%PRIMARY'%", $ex->getMessage(), $match) === 1) {
				throw new NodeJsonApiExceptions\JsonApiErrorException(
					StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
					$this->translator->translate('//node.base.messages.uniqueIdConstraint.heading'),
					$this->translator->translate('//node.base.messages.uniqueIdConstraint.message'),
					[
						'pointer' => '/data/id',
					]
				);

			} elseif (preg_match("%key '(?P<key>.+)_unique'%", $ex->getMessage(), $match) === 1) {
				$columnParts = explode('.', $match['key']);
				$columnKey = end($columnParts);

				if (is_string($columnKey) && Utils\Strings::startsWith($columnKey, 'email_')) {
					throw new NodeJsonApiExceptions\JsonApiErrorException(
						StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
						$this->translator->translate('//node.base.messages.uniqueAttributeConstraint.heading'),
						$this->translator->translate('//node.base.messages.uniqueAttributeConstraint.message'),
						[
							'pointer' => '/data/attributes/' . Utils\Strings::substring($columnKey, 6),
						]
					);
				}
			}

			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('//node.base.messages.uniqueAttributeConstraint.heading'),
				$this->translator->translate('//node.base.messages.uniqueAttributeConstraint.message')
			);

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
				$this->translator->translate('messages.notCreated.heading'),
				$this->translator->translate('messages.notCreated.message')
			);
		}

		/** @var NodeWebServerHttp\Response $response */
		$response = $response
			->withEntity(NodeWebServerHttp\ScalarEntity::from($email))
			->withStatus(StatusCodeInterface::STATUS_CREATED);

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
	 * @Secured\Role(manager,administrator)
	 */
	public function update(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		$document = $this->createDocument($request);

		$email = $this->findEmail($request, $this->findAccount($request));

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

			if ($document->getResource()->getType() === Schemas\Emails\EmailSchema::SCHEMA_TYPE) {
				$updateEmailData = $this->emailHydrator->hydrate($document, $email);

				$email = $this->emailsManager->update($email, $updateEmailData);

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
			->withEntity(NodeWebServerHttp\ScalarEntity::from($email));

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
	 * @Secured\Role(manager,administrator)
	 */
	public function delete(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		$email = $this->findEmail($request, $this->findAccount($request));

		if ($email->isDefault()) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('messages.defaultNotDeletable.heading'),
				$this->translator->translate('messages.defaultNotDeletable.message')
			);
		}

		try {
			// Start transaction connection to the database
			$this->getOrmConnection()->beginTransaction();

			$this->emailsManager->delete($email);

			// Commit all changes into database
			$this->getOrmConnection()->commit();

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
				$this->translator->translate('messages.notDeleted.heading'),
				$this->translator->translate('messages.notDeleted.message')
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
	 * @Secured\Role(manager,administrator)
	 */
	public function readRelationship(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		// At first, try to load email
		$email = $this->findEmail($request, $this->findAccount($request));

		// & relation entity name
		$relationEntity = strtolower($request->getAttribute(Router\Router::RELATION_ENTITY));

		if ($relationEntity === Schemas\Emails\EmailSchema::RELATIONSHIPS_ACCOUNT) {
			return $response
				->withEntity(NodeWebServerHttp\ScalarEntity::from($email->getAccount()));
		}

		$this->throwUnknownRelation($relationEntity);

		return $response;
	}

}
