<?php declare(strict_types = 1);

/**
 * ResourceHydrator.php
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

namespace FastyBird\AuthNode\Hydrators\Resources;

use FastyBird\AuthNode\Entities;
use FastyBird\NodeJsonApi\Hydrators as NodeJsonApiHydrators;
use IPub\JsonAPIDocument;

/**
 * Resource entity hydrator
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Hydrators
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class ResourceHydrator extends NodeJsonApiHydrators\Hydrator
{

	/** @var string */
	protected $entityIdentifier = self::IDENTIFIER_KEY;

	/** @var string[] */
	protected $attributes = [
		'description',
	];

	/**
	 * {@inheritDoc}
	 */
	protected function getEntityName(): string
	{
		return Entities\Resources\Resource::class;
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
