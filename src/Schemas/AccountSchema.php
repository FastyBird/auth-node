<?php declare(strict_types = 1);

/**
 * AccountSchema.php
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
 * Account entity schema
 *
 * @package         iPublikuj:UserProfileModule!
 * @subpackage      Schemas
 *
 * @author          Adam Kadlec <adam.kadlec@ipublikuj.eu>
 *
 * @phpstan-extends JsonApiSchema<Entities\Accounts\IAccount>
 */
final class AccountSchema extends JsonApiSchema
{

	/**
	 * Define entity schema type string
	 */
	public const SCHEMA_TYPE = 'accounts-node/account';

	/**
	 * Define relationships names
	 */
	public const RELATIONSHIPS_EMAILS = 'emails';
	public const RELATIONSHIPS_QUESTION = 'security-question';
	public const RELATIONSHIPS_IDENTITIES = 'identities';
	public const RELATIONSHIPS_ROLES = 'roles';

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
		return Entities\Accounts\Account::class;
	}

	/**
	 * @return string
	 */
	public function getType(): string
	{
		return self::SCHEMA_TYPE;
	}

	/**
	 * @param Entities\Accounts\IAccount $account
	 * @param JsonApi\Contracts\Schema\ContextInterface $context
	 *
	 * @return iterable<string, mixed>
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getAttributes($account, JsonApi\Contracts\Schema\ContextInterface $context): iterable
	{
		return [
			'name'  => $account->getName(),
			'email' => $account->getEmail() !== null ? $account->getEmail()->getAddress() : null,

			'details' => [
				'first_name'  => $account->getDetails()->getFirstName(),
				'last_name'   => $account->getDetails()->getLastName(),
				'middle_name' => $account->getDetails()->getMiddleName(),
			],

			'status' => $account->getStatus()->getValue(),

			'last_visit' => $account->getLastVisit() !== null ? $account->getLastVisit()->format(DATE_ATOM) : null,
			'registered' => $account->getCreatedAt() !== null ? $account->getCreatedAt()->format(DATE_ATOM) : null,

			'language' => $account->getLanguage(),

			'params' => $account->getParams(),
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
				'account',
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
			self::RELATIONSHIPS_EMAILS     => [
				self::RELATIONSHIP_DATA          => $account->getEmails(),
				self::RELATIONSHIP_LINKS_SELF    => true,
				self::RELATIONSHIP_LINKS_RELATED => true,
			],
			self::RELATIONSHIPS_QUESTION   => [
				self::RELATIONSHIP_DATA          => $account->getSecurityQuestion(),
				self::RELATIONSHIP_LINKS_SELF    => true,
				self::RELATIONSHIP_LINKS_RELATED => true,
			],
			self::RELATIONSHIPS_IDENTITIES => [
				self::RELATIONSHIP_DATA          => $account->getIdentities(),
				self::RELATIONSHIP_LINKS_SELF    => true,
				self::RELATIONSHIP_LINKS_RELATED => true,
			],
			self::RELATIONSHIPS_ROLES      => [
				self::RELATIONSHIP_DATA          => $account->getRoles(),
				self::RELATIONSHIP_LINKS_SELF    => true,
				self::RELATIONSHIP_LINKS_RELATED => true,
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
		if ($name === self::RELATIONSHIPS_EMAILS) {
			return new JsonApi\Schema\Link(
				false,
				$this->router->urlFor(
					'account.emails',
					[
						Router\Router::URL_ACCOUNT_ID => $account->getPlainId(),
					]
				),
				true,
				[
					'count' => count($account->getEmails()),
				]
			);

		} elseif ($name === self::RELATIONSHIPS_QUESTION) {
			return new JsonApi\Schema\Link(
				false,
				$this->router->urlFor(
					'account.security.question',
					[
						Router\Router::URL_ACCOUNT_ID => $account->getPlainId(),
					]
				),
				false
			);

		} elseif ($name === self::RELATIONSHIPS_IDENTITIES) {
			return new JsonApi\Schema\Link(
				false,
				$this->router->urlFor(
					'account.identities',
					[
						Router\Router::URL_ACCOUNT_ID => $account->getPlainId(),
					]
				),
				true,
				[
					'count' => count($account->getIdentities()),
				]
			);

		} elseif ($name === self::RELATIONSHIPS_ROLES) {
			return new JsonApi\Schema\Link(
				false,
				$this->router->urlFor(
					'account.roles',
					[
						Router\Router::URL_ACCOUNT_ID => $account->getPlainId(),
					]
				),
				true,
				[
					'count' => count($account->getRoles()),
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
			$name === self::RELATIONSHIPS_EMAILS
			|| $name === self::RELATIONSHIPS_QUESTION
			|| $name === self::RELATIONSHIPS_IDENTITIES
			|| $name === self::RELATIONSHIPS_ROLES
		) {
			return new JsonApi\Schema\Link(
				false,
				$this->router->urlFor(
					'account.relationship',
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
