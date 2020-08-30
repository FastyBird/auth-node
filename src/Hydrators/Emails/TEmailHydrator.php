<?php declare(strict_types = 1);

/**
 * ProfileEmailHydrator.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Hydrators
 * @since          0.1.0
 *
 * @date           21.08.20
 */

namespace FastyBird\AuthNode\Hydrators\Emails;

use FastyBird\AuthNode\Entities;
use FastyBird\AuthNode\Types;
use IPub\JsonAPIDocument;

/**
 * Profile email entity hydrator
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Hydrators
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
trait TEmailHydrator
{

	/**
	 * @return string
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
		$isPrivate = (bool) $attributes->get('private');

		return Types\EmailVisibilityType::get($isPrivate ? Types\EmailVisibilityType::VISIBILITY_PRIVATE : Types\EmailVisibilityType::VISIBILITY_PUBLIC);
	}

}
