<?php declare(strict_types = 1);

/**
 * ResourceSchema.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Schemas
 * @since          0.1.0
 *
 * @date           13.06.20
 */

namespace FastyBird\AuthNode\Schemas\Resources;

use FastyBird\AuthNode\Entities;
use FastyBird\AuthNode\Models;
use FastyBird\AuthNode\Queries;
use FastyBird\AuthNode\Router;
use FastyBird\NodeJsonApi\Schemas as NodeJsonApiSchemas;
use IPub\SlimRouter\Routing;
use Neomerx\JsonApi;

/**
 * Resource entity schema
 *
 * @package          FastyBird:AuthNode!
 * @subpackage       Schemas
 *
 * @author           Adam Kadlec <adam.kadlec@fastybird.com>
 *
 * @phpstan-template T of Entities\Resources\IResource
 * @phpstan-extends  NodeJsonApiSchemas\JsonApiSchema<T>
 */
final class ResourceSchema extends NodeJsonApiSchemas\JsonApiSchema
{

	/**
	 * Define entity schema type string
	 */
	public const SCHEMA_TYPE = 'auth-node/resource';

	/**
	 * Define relationships names
	 */
	public const RELATIONSHIPS_PARENT = 'parent';
	public const RELATIONSHIPS_CHILDREN = 'children';
	public const RELATIONSHIPS_PRIVILEGES = 'privileges';

	/** @var Models\Resources\IResourceRepository */
	private $resourceRepository;

	/** @var Models\Privileges\IPrivilegeRepository */
	private $privilegeRepository;

	/** @var Routing\IRouter */
	private $router;

	public function __construct(
		Models\Resources\IResourceRepository $resourceRepository,
		Models\Privileges\IPrivilegeRepository $privilegeRepository,
		Routing\IRouter $router
	) {
		$this->resourceRepository = $resourceRepository;
		$this->privilegeRepository = $privilegeRepository;

		$this->router = $router;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getEntityClass(): string
	{
		return Entities\Resources\Resource::class;
	}

	/**
	 * @return string
	 */
	public function getType(): string
	{
		return self::SCHEMA_TYPE;
	}

	/**
	 * @param Entities\Resources\IResource $resource
	 * @param JsonApi\Contracts\Schema\ContextInterface $context
	 *
	 * @return iterable<string, mixed>
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getAttributes($resource, JsonApi\Contracts\Schema\ContextInterface $context): iterable
	{
		return [
			'name'        => $resource->getName(),
			'description' => $resource->getDescription(),
			'origin'      => $resource->getOrigin(),
		];
	}

	/**
	 * @param Entities\Resources\IResource $resource
	 *
	 * @return JsonApi\Contracts\Schema\LinkInterface
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getSelfLink($resource): JsonApi\Contracts\Schema\LinkInterface
	{
		return new JsonApi\Schema\Link(
			false,
			$this->router->urlFor(
				'resource',
				[
					Router\Router::URL_ITEM_ID => $resource->getPlainId(),
				]
			),
			false
		);
	}

	/**
	 * @param Entities\Resources\IResource $resource
	 * @param JsonApi\Contracts\Schema\ContextInterface $context
	 *
	 * @return iterable<string, mixed>
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getRelationships($resource, JsonApi\Contracts\Schema\ContextInterface $context): iterable
	{
		$relationships = [
			self::RELATIONSHIPS_CHILDREN   => [
				self::RELATIONSHIP_DATA          => $this->getChildren($resource),
				self::RELATIONSHIP_LINKS_SELF    => true,
				self::RELATIONSHIP_LINKS_RELATED => true,
			],
			self::RELATIONSHIPS_PRIVILEGES => [
				self::RELATIONSHIP_DATA          => $this->getPrivileges($resource),
				self::RELATIONSHIP_LINKS_SELF    => true,
				self::RELATIONSHIP_LINKS_RELATED => true,
			],
		];

		if ($resource->getParent() !== null) {
			$relationships[self::RELATIONSHIPS_PARENT] = [
				self::RELATIONSHIP_DATA          => $resource->getParent(),
				self::RELATIONSHIP_LINKS_SELF    => true,
				self::RELATIONSHIP_LINKS_RELATED => true,
			];
		}

		return $relationships;
	}

	/**
	 * @param Entities\Resources\IResource $resource
	 * @param string $name
	 *
	 * @return JsonApi\Contracts\Schema\LinkInterface
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getRelationshipRelatedLink($resource, string $name): JsonApi\Contracts\Schema\LinkInterface
	{
		if ($name === self::RELATIONSHIPS_PARENT && $resource->getParent() !== null) {
			return new JsonApi\Schema\Link(
				false,
				$this->router->urlFor(
					'resource',
					[
						Router\Router::URL_ITEM_ID => $resource->getPlainId(),
					]
				),
				false
			);

		} elseif ($name === self::RELATIONSHIPS_PRIVILEGES) {
			return new JsonApi\Schema\Link(
				false,
				$this->router->urlFor(
					'resource.privileges',
					[
						Router\Router::URL_ITEM_ID => $resource->getPlainId(),
					]
				),
				true,
				[
					'count' => count($resource->getPrivileges()),
				]
			);

		} elseif ($name === self::RELATIONSHIPS_CHILDREN) {
			return new JsonApi\Schema\Link(
				false,
				$this->router->urlFor(
					'resource.children',
					[
						Router\Router::URL_ITEM_ID => $resource->getPlainId(),
					]
				),
				true,
				[
					'count' => count($resource->getChildren()),
				]
			);
		}

		return parent::getRelationshipRelatedLink($resource, $name);
	}

	/**
	 * @param Entities\Resources\IResource $resource
	 * @param string $name
	 *
	 * @return JsonApi\Contracts\Schema\LinkInterface
	 *
	 * @phpstan-param T $resource
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getRelationshipSelfLink($resource, string $name): JsonApi\Contracts\Schema\LinkInterface
	{
		if (
			$name === self::RELATIONSHIPS_PRIVILEGES
			|| $name === self::RELATIONSHIPS_CHILDREN
			|| ($name === self::RELATIONSHIPS_PARENT && $resource->getParent() !== null)
		) {
			return new JsonApi\Schema\Link(
				false,
				$this->router->urlFor(
					'resource.relationship',
					[
						Router\Router::URL_ITEM_ID     => $resource->getPlainId(),
						Router\Router::RELATION_ENTITY => $name,

					]
				),
				false
			);
		}

		return parent::getRelationshipSelfLink($resource, $name);
	}

	/**
	 * @param Entities\Resources\IResource $resource
	 *
	 * @return Entities\Resources\IResource[]
	 */
	private function getChildren(Entities\Resources\IResource $resource): array
	{
		$findQuery = new Queries\FindResourcesQuery();
		$findQuery->forParent($resource);

		return $this->resourceRepository->findAllBy($findQuery);
	}

	/**
	 * @param Entities\Resources\IResource $resource
	 *
	 * @return Entities\Privileges\IPrivilege[]
	 */
	private function getPrivileges(Entities\Resources\IResource $resource): array
	{
		$findQuery = new Queries\FindPrivilegesQuery();
		$findQuery->forResource($resource);

		return $this->privilegeRepository->findAllBy($findQuery);
	}

}
