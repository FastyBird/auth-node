<?php declare(strict_types = 1);

/**
 * RoleSchema.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Schemas
 * @since          0.1.0
 *
 * @date           26.05.20
 */

namespace FastyBird\AuthNode\Schemas\Roles;

use FastyBird\AuthNode\Entities;
use FastyBird\AuthNode\Models;
use FastyBird\AuthNode\Queries;
use FastyBird\AuthNode\Router;
use FastyBird\NodeJsonApi\Schemas as NodeJsonApiSchemas;
use IPub\SlimRouter\Routing;
use Neomerx\JsonApi;

/**
 * Role entity schema
 *
 * @package          FastyBird:AuthNode!
 * @subpackage       Schemas
 *
 * @author           Adam Kadlec <adam.kadlec@fastybird.com>
 *
 * @phpstan-template T of Entities\Roles\IRole
 * @phpstan-extends  NodeJsonApiSchemas\JsonApiSchema<T>
 */
final class RoleSchema extends NodeJsonApiSchemas\JsonApiSchema
{

	/**
	 * Define entity schema type string
	 */
	public const SCHEMA_TYPE = 'auth-node/role';

	/**
	 * Define relationships names
	 */
	public const RELATIONSHIPS_PARENT = 'parent';
	public const RELATIONSHIPS_CHILDREN = 'children';
	public const RELATIONSHIPS_RULES = 'rules';

	/** @var Models\Roles\IRoleRepository */
	private $roleRepository;

	/** @var Models\Rules\IRuleRepository */
	private $ruleRepository;

	/** @var Routing\IRouter */
	private $router;

	/**
	 * @param Models\Roles\IRoleRepository $roleRepository
	 * @param Models\Rules\IRuleRepository $ruleRepository
	 * @param Routing\IRouter $router
	 */
	public function __construct(
		Models\Roles\IRoleRepository $roleRepository,
		Models\Rules\IRuleRepository $ruleRepository,
		Routing\IRouter $router
	) {
		$this->roleRepository = $roleRepository;
		$this->ruleRepository = $ruleRepository;

		$this->router = $router;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getEntityClass(): string
	{
		return Entities\Roles\Role::class;
	}

	/**
	 * @return string
	 */
	public function getType(): string
	{
		return self::SCHEMA_TYPE;
	}

	/**
	 * @param Entities\Roles\IRole $role
	 * @param JsonApi\Contracts\Schema\ContextInterface $context
	 *
	 * @return iterable<string, mixed>
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getAttributes($role, JsonApi\Contracts\Schema\ContextInterface $context): iterable
	{
		return [
			'name'        => $role->getName(),
			'description' => $role->getDescription(),

			'locked'        => $role->isLocked(),
			'anonymous'     => $role->isAnonymous(),
			'authenticated' => $role->isAuthenticated(),
			'administrator' => $role->isAdministrator(),
		];
	}

	/**
	 * @param Entities\Roles\IRole $role
	 *
	 * @return JsonApi\Contracts\Schema\LinkInterface
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getSelfLink($role): JsonApi\Contracts\Schema\LinkInterface
	{
		return new JsonApi\Schema\Link(
			false,
			$this->router->urlFor(
				'role',
				[
					Router\Router::URL_ITEM_ID => $role->getPlainId(),
				]
			),
			false
		);
	}

	/**
	 * @param Entities\Roles\IRole $role
	 * @param JsonApi\Contracts\Schema\ContextInterface $context
	 *
	 * @return iterable<string, mixed>
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getRelationships($role, JsonApi\Contracts\Schema\ContextInterface $context): iterable
	{
		$relationships = [
			self::RELATIONSHIPS_CHILDREN => [
				self::RELATIONSHIP_DATA          => $this->getChildren($role),
				self::RELATIONSHIP_LINKS_SELF    => true,
				self::RELATIONSHIP_LINKS_RELATED => true,
			],
			self::RELATIONSHIPS_RULES => [
				self::RELATIONSHIP_DATA          => $this->getRules($role),
				self::RELATIONSHIP_LINKS_SELF    => true,
				self::RELATIONSHIP_LINKS_RELATED => true,
			],
		];

		if ($role->getParent() !== null) {
			$relationships[self::RELATIONSHIPS_PARENT] = [
				self::RELATIONSHIP_DATA          => $role->getParent(),
				self::RELATIONSHIP_LINKS_SELF    => true,
				self::RELATIONSHIP_LINKS_RELATED => true,
			];
		}

		return $relationships;
	}

	/**
	 * @param Entities\Roles\IRole $role
	 * @param string $name
	 *
	 * @return JsonApi\Contracts\Schema\LinkInterface
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getRelationshipRelatedLink($role, string $name): JsonApi\Contracts\Schema\LinkInterface
	{
		if ($name === self::RELATIONSHIPS_PARENT && $role->getParent() !== null) {
			return new JsonApi\Schema\Link(
				false,
				$this->router->urlFor(
					'role',
					[
						Router\Router::URL_ITEM_ID => $role->getPlainId(),
					]
				),
				false
			);

		} elseif ($name === self::RELATIONSHIPS_CHILDREN) {
			return new JsonApi\Schema\Link(
				false,
				$this->router->urlFor(
					'role.children',
					[
						Router\Router::URL_ITEM_ID => $role->getPlainId(),
					]
				),
				true,
				[
					'count' => count($role->getChildren()),
				]
			);

		} elseif ($name === self::RELATIONSHIPS_RULES) {
			return new JsonApi\Schema\Link(
				false,
				$this->router->urlFor(
					'role.rules',
					[
						Router\Router::URL_ITEM_ID => $role->getPlainId(),
					]
				),
				true,
				[
					'count' => count($role->getRules()),
				]
			);
		}

		return parent::getRelationshipRelatedLink($role, $name);
	}

	/**
	 * @param Entities\Roles\IRole $role
	 * @param string $name
	 *
	 * @return JsonApi\Contracts\Schema\LinkInterface
	 *
	 * @phpstan-param T $role
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getRelationshipSelfLink($role, string $name): JsonApi\Contracts\Schema\LinkInterface
	{
		if (
			$name === self::RELATIONSHIPS_CHILDREN
			|| ($name === self::RELATIONSHIPS_PARENT && $role->getParent() !== null)
			|| $name === self::RELATIONSHIPS_RULES
		) {
			return new JsonApi\Schema\Link(
				false,
				$this->router->urlFor(
					'role.relationship',
					[
						Router\Router::URL_ITEM_ID     => $role->getPlainId(),
						Router\Router::RELATION_ENTITY => $name,

					]
				),
				false
			);
		}

		return parent::getRelationshipSelfLink($role, $name);
	}

	/**
	 * @param Entities\Roles\IRole $role
	 *
	 * @return Entities\Roles\IRole[]
	 */
	private function getChildren(Entities\Roles\IRole $role): array
	{
		$findQuery = new Queries\FindRolesQuery();
		$findQuery->forParent($role);

		return $this->roleRepository->findAllBy($findQuery);
	}

	/**
	 * @param Entities\Roles\IRole $role
	 *
	 * @return Entities\Rules\IRule[]
	 */
	private function getRules(Entities\Roles\IRole $role): array
	{
		$findQuery = new Queries\FindRulesQuery();
		$findQuery->forRole($role);

		return $this->ruleRepository->findAllBy($findQuery);
	}

}
