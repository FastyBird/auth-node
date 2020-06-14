<?php declare(strict_types = 1);

/**
 * RuleHydrator.php
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

namespace FastyBird\AuthNode\Hydrators\Rules;

use FastyBird\AuthNode\Entities;
use FastyBird\AuthNode\Schemas;
use FastyBird\NodeJsonApi\Hydrators as NodeJsonApiHydrators;
use IPub\JsonAPIDocument;

/**
 * Rule entity hydrator
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Hydrators
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class RuleHydrator extends NodeJsonApiHydrators\Hydrator
{

	/** @var string */
	protected $entityIdentifier = self::IDENTIFIER_KEY;

	/** @var string[] */
	protected $attributes = [
		'access',
	];

	/** @var string[] */
	protected $relationships = [
		Schemas\Rules\RuleSchema::RELATIONSHIPS_ROLE,
		Schemas\Rules\RuleSchema::RELATIONSHIPS_RESOURCE,
		Schemas\Rules\RuleSchema::RELATIONSHIPS_PRIVILEGE,
	];

	/**
	 * {@inheritDoc}
	 */
	protected function getEntityName(): string
	{
		return Entities\Rules\Rule::class;
	}

	/**
	 * @param JsonAPIDocument\Objects\IStandardObject<mixed> $attributes
	 *
	 * @return bool
	 */
	protected function hydrateAccessAttribute(JsonAPIDocument\Objects\IStandardObject $attributes): bool
	{
		if ($attributes->get('access') === null) {
			return false;
		}

		return (bool) $attributes->get('access');
	}

}
