<?php declare(strict_types = 1);

/**
 * RoletHydrator.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Hydrators
 * @since          0.1.0
 *
 * @date           03.06.20
 */

namespace FastyBird\AuthNode\Hydrators\Roles;

use FastyBird\AuthNode\Entities;
use FastyBird\AuthNode\Schemas;
use FastyBird\NodeJsonApi\Hydrators as NodeJsonApiHydrators;
use IPub\JsonAPIDocument;
use Nette\Utils;

/**
 * Role entity hydrator
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Hydrators
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class RoleHydrator extends NodeJsonApiHydrators\Hydrator
{

	/** @var string */
	protected $entityIdentifier = self::IDENTIFIER_KEY;

	/** @var string[] */
	protected $attributes = [
		'name',
		'description',
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
	protected function hydrateNameAttribute(JsonAPIDocument\Objects\IStandardObject $attributes): string
	{
		if ($attributes->get('name') === null || (string) $attributes->get('name') === '') {
			return Utils\Strings::webalize((string) $attributes->get('name'));
		}

		return Utils\Strings::webalize((string) $attributes->get('name'));
	}

	/**
	 * @param JsonAPIDocument\Objects\IStandardObject<mixed> $attributes
	 *
	 * @return string|null
	 */
	protected function hydrateDescriptionAttribute(JsonAPIDocument\Objects\IStandardObject $attributes): ?string
	{
		if ($attributes->get('description') === null || (string) $attributes->get('description') === '') {
			return null;
		}

		return (string) $attributes->get('description');
	}

}
