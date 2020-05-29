<?php declare(strict_types = 1);

/**
 * EmailHydrator.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AccountsNode!
 * @subpackage     Hydrators
 * @since          0.1.0
 *
 * @date           31.03.20
 */

namespace FastyBird\AccountsNode\Hydrators;

use FastyBird\AccountsNode\Entities;
use FastyBird\AccountsNode\Types;
use FastyBird\NodeDatabase\Hydrators as NodeDatabaseHydrators;
use IPub\JsonAPIDocument;

/**
 * Email entity hydrator
 *
 * @package        FastyBird:AccountsNode!
 * @subpackage     Hydrators
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class EmailHydrator extends NodeDatabaseHydrators\Hydrator
{

	/** @var string */
	protected $entityIdentifier = self::IDENTIFIER_KEY;

	/** @var string[] */
	protected $attributes = [
		0 => 'address',

		'is_default' => 'default',
		'is_private' => 'visibility',
	];

	/**
	 * {@inheritdoc}
	 */
	protected function getEntityName(): string
	{
		return Entities\Emails\Email::class;
	}

	/**
	 * @param JsonAPIDocument\Objects\IStandardObject<mixed> $attributes
	 *
	 * @return Types\EmailVisibilityType
	 */
	protected function hydrateVisibilityAttribute(JsonAPIDocument\Objects\IStandardObject $attributes): Types\EmailVisibilityType
	{
		$isPrivate = (bool) $attributes->get('is_private');

		return Types\EmailVisibilityType::get($isPrivate ? Types\EmailVisibilityType::VISIBILITY_PRIVATE : Types\EmailVisibilityType::VISIBILITY_PUBLIC);
	}

}
