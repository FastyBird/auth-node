<?php declare(strict_types = 1);

/**
 * RoletHydrator.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AccountsNode!
 * @subpackage     Hydrators
 * @since          0.1.0
 *
 * @date           03.06.20
 */

namespace FastyBird\AccountsNode\Hydrators\Roles;

use FastyBird\AccountsNode\Entities;
use FastyBird\AccountsNode\Schemas;
use FastyBird\NodeDatabase\Hydrators as NodeDatabaseHydrators;
use IPub\JsonAPIDocument;
use Nette\Utils;

/**
 * Role entity hydrator
 *
 * @package        FastyBird:AccountsNode!
 * @subpackage     Hydrators
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class RoleHydrator extends NodeDatabaseHydrators\Hydrator
{

	/** @var string */
	protected $entityIdentifier = self::IDENTIFIER_KEY;

	/** @var string[] */
	protected $attributes = [
		0 => 'name',
		1 => 'comment',
		2 => 'priority',

		'key_name' => 'keyName',
	];

	/** @var string[] */
	protected $relationships = [
		Schemas\Roles\RoleSchema::RELATIONSHIPS_PARENT,
	];

	/**
	 * {@inheritDoc}
	 */
	protected function getEntityName(): string
	{
		return Entities\Roles\Role::class;
	}

	/**
	 * @param JsonAPIDocument\Objects\IStandardObject<mixed> $attributes
	 *
	 * @return string
	 */
	protected function hydrateKeyNameAttribute(JsonAPIDocument\Objects\IStandardObject $attributes): string
	{
		if ($attributes->get('key_name') === null || (string) $attributes->get('key_name') === '') {
			return Utils\Strings::webalize((string) $attributes->get('name'));
		}

		return Utils\Strings::webalize((string) $attributes->get('key_name'));
	}

	/**
	 * @param JsonAPIDocument\Objects\IStandardObject<mixed> $attributes
	 *
	 * @return string|null
	 */
	protected function hydrateCommentAttribute(JsonAPIDocument\Objects\IStandardObject $attributes): ?string
	{
		if ($attributes->get('comment') === null || (string) $attributes->get('comment') === '') {
			return null;
		}

		return (string) $attributes->get('comment');
	}

	/**
	 * @param JsonAPIDocument\Objects\IStandardObject<mixed> $attributes
	 *
	 * @return int
	 */
	protected function hydratePriorityAttribute(JsonAPIDocument\Objects\IStandardObject $attributes): int
	{
		if ($attributes->get('priority') === null || (string) $attributes->get('priority') === '') {
			return 0;
		}

		return (int) $attributes->get('priority');
	}

}
