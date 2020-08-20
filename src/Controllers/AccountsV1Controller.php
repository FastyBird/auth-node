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
use FastyBird\AuthNode\Exceptions;
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
					$this->translator->translate('//node.base.messages.invalidType.heading'),
					$this->translator->translate('//node.base.messages.invalidType.message'),
					[
						'pointer' => '/data/type',
					]
				);
			}

			// Commit all changes into database
			$this->getOrmConnection()->commit();

		} catch (Exceptions\RelationEntityRequired $ex) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('//node.base.messages.missingRelation.heading'),
				$this->translator->translate('//node.base.messages.missingRelation.message'),
				[
					'pointer' => '/data/relationships/parent/data/id',
				]
			);

		} catch (Exceptions\ParentInvalidException | Exceptions\ParentWithParentException $ex) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('//node.base.messages.invalidRelation.heading'),
				$this->translator->translate('//node.base.messages.invalidRelation.message'),
				[
					'pointer' => '/data/relationships/parent/data/id',
				]
			);

		} catch (Exceptions\AccountRoleInvalidException $ex) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('//node.base.messages.invalidRelation.heading'),
				$this->translator->translate('//node.base.messages.invalidRelation.message'),
				[
					'pointer' => '/data/relationships/roles/data/id',
				]
			);

		} catch (DoctrineCrudExceptions\EntityCreationException $ex) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('//node.base.messages.missingAttribute.heading'),
				$this->translator->translate('//node.base.messages.missingAttribute.message'),
				[
					'pointer' => 'data/attributes/' . $ex->getField(),
				]
			);

		} catch (NodeJsonApiExceptions\IJsonApiException $ex) {
			throw $ex;

		} catch (Doctrine\DBAL\Exception\UniqueConstraintViolationException $ex) {
			if (preg_match("%PRIMARY'%", $ex->getMessage(), $match) === 1) {
				throw new NodeJsonApiExceptions\JsonApiErrorException(
					StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
					$this->translator->translate('//node.base.messages.uniqueIdentifier.heading'),
					$this->translator->translate('//node.base.messages.uniqueIdentifier.message'),
					[
						'pointer' => '/data/id',
					]
				);

			} elseif (preg_match("%key '(?P<key>.+)_unique'%", $ex->getMessage(), $match) === 1) {
				$columnParts = explode('.', $match['key']);
				$columnKey = end($columnParts);

				if (is_string($columnKey) && Utils\Strings::startsWith($columnKey, 'account_')) {
					throw new NodeJsonApiExceptions\JsonApiErrorException(
						StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
						$this->translator->translate('//node.base.messages.uniqueAttribute.heading'),
						$this->translator->translate('//node.base.messages.uniqueAttribute.message'),
						[
							'pointer' => '/data/attributes/' . Utils\Strings::substring($columnKey, 8),
						]
					);
				}
			}

			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('//node.base.messages.uniqueAttribute.heading'),
				$this->translator->translate('//node.base.messages.uniqueAttribute.message')
			);

		} catch (Throwable $ex) {
			// Log catched exception
			$this->logger->error('[CONTROLLER] ' . $ex->getMessage(), [
				'exception' => [
					'message' => $ex->getMessage(),
					'code'    => $ex->getCode(),
				],
			]);

			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('//node.base.messages.notCreated.heading'),
				$this->translator->translate('//node.base.messages.notCreated.message')
			);

		} finally {
			// Revert all changes when error occur
			if ($this->getOrmConnection()->isTransactionActive()) {
				$this->getOrmConnection()->rollBack();
			}
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
	 * @throws Doctrine\DBAL\ConnectionException
	 */
	public function update(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		$document = $this->createDocument($request);

		$account = $this->findAccount($request);

		$this->validateIdentifier($request, $document);

		try {
			// Start transaction connection to the database
			$this->getOrmConnection()->beginTransaction();

			if (
				$document->getResource()->getType() === Schemas\Accounts\UserAccountSchema::SCHEMA_TYPE
				&& $account instanceof Entities\Accounts\IUserAccount
			) {
				$updateAccountData = $this->userAccountHydrator->hydrate($document, $account);

				$account = $this->accountsManager->update($account, $updateAccountData);

			} elseif (
				$document->getResource()->getType() === Schemas\Accounts\MachineAccountSchema::SCHEMA_TYPE
				&& $account instanceof Entities\Accounts\IMachineAccount
			) {
				$updateAccountData = $this->machineAccountHydrator->hydrate($document, $account);

				$account = $this->accountsManager->update($account, $updateAccountData);

			} else {
				throw new NodeJsonApiExceptions\JsonApiErrorException(
					StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
					$this->translator->translate('//node.base.messages.invalidType.heading'),
					$this->translator->translate('//node.base.messages.invalidType.message'),
					[
						'pointer' => '/data/type',
					]
				);
			}

			// Commit all changes into database
			$this->getOrmConnection()->commit();

		} catch (NodeJsonApiExceptions\IJsonApiException $ex) {
			throw $ex;

		} catch (Exceptions\ParentInvalidException | Exceptions\ParentWithParentException $ex) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('//node.base.messages.invalidRelation.heading'),
				$this->translator->translate('//node.base.messages.invalidRelation.message'),
				[
					'pointer' => '/data/relationships/parent/data/id',
				]
			);

		} catch (Exceptions\AccountRoleInvalidException $ex) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('//node.base.messages.invalidRelation.heading'),
				$this->translator->translate('//node.base.messages.invalidRelation.message'),
				[
					'pointer' => '/data/relationships/roles/data/id',
				]
			);

		} catch (Throwable $ex) {
			// Log catched exception
			$this->logger->error('[CONTROLLER] ' . $ex->getMessage(), [
				'exception' => [
					'message' => $ex->getMessage(),
					'code'    => $ex->getCode(),
				],
			]);

			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('//node.base.messages.notUpdated.heading'),
				$this->translator->translate('//node.base.messages.notUpdated.message')
			);

		} finally {
			// Revert all changes when error occur
			if ($this->getOrmConnection()->isTransactionActive()) {
				$this->getOrmConnection()->rollBack();
			}
		}

		/** @var NodeWebServerHttp\Response $response */
		$response = $response
			->withEntity(NodeWebServerHttp\ScalarEntity::from($account));

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

		return parent::readRelationship($request, $response);
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
				$this->translator->translate('//node.base.messages.notFound.heading'),
				$this->translator->translate('//node.base.messages.notFound.message')
			);
		}

		$findQuery = new Queries\FindAccountsQuery();
		$findQuery->byId(Uuid\Uuid::fromString($request->getAttribute(Router\Router::URL_ITEM_ID, null)));

		$account = $this->accountRepository->findOneBy($findQuery);

		if ($account === null) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_NOT_FOUND,
				$this->translator->translate('//node.base.messages.notFound.heading'),
				$this->translator->translate('//node.base.messages.notFound.message')
			);
		}

		return $account;
	}

}
