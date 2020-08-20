<?php declare(strict_types = 1);

/**
 * IdentitiesV1Controller.php
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
use Throwable;

/**
 * Account identity controller
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Controllers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 *
 * @Secured
 * @Secured\Role(manager,administrator)
 */
final class IdentitiesV1Controller extends BaseV1Controller
{

	use Controllers\Finders\TAccountFinder;
	use Controllers\Finders\TIdentityFinder;

	/** @var Hydrators\Identities\UserAccountIdentityHydrator */
	private $userAccountIdentityHydrator;

	/** @var Hydrators\Identities\MachineAccountIdentityHydrator */
	private $machineAccountIdentityHydrator;

	/** @var Models\Identities\IIdentitiesManager */
	private $identitiesManager;

	/** @var Models\Identities\IIdentityRepository */
	protected $identityRepository;

	/** @var Models\Accounts\IAccountRepository */
	protected $accountRepository;

	/** @var string */
	protected $translationDomain = 'node.identities';

	public function __construct(
		Hydrators\Identities\UserAccountIdentityHydrator $userAccountIdentityHydrator,
		Hydrators\Identities\MachineAccountIdentityHydrator $machineAccountIdentityHydrator,
		Models\Identities\IIdentityRepository $identityRepository,
		Models\Identities\IIdentitiesManager $identitiesManager,
		Models\Accounts\IAccountRepository $accountRepository
	) {
		$this->userAccountIdentityHydrator = $userAccountIdentityHydrator;
		$this->machineAccountIdentityHydrator = $machineAccountIdentityHydrator;
		$this->identityRepository = $identityRepository;
		$this->identitiesManager = $identitiesManager;

		$this->accountRepository = $accountRepository;
	}

	/**
	 * @param Message\ServerRequestInterface $request
	 * @param NodeWebServerHttp\Response $response
	 *
	 * @return NodeWebServerHttp\Response
	 *
	 * @throws NodeJsonApiExceptions\IJsonApiException
	 */
	public function index(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		$findQuery = new Queries\FindIdentitiesQuery();
		$findQuery->forAccount($this->findAccount($request));

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
	 */
	public function read(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		// Find identity
		$identity = $this->findIdentity($request, $this->findAccount($request));

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

			if ($document->getResource()->getType() === Schemas\Identities\UserAccountIdentitySchema::SCHEMA_TYPE) {
				$createData = $this->userAccountIdentityHydrator->hydrate($document);
				$createData->offsetSet('account', $account);

				// Store item into database
				$identity = $this->identitiesManager->create($createData);

			} elseif ($document->getResource()->getType() === Schemas\Identities\MachineAccountIdentitySchema::SCHEMA_TYPE) {
				$createData = $this->machineAccountIdentityHydrator->hydrate($document);
				$createData->offsetSet('account', $account);

				// Store item into database
				$identity = $this->identitiesManager->create($createData);

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

				if (is_string($columnKey) && Utils\Strings::startsWith($columnKey, 'identity_')) {
					throw new NodeJsonApiExceptions\JsonApiErrorException(
						StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
						$this->translator->translate('//node.base.messages.uniqueAttribute.heading'),
						$this->translator->translate('//node.base.messages.uniqueAttribute.message'),
						[
							'pointer' => '/data/attributes/' . Utils\Strings::substring($columnKey, 9),
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
			->withEntity(NodeWebServerHttp\ScalarEntity::from($identity))
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

		$identity = $this->findIdentity($request, $this->findAccount($request));

		$this->validateIdentifier($request, $document);

		try {
			// Start transaction connection to the database
			$this->getOrmConnection()->beginTransaction();

			if (
				$document->getResource()->getType() === Schemas\Identities\UserAccountIdentitySchema::SCHEMA_TYPE
				&& $identity instanceof Entities\Identities\IUserAccountIdentity
			) {
				$updateIdentityData = $this->userAccountIdentityHydrator->hydrate($document, $identity);

				// Update item in database
				$identity = $this->identitiesManager->update($identity, $updateIdentityData);

			} elseif (
				$document->getResource()->getType() === Schemas\Identities\MachineAccountIdentitySchema::SCHEMA_TYPE
				&& $identity instanceof Entities\Identities\IMachineAccountIdentity
			) {
				$updateIdentityData = $this->machineAccountIdentityHydrator->hydrate($document, $identity);

				// Update item in database
				$identity = $this->identitiesManager->update($identity, $updateIdentityData);

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
			->withEntity(NodeWebServerHttp\ScalarEntity::from($identity));

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
		$identity = $this->findIdentity($request, $this->findAccount($request));

		// & relation entity name
		$relationEntity = strtolower($request->getAttribute(Router\Router::RELATION_ENTITY));

		if ($relationEntity === Schemas\Identities\IdentitySchema::RELATIONSHIPS_ACCOUNT) {
			return $response
				->withEntity(NodeWebServerHttp\ScalarEntity::from($identity->getAccount()));
		}

		return parent::readRelationship($request, $response);
	}

}
