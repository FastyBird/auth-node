<?php declare(strict_types = 1);

/**
 * PrivilegesV1Controller.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Controllers
 * @since          0.1.0
 *
 * @date           02.04.20
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
 * ACL privileges controller
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Controllers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class PrivilegesV1Controller extends BaseV1Controller
{

	/** @var Hydrators\Privileges\PrivilegeHydrator */
	private $privilegeHydrator;

	/** @var Models\Privileges\IPrivilegeRepository */
	private $privilegeRepository;

	/** @var Models\Privileges\IPrivilegesManager */
	private $privilegesManager;

	/** @var string */
	protected $translationDomain = 'node.privileges';

	/**
	 * @param Models\Privileges\IPrivilegeRepository $privilegeRepository
	 * @param Models\Privileges\IPrivilegesManager $privilegesManager
	 * @param Hydrators\Privileges\PrivilegeHydrator $privilegeHydrator
	 */
	public function __construct(
		Models\Privileges\IPrivilegeRepository $privilegeRepository,
		Models\Privileges\IPrivilegesManager $privilegesManager,
		Hydrators\Privileges\PrivilegeHydrator $privilegeHydrator
	) {
		$this->privilegeRepository = $privilegeRepository;
		$this->privilegesManager = $privilegesManager;
		$this->privilegeHydrator = $privilegeHydrator;
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
		$findQuery = new Queries\FindPrivilegesQuery();

		$privileges = $this->privilegeRepository->getResultSet($findQuery);

		return $response
			->withEntity(NodeWebServerHttp\ScalarEntity::from($privileges));
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
		$privilege = $this->findPrivilege($request->getAttribute(Router\Router::URL_ITEM_ID));

		return $response
			->withEntity(NodeWebServerHttp\ScalarEntity::from($privilege));
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

		if ($document->getResource()->getType() === Schemas\Privileges\PrivilegeSchema::SCHEMA_TYPE) {
			try {
				// Start transaction connection to the database
				$this->getOrmConnection()->beginTransaction();

				$privilege = $this->privilegesManager->create($this->privilegeHydrator->hydrate($document));

				// Commit all changes into database
				$this->getOrmConnection()->commit();

			} catch (NodeJsonApiExceptions\IJsonApiException $ex) {
				// Revert all changes when error occur
				$this->getOrmConnection()->rollBack();

				throw $ex;

			} catch (DoctrineCrudExceptions\EntityCreationException | DoctrineCrudExceptions\MissingRequiredFieldException $ex) {
				// Revert all changes when error occur
				$this->getOrmConnection()->rollBack();

				$pointer = 'data/attributes/' . $ex->getField();

				throw new NodeJsonApiExceptions\JsonApiErrorException(
					StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
					$this->translator->translate('//node.base.messages.missingRequired.heading'),
					$this->translator->translate('//node.base.messages.missingRequired.message'),
					[
						'pointer' => $pointer,
					]
				);

			} catch (Doctrine\DBAL\Exception\UniqueConstraintViolationException $ex) {
				// Revert all changes when error occur
				$this->getOrmConnection()->rollBack();

				if (
					preg_match("%key '(?P<key>.+)_unique'%", $ex->getMessage(), $match) !== false
					&& array_key_exists('key', $match)
				) {
					if (Utils\Strings::startsWith($match['key'], 'privilege_')) {
						throw new NodeJsonApiExceptions\JsonApiErrorException(
							StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
							$this->translator->translate('//node.base.messages.uniqueConstraint.heading'),
							$this->translator->translate('//node.base.messages.uniqueConstraint.message'),
							[
								'pointer' => '/data/attributes/' . Utils\Strings::substring($match['key'], 5),
							]
						);
					}
				}

				throw new NodeJsonApiExceptions\JsonApiErrorException(
					StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
					$this->translator->translate('//node.base.messages.uniqueConstraint.heading'),
					$this->translator->translate('//node.base.messages.uniqueConstraint.message')
				);

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

				throw new NodeJsonApiExceptions\JsonApiErrorException(
					StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
					$this->translator->translate('messages.notCreated.heading'),
					$this->translator->translate('messages.notCreated.message')
				);
			}

			/** @var NodeWebServerHttp\Response $response */
			$response = $response
				->withEntity(NodeWebServerHttp\ScalarEntity::from($privilege))
				->withStatus(StatusCodeInterface::STATUS_CREATED);

			return $response;
		}

		throw new NodeJsonApiExceptions\JsonApiErrorException(
			StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
			$this->translator->translate('messages.invalidType.heading'),
			$this->translator->translate('messages.invalidType.message'),
			[
				'pointer' => '/data/type',
			]
		);
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

		if ($request->getAttribute(Router\Router::URL_ITEM_ID) !== $document->getResource()->getIdentifier()->getId()) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_BAD_REQUEST,
				$this->translator->translate('//node.base.messages.identifierInvalid.heading'),
				$this->translator->translate('//node.base.messages.identifierInvalid.message')
			);
		}

		$privilege = $this->findPrivilege($request->getAttribute(Router\Router::URL_ITEM_ID));

		try {
			// Start transaction connection to the database
			$this->getOrmConnection()->beginTransaction();

			if ($document->getResource()->getType() === Schemas\Privileges\PrivilegeSchema::SCHEMA_TYPE) {
				$updatePrivilegeData = $this->privilegeHydrator->hydrate($document, $privilege);

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

			$privilege = $this->privilegesManager->update($privilege, $updatePrivilegeData);

			// Commit all changes into database
			$this->getOrmConnection()->commit();

		} catch (NodeJsonApiExceptions\IJsonApiException $ex) {
			// Revert all changes when error occur
			$this->getOrmConnection()->rollBack();

			throw $ex;

		} catch (Doctrine\DBAL\Exception\UniqueConstraintViolationException $ex) {
			// Revert all changes when error occur
			$this->getOrmConnection()->rollBack();

			if (
				preg_match("%key '(?P<key>.+)_unique'%", $ex->getMessage(), $match) !== false
				&& array_key_exists('key', $match)
			) {
				if (Utils\Strings::startsWith($match['key'], 'privilege_')) {
					throw new NodeJsonApiExceptions\JsonApiErrorException(
						StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
						$this->translator->translate('//node.base.messages.uniqueConstraint.heading'),
						$this->translator->translate('//node.base.messages.uniqueConstraint.message'),
						[
							'pointer' => '/data/attributes/' . Utils\Strings::substring($match['key'], 5),
						]
					);
				}
			}

			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('//node.base.messages.uniqueConstraint.heading'),
				$this->translator->translate('//node.base.messages.uniqueConstraint.message')
			);

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

			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('messages.notUpdated.heading'),
				$this->translator->translate('messages.notUpdated.message')
			);
		}

		return $response
			->withEntity(NodeWebServerHttp\ScalarEntity::from($privilege));
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
	public function delete(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		$privilege = $this->findPrivilege($request->getAttribute(Router\Router::URL_ITEM_ID));

		try {
			// Start transaction connection to the database
			$this->getOrmConnection()->beginTransaction();

			// Move device back into warehouse
			$this->privilegesManager->delete($privilege);

			// Commit all changes into database
			$this->getOrmConnection()->commit();

		} catch (NodeJsonApiExceptions\IJsonApiException $ex) {
			// Revert all changes when error occur
			$this->getOrmConnection()->rollBack();

			throw $ex;

		} catch (Throwable $ex) {
			// Log catched exception
			$this->logger->error('[CONTROLLER] ' . $ex->getMessage(), [
				'exception' => [
					'message' => $ex->getMessage(),
					'code'    => $ex->getCode(),
				],
			]);

			// Revert all changes when error occur
			$this->getOrmConnection()->rollBack();

			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('messages.notDeleted.heading'),
				$this->translator->translate('messages.notDeleted.message')
			);
		}

		/** @var NodeWebServerHttp\Response $response */
		$response = $response->withStatus(StatusCodeInterface::STATUS_NO_CONTENT);

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
		$privilege = $this->findPrivilege($request->getAttribute(Router\Router::URL_ITEM_ID));

		$relationEntity = strtolower($request->getAttribute(Router\Router::RELATION_ENTITY));

		if ($relationEntity === Schemas\Privileges\PrivilegeSchema::RELATIONSHIPS_RESOURCE) {
			return $response
				->withEntity(NodeWebServerHttp\ScalarEntity::from($privilege->getResource()));
		}

		$this->throwUnknownRelation($relationEntity);

		return $response;
	}

	/**
	 * @param string $id
	 *
	 * @return Entities\Privileges\IPrivilege
	 *
	 * @throws NodeJsonApiExceptions\IJsonApiException
	 */
	protected function findPrivilege(string $id): Entities\Privileges\IPrivilege
	{
		try {
			$findQuery = new Queries\FindPrivilegesQuery();
			$findQuery->byId(Uuid\Uuid::fromString($id));

			$privilege = $this->privilegeRepository->findOneBy($findQuery);

			if ($privilege === null) {
				throw new NodeJsonApiExceptions\JsonApiErrorException(
					StatusCodeInterface::STATUS_NOT_FOUND,
					$this->translator->translate('messages.privilegeNotFound.heading'),
					$this->translator->translate('messages.privilegeNotFound.message')
				);
			}

		} catch (Uuid\Exception\InvalidUuidStringException $ex) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_NOT_FOUND,
				$this->translator->translate('messages.privilegeNotFound.heading'),
				$this->translator->translate('messages.privilegeNotFound.message')
			);
		}

		return $privilege;
	}

}
