<?php declare(strict_types = 1);

/**
 * RoleChildrenV1Controller.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AccountsNode!
 * @subpackage     Controllers
 * @since          0.1.0
 *
 * @date           03.06.20
 */

namespace FastyBird\AccountsNode\Controllers;

use FastyBird\AccountsNode\Controllers;
use FastyBird\AccountsNode\Models;
use FastyBird\AccountsNode\Queries;
use FastyBird\AccountsNode\Router;
use FastyBird\NodeWebServer\Exceptions as NodeWebServerExceptions;
use FastyBird\NodeWebServer\Http as NodeWebServerHttp;
use Psr\Http\Message;

/**
 * Role children API controller
 *
 * @package        FastyBird:AccountsNode!
 * @subpackage     Controllers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class RoleChildrenV1Controller extends BaseV1Controller
{

	use Controllers\Finders\TRoleFinder;

	/** @var Models\Roles\IRoleRepository */
	protected $roleRepository;

	/** @var string */
	protected $translationDomain = 'node.roleChildren';

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
	 * @throws NodeWebServerExceptions\IJsonApiException
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
