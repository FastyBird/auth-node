<?php declare(strict_types = 1);

/**
 * MachineAccountHydrator.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Hydrators
 * @since          0.1.0
 *
 * @date           15.08.20
 */

namespace FastyBird\AuthNode\Hydrators\Accounts;

use FastyBird\AuthNode\Entities;
use FastyBird\NodeJsonApi\Hydrators as NodeJsonApiHydrators;
use IPub\JsonAPIDocument;
use Nette\Utils;

/**
 * Machine account entity hydrator
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Hydrators
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class MachineAccountHydrator extends NodeJsonApiHydrators\Hydrator
{

	/** @var string */
	protected $entityIdentifier = self::IDENTIFIER_KEY;

	/** @var string[] */
	protected $attributes = [
		0 => 'params',

		"device",
	];

	/**
	 * {@inheritDoc}
	 */
	protected function getEntityName(): string
	{
		return Entities\Accounts\MachineAccount::class;
	}

	/**
	 * @param JsonAPIDocument\Objects\IStandardObject<mixed> $attributes
	 *
	 * @return Utils\ArrayHash|null
	 */
	protected function hydrateParamsAttribute(
		JsonAPIDocument\Objects\IStandardObject $attributes
	): ?Utils\ArrayHash {
		if ($attributes->has('params')) {
			if ($attributes->get('params') instanceof JsonAPIDocument\Objects\IStandardObject) {
				return Utils\ArrayHash::from($attributes->get('params')->toArray());

			} elseif ($attributes->get('params') !== null) {
				return Utils\ArrayHash::from((array) $attributes->get('params'));
			}
		}

		return null;
	}

}
