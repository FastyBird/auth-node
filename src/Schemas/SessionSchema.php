<?php declare(strict_types = 1);

/**
 * SessionSchema.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AccountsNode!
 * @subpackage     Schemas
 * @since          0.1.0
 *
 * @date           31.03.20
 */

namespace FastyBird\AccountsNode\Schemas;

use FastyBird\AccountsNode\Entities;
use FastyBird\AccountsNode\Router;
use IPub\SlimRouter\Routing;
use Neomerx\JsonApi;

/**
 * Session entity schema
 *
 * @package         iPublikuj:UserProfileModule!
 * @subpackage      Schemas
 *
 * @author          Adam Kadlec <adam.kadlec@ipublikuj.eu>
 *
 * @phpstan-extends JsonApiSchema<Entities\Tokens\IAccessToken>
 */
final class SessionSchema extends JsonApiSchema
{

	/**
	 * Define entity schema type string
	 */
	public const SCHEMA_TYPE = 'accounts-node/session';

	/**
	 * Define relationships names
	 */
	public const RELATIONSHIPS_ACCOUNT = 'account';

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
		return Entities\Tokens\AccessToken::class;
	}

	/**
	 * @return string
	 */
	public function getType(): string
	{
		return self::SCHEMA_TYPE;
	}

	/**
	 * @param Entities\Tokens\AccessToken $accessToken
	 * @param JsonApi\Contracts\Schema\ContextInterface $context
	 *
	 * @return iterable<string, mixed>
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getAttributes($accessToken, JsonApi\Contracts\Schema\ContextInterface $context): iterable
	{
		return [
			'token'      => $accessToken->getToken(),
			'expiration' => $accessToken->getValidTill() !== null ? $accessToken->getValidTill()->format(DATE_ATOM) : null,
			'token_type' => 'Bearer',
			'refresh'    => $accessToken->getRefreshToken() !== null ? $accessToken->getRefreshToken()->getToken() : null,
		];
	}

	/**
	 * @param Entities\Tokens\AccessToken $accessToken
	 *
	 * @return JsonApi\Contracts\Schema\LinkInterface
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getSelfLink($accessToken): JsonApi\Contracts\Schema\LinkInterface
	{
		return new JsonApi\Schema\Link(
			false,
			$this->router->urlFor('session'),
			false
		);
	}

	/**
	 * @param Entities\Tokens\AccessToken $accessToken
	 * @param JsonApi\Contracts\Schema\ContextInterface $context
	 *
	 * @return iterable<string, mixed>
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getRelationships($accessToken, JsonApi\Contracts\Schema\ContextInterface $context): iterable
	{
		return [
			self::RELATIONSHIPS_ACCOUNT => [
				self::RELATIONSHIP_DATA          => $accessToken->getIdentity()->getAccount(),
				self::RELATIONSHIP_LINKS_SELF    => true,
				self::RELATIONSHIP_LINKS_RELATED => true,
			],
		];
	}

	/**
	 * @param Entities\Tokens\AccessToken $accessToken
	 * @param string $name
	 *
	 * @return JsonApi\Contracts\Schema\LinkInterface
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getRelationshipRelatedLink($accessToken, string $name): JsonApi\Contracts\Schema\LinkInterface
	{
		if ($name === self::RELATIONSHIPS_ACCOUNT) {
			return new JsonApi\Schema\Link(
				false,
				$this->router->urlFor(
					'account',
					[
						Router\Router::URL_ITEM_ID => $accessToken->getIdentity()->getAccount()->getPlainId(),
					]
				),
				false
			);
		}

		return parent::getRelationshipRelatedLink($accessToken, $name);
	}

	/**
	 * @param Entities\Tokens\AccessToken $accessToken
	 * @param string $name
	 *
	 * @return JsonApi\Contracts\Schema\LinkInterface
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getRelationshipSelfLink($accessToken, string $name): JsonApi\Contracts\Schema\LinkInterface
	{
		if ($name === self::RELATIONSHIPS_ACCOUNT) {
			return new JsonApi\Schema\Link(
				false,
				$this->router->urlFor(
					'session.relationship',
					[
						Router\Router::RELATION_ENTITY => $name,
					]
				),
				false
			);
		}

		return parent::getRelationshipSelfLink($accessToken, $name);
	}

}
