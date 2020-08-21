<?php declare(strict_types = 1);

/**
 * UserAccountSchema.php
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

namespace FastyBird\AuthNode\Schemas\Accounts;

use FastyBird\AuthNode;
use FastyBird\AuthNode\Entities;
use FastyBird\AuthNode\Router;
use FastyBird\AuthNode\Security;
use FastyBird\NodeAuth;
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
 * @phpstan-extends AccountSchema<Entities\Accounts\IUserAccount>
 */
final class UserAccountSchema extends AccountSchema
{

	/**
	 * Define entity schema type string
	 */
	public const SCHEMA_TYPE = 'auth-node/user-account';

	/**
	 * Define relationships names
	 */
	public const RELATIONSHIPS_PARENT = 'parent';
	public const RELATIONSHIPS_CHILDREN = 'children';
	public const RELATIONSHIPS_EMAILS = 'emails';

	/** @var Security\User */
	private $user;

	public function __construct(
		Routing\IRouter $router,
		Security\User $user
	) {
		parent::__construct($router);

		$this->user = $user;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getEntityClass(): string
	{
		return Entities\Accounts\UserAccount::class;
	}

	/**
	 * @return string
	 */
	public function getType(): string
	{
		return self::SCHEMA_TYPE;
	}

	/**
	 * @param Entities\Accounts\IUserAccount $account
	 * @param JsonApi\Contracts\Schema\ContextInterface $context
	 *
	 * @return iterable<string, mixed>
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getAttributes($account, JsonApi\Contracts\Schema\ContextInterface $context): iterable
	{
		return array_merge((array) parent::getAttributes($account, $context), [
			'name'  => $account->getName(),
			'email' => $account->getEmail() !== null ? $account->getEmail()->getAddress() : null,

			'details' => [
				'first_name'  => $account->getDetails()->getFirstName(),
				'last_name'   => $account->getDetails()->getLastName(),
				'middle_name' => $account->getDetails()->getMiddleName(),
			],

			'language' => $account->getLanguage(),

			'week_start' => $account->getParam('datetime.week_start', 1),
			'datetime'   => [
				'timezone'    => $account->getParam('datetime.zone', 'Europe/London'),
				'date_format' => $account->getParam('datetime.format.date', 'DD.MM.YYYY'),
				'time_format' => $account->getParam('datetime.format.time', 'HH:mm'),
			],
		]);
	}

	/**
	 * @param Entities\Accounts\IUserAccount $account
	 * @param JsonApi\Contracts\Schema\ContextInterface $context
	 *
	 * @return iterable<string, mixed>
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getRelationships($account, JsonApi\Contracts\Schema\ContextInterface $context): iterable
	{
		$basicRelations = array_merge((array) parent::getRelationships($account, $context), [
			self::RELATIONSHIPS_EMAILS => [
				self::RELATIONSHIP_DATA          => $account->getEmails(),
				self::RELATIONSHIP_LINKS_SELF    => true,
				self::RELATIONSHIP_LINKS_RELATED => false,
			],
		]);

		if ($this->user->isInRole(NodeAuth\Constants::ROLE_USER)) {
			return $basicRelations;

		} else {
			return array_merge($basicRelations, [
				self::RELATIONSHIPS_PARENT   => [
					self::RELATIONSHIP_DATA          => $account->getParent(),
					self::RELATIONSHIP_LINKS_SELF    => true,
					self::RELATIONSHIP_LINKS_RELATED => false,
				],
				self::RELATIONSHIPS_CHILDREN => [
					self::RELATIONSHIP_DATA          => $account->getChildren(),
					self::RELATIONSHIP_LINKS_SELF    => true,
					self::RELATIONSHIP_LINKS_RELATED => false,
				],
			]);
		}
	}

	/**
	 * @param Entities\Accounts\IUserAccount $account
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
					AuthNode\Constants::ROUTE_NAME_ACCOUNT_EMAILS
				),
				true,
				[
					'count' => count($account->getEmails()),
				]
			);
		}

		return parent::getRelationshipRelatedLink($account, $name);
	}

	/**
	 * @param Entities\Accounts\IUserAccount $account
	 * @param string $name
	 *
	 * @return JsonApi\Contracts\Schema\LinkInterface
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getRelationshipSelfLink($account, string $name): JsonApi\Contracts\Schema\LinkInterface
	{
		if (
			$name === self::RELATIONSHIPS_PARENT
			|| $name === self::RELATIONSHIPS_CHILDREN
			|| $name === self::RELATIONSHIPS_EMAILS
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
