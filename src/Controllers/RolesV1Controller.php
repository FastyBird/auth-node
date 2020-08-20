<?php declare(strict_types = 1);

/**
 * RolesV1Controller.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Controllers
 * @since          0.1.0
 *
 * @date           02.04.20
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
use Psr\Http\Message;
use Throwable;

/**
 * ACL roles controller
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Controllers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 *
 * @Secured
 * @Secured\Role(manager,administrator)
 */
final class RolesV1Controller extends BaseV1Controller
{

	use Controllers\Finders\TRoleFinder;

	/** @var Hydrators\Roles\RoleHydrator */
	private $roleHydrator;

	/** @var Models\Roles\IRoleRepository */
	private $roleRepository;

	/** @var Models\Roles\IRolesManager */
	private $rolesManager;

	/** @var string */
	protected $translationDomain = 'node.roles';

	public function __construct(
		Models\Roles\IRoleRepository $roleRepository,
		Models\Roles\IRolesManager $rolesManager,
		Hydrators\Roles\RoleHydrator $roleHydrator
	) {
		$this->roleRepository = $roleRepository;
		$this->rolesManager = $rolesManager;
		$this->roleHydrator = $roleHydrator;
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
		$findQuery = new Queries\FindRolesQuery();

		$roles = $this->roleRepository->getResultSet($findQuery);

		return $response
			->withEntity(NodeWebServerHttp\ScalarEntity::from($roles));
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
		$role = $this->findRole($request);

		return $response
			->withEntity(NodeWebServerHttp\ScalarEntity::from($role));
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

		$role = $this->findRole($request);

		$this->validateIdentifier($request, $document);

		try {
			// Start transaction connection to the database
			$this->getOrmConnection()->beginTransaction();

			if ($document->getResource()->getType() === Schemas\Roles\RoleSchema::SCHEMA_TYPE) {
				$updateRoleData = $this->roleHydrator->hydrate($document, $role);

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

			$role = $this->rolesManager->update($role, $updateRoleData);

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

		return $response
			->withEntity(NodeWebServerHttp\ScalarEntity::from($role));
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
		$role = $this->findRole($request);

		$relationEntity = strtolower($request->getAttribute(Router\Router::RELATION_ENTITY));

		if ($relationEntity === Schemas\Roles\RoleSchema::RELATIONSHIPS_PARENT) {
			return $response
				->withEntity(NodeWebServerHttp\ScalarEntity::from($role->getParent()));

		} elseif ($relationEntity === Schemas\Roles\RoleSchema::RELATIONSHIPS_CHILDREN) {
			return $response
				->withEntity(NodeWebServerHttp\ScalarEntity::from($role->getChildren()));
		}

		return parent::readRelationship($request, $response);
	}

}
