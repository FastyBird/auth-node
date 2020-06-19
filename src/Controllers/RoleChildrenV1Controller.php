<?php declare(strict_types = 1);

/**
 * RoleChildrenV1Controller.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Controllers
 * @since          0.1.0
 *
 * @date           03.06.20
 */

namespace FastyBird\AuthNode\Controllers;

use FastyBird\AuthNode\Controllers;
use FastyBird\AuthNode\Models;
use FastyBird\AuthNode\Queries;
use FastyBird\AuthNode\Router;
use FastyBird\NodeJsonApi\Exceptions as NodeJsonApiExceptions;
use FastyBird\NodeWebServer\Http as NodeWebServerHttp;
use Psr\Http\Message;

/**
 * Role children API controller
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Controllers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class RoleChildrenV1Controller extends BaseV1Controller
{

	use Controllers\Finders\TRoleFinder;

	/** @var Models\Roles\IRoleRepository */
	private $roleRepository;

	/** @var string */
	protected $translationDomain = 'node.roles';

	public function __construct(
		Models\Roles\IRoleRepository $roleRepository
	) {
		$this->roleRepository = $roleRepository;
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
	public function index(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		// At first, try to load role
		$role = $this->findRole($request->getAttribute(Router\Router::URL_ITEM_ID));

		$findQuery = new Queries\FindRolesQuery();
		$findQuery->forParent($role);

		$children = $this->roleRepository->getResultSet($findQuery);

		return $response
			->withEntity(NodeWebServerHttp\ScalarEntity::from($children));
	}

}
