<?php declare(strict_types = 1);

/**
 * ResourcesV1Controller.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Controllers
 * @since          0.1.0
 *
 * @date           13.06.20
 */

namespace FastyBird\AuthNode\Controllers;

use Doctrine;
use FastyBird\AuthNode\Controllers;
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
 * ACL resources controller
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Controllers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class ResourcesV1Controller extends BaseV1Controller
{

	use Controllers\Finders\TResourceFinder;

	/** @var Hydrators\Resources\ResourceHydrator */
	private $resourceHydrator;

	/** @var Models\Resources\IResourceRepository */
	private $resourceRepository;

	/** @var Models\Resources\IResourcesManager */
	private $resourcesManager;

	/** @var string */
	protected $translationDomain = 'node.resources';

	/**
	 * @param Models\Resources\IResourceRepository $resourceRepository
	 * @param Models\Resources\IResourcesManager $resourcesManager
	 * @param Hydrators\Resources\ResourceHydrator $resourceHydrator
	 */
	public function __construct(
		Models\Resources\IResourceRepository $resourceRepository,
		Models\Resources\IResourcesManager $resourcesManager,
		Hydrators\Resources\ResourceHydrator $resourceHydrator
	) {
		$this->resourceRepository = $resourceRepository;
		$this->resourcesManager = $resourcesManager;
		$this->resourceHydrator = $resourceHydrator;
	}

	/**
	 * @param Message\ServerRequestInterface $request
	 * @param NodeWebServerHttp\Response $response
	 *
	 * @return NodeWebServerHttp\Response
	 *
	 * @Secured
	 * @Secured\Permission(manage-access-control:read)
	 */
	public function index(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		$findQuery = new Queries\FindResourcesQuery();

		$resources = $this->resourceRepository->getResultSet($findQuery);

		return $response
			->withEntity(NodeWebServerHttp\ScalarEntity::from($resources));
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
	 * @Secured\Permission(manage-access-control:read)
	 */
	public function read(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		$resource = $this->findResource($request->getAttribute(Router\Router::URL_ITEM_ID));

		return $response
			->withEntity(NodeWebServerHttp\ScalarEntity::from($resource));
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
	 * @Secured\Permission(manage-access-control:create)
	 */
	public function create(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		$document = $this->createDocument($request);

		if ($document->getResource()->getType() === Schemas\Resources\ResourceSchema::SCHEMA_TYPE) {
			try {
				// Start transaction connection to the database
				$this->getOrmConnection()->beginTransaction();

				$resource = $this->resourcesManager->create($this->resourceHydrator->hydrate($document));

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
					if (Utils\Strings::startsWith($match['key'], 'resource_')) {
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
				->withEntity(NodeWebServerHttp\ScalarEntity::from($resource))
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
	 *
	 * @Secured
	 * @Secured\Permission(manage-access-control:update)
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

		$resource = $this->findResource($request->getAttribute(Router\Router::URL_ITEM_ID));

		try {
			// Start transaction connection to the database
			$this->getOrmConnection()->beginTransaction();

			if ($document->getResource()->getType() === Schemas\Resources\ResourceSchema::SCHEMA_TYPE) {
				$updateResourceData = $this->resourceHydrator->hydrate($document, $resource);

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

			$resource = $this->resourcesManager->update($resource, $updateResourceData);

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
				if (Utils\Strings::startsWith($match['key'], 'resource_')) {
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
			->withEntity(NodeWebServerHttp\ScalarEntity::from($resource));
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
	 * @Secured\Permission(manage-access-control:read)
	 */
	public function readRelationship(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		$resource = $this->findResource($request->getAttribute(Router\Router::URL_ITEM_ID));

		$relationEntity = strtolower($request->getAttribute(Router\Router::RELATION_ENTITY));

		if ($relationEntity === Schemas\Resources\ResourceSchema::RELATIONSHIPS_PARENT) {
			return $response
				->withEntity(NodeWebServerHttp\ScalarEntity::from($resource->getParent()));

		} elseif ($relationEntity === Schemas\Resources\ResourceSchema::RELATIONSHIPS_CHILDREN) {
			return $response
				->withEntity(NodeWebServerHttp\ScalarEntity::from($resource->getChildren()));

		} elseif ($relationEntity === Schemas\Resources\ResourceSchema::RELATIONSHIPS_PRIVILEGES) {
			return $response
				->withEntity(NodeWebServerHttp\ScalarEntity::from($resource->getPrivileges()));
		}

		$this->throwUnknownRelation($relationEntity);

		return $response;
	}

}
