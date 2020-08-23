<?php declare(strict_types = 1);

/**
 * AccountSchema.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Schemas
 * @since          0.1.0
 *
 * @date           19.08.20
 */

namespace FastyBird\AuthNode\Schemas\Accounts;

use FastyBird\AuthNode;
use FastyBird\AuthNode\Entities;
use FastyBird\AuthNode\Router;
use FastyBird\NodeJsonApi\Schemas as NodeJsonApiSchemas;
use IPub\SlimRouter\Routing;
use Neomerx\JsonApi;

/**
 * Account entity schema
 *
 * @package         FastyBird:AuthNode!
 * @subpackage      Schemas
 *
 * @author          Adam Kadlec <adam.kadlec@fastybird.com>
 *
 * @phpstan-template T of Entities\Accounts\IAccount
 * @phpstan-extends  NodeJsonApiSchemas\JsonApiSchema<T>
 */
abstract class AccountSchema extends NodeJsonApiSchemas\JsonApiSchema
{

	/**
	 * Define relationships names
	 */
	public const RELATIONSHIPS_IDENTITIES = 'identities';
	public const RELATIONSHIPS_ROLES = 'roles';

	/** @var Routing\IRouter */
	protected $router;

	public function __construct(
		Routing\IRouter $router
	) {
		$this->router = $router;
	}

	/**
	 * @param Entities\Accounts\IAccount $account
	 * @param JsonApi\Contracts\Schema\ContextInterface $context
	 *
	 * @return iterable<string, string|null>
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getAttributes($account, JsonApi\Contracts\Schema\ContextInterface $context): iterable
	{
		return [
			'state' => $account->getState()->getValue(),

			'last_visit' => $account->getLastVisit() !== null ? $account->getLastVisit()->format(DATE_ATOM) : null,
			'registered' => $account->getCreatedAt() !== null ? $account->getCreatedAt()->format(DATE_ATOM) : null,
		];
	}

	/**
	 * @param Entities\Accounts\IAccount $account
	 *
	 * @return JsonApi\Contracts\Schema\LinkInterface
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getSelfLink($account): JsonApi\Contracts\Schema\LinkInterface
	{
		return new JsonApi\Schema\Link(
			false,
			$this->router->urlFor(
				AuthNode\Constants::ROUTE_NAME_ACCOUNT,
				[
					Router\Router::URL_ITEM_ID => $account->getPlainId(),
				]
			),
			false
		);
	}

	/**
	 * @param Entities\Accounts\IAccount $account
	 * @param JsonApi\Contracts\Schema\ContextInterface $context
	 *
	 * @return iterable<string, mixed>
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getRelationships($account, JsonApi\Contracts\Schema\ContextInterface $context): iterable
	{
		return [
			self::RELATIONSHIPS_IDENTITIES => [
				self::RELATIONSHIP_DATA          => $account->getIdentities(),
				self::RELATIONSHIP_LINKS_SELF    => true,
				self::RELATIONSHIP_LINKS_RELATED => true,
			],
			self::RELATIONSHIPS_ROLES      => [
				self::RELATIONSHIP_DATA          => $account->getRoles(),
				self::RELATIONSHIP_LINKS_SELF    => true,
				self::RELATIONSHIP_LINKS_RELATED => false,
			],
		];
	}

	/**
	 * @param Entities\Accounts\IAccount $account
	 * @param string $name
	 *
	 * @return JsonApi\Contracts\Schema\LinkInterface
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getRelationshipRelatedLink($account, string $name): JsonApi\Contracts\Schema\LinkInterface
	{
		if ($name === self::RELATIONSHIPS_IDENTITIES) {
			return new JsonApi\Schema\Link(
				false,
				$this->router->urlFor(
					AuthNode\Constants::ROUTE_NAME_ACCOUNT_IDENTITIES,
					[
						Router\Router::URL_ACCOUNT_ID => $account->getPlainId(),
					]
				),
				true,
				[
					'count' => count($account->getIdentities()),
				]
			);
		}

		return parent::getRelationshipRelatedLink($account, $name);
	}

	/**
	 * @param Entities\Accounts\IAccount $account
	 * @param string $name
	 *
	 * @return JsonApi\Contracts\Schema\LinkInterface
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getRelationshipSelfLink($account, string $name): JsonApi\Contracts\Schema\LinkInterface
	{
		if (
			$name === self::RELATIONSHIPS_IDENTITIES
			|| $name === self::RELATIONSHIPS_ROLES
		) {
			return new JsonApi\Schema\Link(
				false,
				$this->router->urlFor(
					AuthNode\Constants::ROUTE_NAME_ACCOUNT_RELATIONSHIP,
					[
						Router\Router::URL_ITEM_ID     => $account->getPlainId(),
						Router\Router::RELATION_ENTITY => $name,
					]
				),
				false
			);
		}

		return parent::getRelationshipSelfLink($account, $name);
	}

}
