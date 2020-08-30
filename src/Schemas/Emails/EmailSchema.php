<?php declare(strict_types = 1);

/**
 * EmailSchema.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Schemas
 * @since          0.1.0
 *
 * @date           31.03.20
 */

namespace FastyBird\AuthNode\Schemas\Emails;

use FastyBird\AuthNode;
use FastyBird\AuthNode\Entities;
use FastyBird\AuthNode\Router;
use FastyBird\NodeJsonApi\Schemas as NodeJsonApiSchemas;
use IPub\SlimRouter\Routing;
use Neomerx\JsonApi;

/**
 * Email entity schema
 *
 * @package         FastyBird:AuthNode!
 * @subpackage      Schemas
 *
 * @author          Adam Kadlec <adam.kadlec@fastybird.com>
 *
 * @phpstan-extends NodeJsonApiSchemas\JsonApiSchema<Entities\Emails\IEmail>
 */
final class EmailSchema extends NodeJsonApiSchemas\JsonApiSchema
{

	/**
	 * Define entity schema type string
	 */
	public const SCHEMA_TYPE = 'auth-node/email';

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
		return Entities\Emails\Email::class;
	}

	/**
	 * @return string
	 */
	public function getType(): string
	{
		return self::SCHEMA_TYPE;
	}

	/**
	 * @param Entities\Emails\IEmail $email
	 * @param JsonApi\Contracts\Schema\ContextInterface $context
	 *
	 * @return iterable<string, string|bool>
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getAttributes($email, JsonApi\Contracts\Schema\ContextInterface $context): iterable
	{
		return [
			'address'  => $email->getAddress(),
			'default'  => $email->isDefault(),
			'verified' => $email->isVerified(),
			'private'  => $email->isPrivate(),
		];
	}

	/**
	 * @param Entities\Emails\IEmail $email
	 *
	 * @return JsonApi\Contracts\Schema\LinkInterface
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getSelfLink($email): JsonApi\Contracts\Schema\LinkInterface
	{
		return new JsonApi\Schema\Link(
			false,
			$this->router->urlFor(
				AuthNode\Constants::ROUTE_NAME_ACCOUNT_EMAIL,
				[
					Router\Router::URL_ACCOUNT_ID => $email->getAccount()->getPlainId(),
					Router\Router::URL_ITEM_ID    => $email->getPlainId(),
				]
			),
			false
		);
	}

	/**
	 * @param Entities\Emails\IEmail $email
	 * @param JsonApi\Contracts\Schema\ContextInterface $context
	 *
	 * @return iterable<string, mixed>
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getRelationships($email, JsonApi\Contracts\Schema\ContextInterface $context): iterable
	{
		return [
			self::RELATIONSHIPS_ACCOUNT => [
				self::RELATIONSHIP_DATA          => $email->getAccount(),
				self::RELATIONSHIP_LINKS_SELF    => true,
				self::RELATIONSHIP_LINKS_RELATED => true,
			],
		];
	}

	/**
	 * @param Entities\Emails\IEmail $email
	 * @param string $name
	 *
	 * @return JsonApi\Contracts\Schema\LinkInterface
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getRelationshipRelatedLink($email, string $name): JsonApi\Contracts\Schema\LinkInterface
	{
		if ($name === self::RELATIONSHIPS_ACCOUNT) {
			return new JsonApi\Schema\Link(
				false,
				$this->router->urlFor(
					AuthNode\Constants::ROUTE_NAME_ACCOUNT,
					[
						Router\Router::URL_ITEM_ID => $email->getAccount()->getPlainId(),
					]
				),
				false
			);
		}

		return parent::getRelationshipRelatedLink($email, $name);
	}

	/**
	 * @param Entities\Emails\IEmail $email
	 * @param string $name
	 *
	 * @return JsonApi\Contracts\Schema\LinkInterface
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getRelationshipSelfLink($email, string $name): JsonApi\Contracts\Schema\LinkInterface
	{
		if ($name === self::RELATIONSHIPS_ACCOUNT) {
			return new JsonApi\Schema\Link(
				false,
				$this->router->urlFor(
					AuthNode\Constants::ROUTE_NAME_ACCOUNT_EMAIL_RELATIONSHIP,
					[
						Router\Router::URL_ACCOUNT_ID  => $email->getAccount()->getPlainId(),
						Router\Router::URL_ITEM_ID     => $email->getPlainId(),
						Router\Router::RELATION_ENTITY => $name,
					]
				),
				false
			);
		}

		return parent::getRelationshipSelfLink($email, $name);
	}

}
