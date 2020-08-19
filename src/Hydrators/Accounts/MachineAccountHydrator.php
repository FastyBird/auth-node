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

/**
 * Machine account entity hydrator
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Hydrators
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class MachineAccountHydrator extends AccountHydrator
{

	/** @var string[] */
	protected $attributes = [
		'device',
		'state',
	];

	/**
	 * {@inheritDoc}
	 */
	protected function getEntityName(): string
	{
		return Entities\Accounts\MachineAccount::class;
	}

}
