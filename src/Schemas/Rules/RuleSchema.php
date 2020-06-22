<?php declare(strict_types = 1);

/**
 * RuleSchema.php
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

namespace FastyBird\AuthNode\Schemas\Rules;

use FastyBird\AuthNode\Entities;
use FastyBird\AuthNode\Router;
use FastyBird\NodeJsonApi\Schemas as NodeJsonApiSchemas;
use IPub\SlimRouter\Routing;
use Neomerx\JsonApi;

/**
 * Rule entity schema
 *
 * @package          FastyBird:AuthNode!
 * @subpackage       Schemas
 *
 * @author           Adam Kadlec <adam.kadlec@fastybird.com>
 *
 * @phpstan-template T of Entities\Rules\IRule
 * @phpstan-extends  NodeJsonApiSchemas\JsonApiSchema<T>
 */
final class RuleSchema extends NodeJsonApiSchemas\JsonApiSchema
{

	/**
	 * Define entity schema type string
	 */
	public const SCHEMA_TYPE = 'auth-node/rule';

	/**
	 * Define relationships names
	 */
	public const RELATIONSHIPS_ROLE = 'role';
	public const RELATIONSHIPS_RESOURCE = 'resource';
	public const RELATIONSHIPS_PRIVILEGE = 'privilege';

	/** @var Routing\IRouter */
	private $router;

	public function __construct(
		Routing\IRouter $router
	) {
		$this->router = $router;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getEntityClass(): string
	{
		return Entities\Rules\Rule::class;
	}

	/**
	 * @return string
	 */
	public function getType(): string
	{
		return self::SCHEMA_TYPE;
	}

	/**
	 * @param Entities\Rules\IRule $rule
	 * @param JsonApi\Contracts\Schema\ContextInterface $context
	 *
	 * @return iterable<string, mixed>
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getAttributes($rule, JsonApi\Contracts\Schema\ContextInterface $context): iterable
	{
		return [
			'access' => $rule->hasAccess(),
		];
	}

	/**
	 * @param Entities\Rules\IRule $rule
	 *
	 * @return JsonApi\Contracts\Schema\LinkInterface
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getSelfLink($rule): JsonApi\Contracts\Schema\LinkInterface
	{
		return new JsonApi\Schema\Link(
			false,
			$this->router->urlFor(
				'rule',
				[
					Router\Router::URL_ITEM_ID => $rule->getPlainId(),
				]
			),
			false
		);
	}

	/**
	 * @param Entities\Rules\IRule $rule
	 * @param JsonApi\Contracts\Schema\ContextInterface $context
	 *
	 * @return iterable<string, mixed>
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getRelationships($rule, JsonApi\Contracts\Schema\ContextInterface $context): iterable
	{
		return [
			self::RELATIONSHIPS_ROLE => [
				self::RELATIONSHIP_DATA          => $rule->getRole(),
				self::RELATIONSHIP_LINKS_SELF    => true,
				self::RELATIONSHIP_LINKS_RELATED => true,
			],
			self::RELATIONSHIPS_RESOURCE => [
				self::RELATIONSHIP_DATA          => $rule->getResource(),
				self::RELATIONSHIP_LINKS_SELF    => true,
				self::RELATIONSHIP_LINKS_RELATED => true,
			],
			self::RELATIONSHIPS_PRIVILEGE => [
				self::RELATIONSHIP_DATA          => $rule->getPrivilege(),
				self::RELATIONSHIP_LINKS_SELF    => true,
				self::RELATIONSHIP_LINKS_RELATED => true,
			],
		];
	}

	/**
	 * @param Entities\Rules\IRule $rule
	 * @param string $name
	 *
	 * @return JsonApi\Contracts\Schema\LinkInterface
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getRelationshipRelatedLink($rule, string $name): JsonApi\Contracts\Schema\LinkInterface
	{
		if ($name === self::RELATIONSHIPS_ROLE) {
			return new JsonApi\Schema\Link(
				false,
				$this->router->urlFor(
					'role',
					[
						Router\Router::URL_ITEM_ID => $rule->getRole()->getPlainId(),
					]
				),
				false
			);

		} elseif ($name === self::RELATIONSHIPS_RESOURCE) {
			return new JsonApi\Schema\Link(
				false,
				$this->router->urlFor(
					'resource',
					[
						Router\Router::URL_ITEM_ID => $rule->getResource()->getPlainId(),
					]
				),
				false
			);

		} elseif ($name === self::RELATIONSHIPS_PRIVILEGE) {
			return new JsonApi\Schema\Link(
				false,
				$this->router->urlFor(
					'privilege',
					[
						Router\Router::URL_ITEM_ID => $rule->getPrivilege()->getPlainId(),
					]
				),
				false
			);
		}

		return parent::getRelationshipRelatedLink($rule, $name);
	}

	/**
	 * @param Entities\Rules\IRule $rule
	 * @param string $name
	 *
	 * @return JsonApi\Contracts\Schema\LinkInterface
	 *
	 * @phpstan-param T $rule
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getRelationshipSelfLink($rule, string $name): JsonApi\Contracts\Schema\LinkInterface
	{
		if (
			$name === self::RELATIONSHIPS_ROLE
			|| $name === self::RELATIONSHIPS_RESOURCE
			|| $name === self::RELATIONSHIPS_PRIVILEGE
		) {
			return new JsonApi\Schema\Link(
				false,
				$this->router->urlFor(
					'rule.relationship',
					[
						Router\Router::URL_ITEM_ID     => $rule->getPlainId(),
						Router\Router::RELATION_ENTITY => $name,

					]
				),
				false
			);
		}

		return parent::getRelationshipSelfLink($rule, $name);
	}

}
