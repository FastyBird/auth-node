<?php declare(strict_types = 1);

/**
 * AccountsV1Controller.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Controllers
 * @since          0.1.0
 *
 * @date           21.06.20
 */

namespace FastyBird\AuthNode\Controllers;

use Doctrine;
use FastyBird\AuthNode\Entities;
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
use Ramsey\Uuid;
use Throwable;

/**
 * Accounts controller
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Controllers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 *
 * @Secured
 * @Secured\Role(manager,administrator)
 */
final class AccountsV1Controller extends BaseV1Controller
{

	/** @var Hydrators\Accounts\UserAccountHydrator */
	private $userAccountHydrator;

	/** @var Hydrators\Accounts\MachineAccountHydrator */
	private $machineAccountHydrator;

	/** @var Models\Accounts\IAccountRepository */
	private $accountRepository;

	/** @var Models\Accounts\IAccountsManager */
	private $accountsManager;

	/** @var string */
	protected $translationDomain = 'node.accounts';

	public function __construct(
		Hydrators\Accounts\UserAccountHydrator $userAccountHydrator,
		Hydrators\Accounts\MachineAccountHydrator $machineAccountHydrator,
		Models\Accounts\IAccountRepository $accountRepository,
		Models\Accounts\IAccountsManager $accountsManager
	) {
		$this->userAccountHydrator = $userAccountHydrator;
		$this->machineAccountHydrator = $machineAccountHydrator;

		$this->accountRepository = $accountRepository;
		$this->accountsManager = $accountsManager;
	}

	/**
	 * @param Message\ServerRequestInterface $request
	 * @param NodeWebServerHttp\Response $response
	 *
	 * @return NodeWebServerHttp\Response
	 */
	public function index(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		$findQuery = new Queries\FindAccountsQuery();

		$accounts = $this->accountRepository->getResultSet($findQuery);

		return $response
			->withEntity(NodeWebServerHttp\ScalarEntity::from($accounts));
	}

	/**
	 * @param Message\ServerRequestInterface $request
	 * @param NodeWebServerHttp\Response $response
	 *
	 * @return NodeWebServerHttp\Response
	 *
	 * @throws NodeJsonApiExceptions\IJsonApiException
	 */
	public function read(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		// Find account
		$account = $this->findAccount($request);

		return $response
			->withEntity(NodeWebServerHttp\ScalarEntity::from($account));
	}

	/**
	 * @param Message\ServerRequestInterface $request
	 * @param NodeWebServerHttp\Response $response
	 *
	 * @return NodeWebServerHttp\Response
	 *
	 * @throws NodeJsonApiExceptions\IJsonApiException
	 * @throws Doctrine\DBAL\ConnectionException
	 */
	public function create(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		$document = $this->createDocument($request);

		try {
			// Start transaction connection to the database
			$this->getOrmConnection()->beginTransaction();

			if ($document->getResource()->getType() === Schemas\Accounts\UserAccountSchema::SCHEMA_TYPE) {
				$createData = $this->userAccountHydrator->hydrate($document);

				// Store item into database
				$account = $this->accountsManager->create($createData);

			} elseif ($document->getResource()->getType() === Schemas\Accounts\MachineAccountSchema::SCHEMA_TYPE) {
				$createData = $this->machineAccountHydrator->hydrate($document);

				// Store item into database
				$account = $this->accountsManager->create($createData);

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

			if (preg_match("%'PRIMARY'%", $ex->getMessage(), $match) !== false) {
				throw new NodeJsonApiExceptions\JsonApiErrorException(
					StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
					$this->translator->translate('//node.base.messages.uniqueIdConstraint.heading'),
					$this->translator->translate('//node.base.messages.uniqueIdConstraint.message'),
					[
						'pointer' => '/data/id',
					]
				);

			} elseif (preg_match("%key '(?P<key>.+)_unique'%", $ex->getMessage(), $match) !== false) {
				$columnParts = explode('.', $match['key']);
				$columnKey = end($columnParts);

				if (is_string($columnKey) && Utils\Strings::startsWith($columnKey, 'account_')) {
					throw new NodeJsonApiExceptions\JsonApiErrorException(
						StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
						$this->translator->translate('//node.base.messages.uniqueAttributeConstraint.heading'),
						$this->translator->translate('//node.base.messages.uniqueAttributeConstraint.message'),
						[
							'pointer' => '/data/attributes/' . Utils\Strings::substring($columnKey, 8),
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
			->withEntity(NodeWebServerHttp\ScalarEntity::from($account))
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
	 */
	public function readRelationship(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		// At first, try to load acount
		$account = $this->findAccount($request);

		// & relation entity name
		$relationEntity = strtolower($request->getAttribute(Router\Router::RELATION_ENTITY));

		if ($account instanceof Entities\Accounts\IUserAccount) {
			if ($relationEntity === Schemas\Accounts\UserAccountSchema::RELATIONSHIPS_EMAILS) {
				return $response
					->withEntity(NodeWebServerHttp\ScalarEntity::from($account->getEmails()));

			} elseif ($relationEntity === Schemas\Accounts\UserAccountSchema::RELATIONSHIPS_IDENTITIES) {
				return $response
					->withEntity(NodeWebServerHttp\ScalarEntity::from($account->getIdentities()));

			} elseif ($relationEntity === Schemas\Accounts\UserAccountSchema::RELATIONSHIPS_ROLES) {
				return $response
					->withEntity(NodeWebServerHttp\ScalarEntity::from($account->getRoles()));
			}

		} elseif ($account instanceof Entities\Accounts\IMachineAccount) {
			if ($relationEntity === Schemas\Accounts\MachineAccountSchema::RELATIONSHIPS_PARENT) {
				return $response
					->withEntity(NodeWebServerHttp\ScalarEntity::from($account->getParent()));

			} elseif ($relationEntity === Schemas\Accounts\MachineAccountSchema::RELATIONSHIPS_CHILDREN) {
				return $response
					->withEntity(NodeWebServerHttp\ScalarEntity::from($account->getChildren()));

			} elseif ($relationEntity === Schemas\Accounts\MachineAccountSchema::RELATIONSHIPS_IDENTITIES) {
				return $response
					->withEntity(NodeWebServerHttp\ScalarEntity::from($account->getIdentities()));
			}
		}

		$this->throwUnknownRelation($relationEntity);

		return $response;
	}

	/**
	 * @param Message\ServerRequestInterface $request
	 *
	 * @return Entities\Accounts\IAccount
	 *
	 * @throws NodeJsonApiExceptions\IJsonApiException
	 */
	private function findAccount(
		Message\ServerRequestInterface $request
	): Entities\Accounts\IAccount {
		if (!Uuid\Uuid::isValid($request->getAttribute(Router\Router::URL_ITEM_ID, null))) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_NOT_FOUND,
				$this->translator->translate('messages.notFound.heading'),
				$this->translator->translate('messages.notFound.message')
			);
		}

		$findQuery = new Queries\FindAccountsQuery();
		$findQuery->byId(Uuid\Uuid::fromString($request->getAttribute(Router\Router::URL_ITEM_ID, null)));

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
