<?php declare(strict_types = 1);

/**
 * ResourceChildrenV1Controller.php
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
 * Resource children API controller
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Controllers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class ResourceChildrenV1Controller extends BaseV1Controller
{

	use Controllers\Finders\TResourceFinder;

	/** @var Models\Resources\IResourceRepository */
	private $resourceRepository;

	/** @var string */
	protected $translationDomain = 'node.resources';

	public function __construct(
		Models\Resources\IResourceRepository $resourceRepository
	) {
		$this->resourceRepository = $resourceRepository;
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
		// At first, try to load resource
		$resource = $this->findResource($request->getAttribute(Router\Router::URL_ITEM_ID));

		$findQuery = new Queries\FindResourcesQuery();
		$findQuery->forParent($resource);

		$children = $this->resourceRepository->getResultSet($findQuery);

		return $response
			->withEntity(NodeWebServerHttp\ScalarEntity::from($children));
	}

}
