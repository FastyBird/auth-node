<?php declare(strict_types = 1);

/**
 * RoleRulesV1Controller.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Controllers
 * @since          0.1.0
 *
 * @date           14.06.20
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
 * Role rules API controller
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Controllers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class RoleRulesV1Controller extends BaseV1Controller
{

	use Controllers\Finders\TRoleFinder;

	/** @var Models\Rules\IRuleRepository */
	private $ruleRepository;

	/** @var Models\Roles\IRoleRepository */
	protected $roleRepository;

	/** @var string */
	protected $translationDomain = 'node.roles';

	public function __construct(
		Models\Roles\IRoleRepository $roleRepository,
		Models\Rules\IRuleRepository $ruleRepository
	) {
		$this->roleRepository = $roleRepository;
		$this->ruleRepository = $ruleRepository;
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
		// At first, try to load role
		$role = $this->findRole($request->getAttribute(Router\Router::URL_ITEM_ID));

		$findQuery = new Queries\FindRulesQuery();
		$findQuery->forRole($role);

		$rules = $this->ruleRepository->getResultSet($findQuery);

		return $response
			->withEntity(NodeWebServerHttp\ScalarEntity::from($rules));
	}

}
