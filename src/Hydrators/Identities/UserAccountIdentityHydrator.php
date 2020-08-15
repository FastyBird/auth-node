<?php declare(strict_types = 1);

/**
 * UserAccountIdentityHydrator.php
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

namespace FastyBird\AuthNode\Hydrators\Identities;

use FastyBird\AuthNode\Entities;

/**
 * User account identity entity hydrator
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Hydrators
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class UserAccountIdentityHydrator extends IdentityHydrator
{

	/** @var string[] */
	protected $attributes = [
		'uid',
		'password',
	];

	/**
	 * {@inheritDoc}
	 */
	protected function getEntityName(): string
	{
		return Entities\Identities\UserAccountIdentity::class;
	}

}
