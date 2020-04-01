<?php declare(strict_types = 1);

/**
 * EmailSchema.php
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
 * Email entity schema
 *
 * @package         iPublikuj:UserProfileModule!
 * @subpackage      Schemas
 *
 * @author          Adam Kadlec <adam.kadlec@ipublikuj.eu>
 *
 * @phpstan-extends JsonApiSchema<Entities\Emails\IEmail>
 */
final class EmailSchema extends JsonApiSchema
{

	/**
	 * Define entity schema type string
	 */
	public const SCHEMA_TYPE = 'accounts-node/email';

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
	 * @return iterable<string, mixed>
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getAttributes($email, JsonApi\Contracts\Schema\ContextInterface $context): iterable
	{
		return [
			'address'     => $email->getAddress(),
			'is_default'  => $email->isDefault(),
			'is_verified' => $email->isVerified(),
			'is_private'  => $email->isPrivate(),
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
				'account.email',
				[
					Router\Router::URL_ITEM_ID => $email->getPlainId(),
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
				$this->router->urlFor('account'),
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
					'account.emails.relationship',
					[
						Router\Router::RELATION_ENTITY => $name,
					]
				),
				false
			);
		}

		return parent::getRelationshipSelfLink($email, $name);
	}

}
