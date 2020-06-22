<?php declare(strict_types = 1);

/**
 * MachineAccountIdentitySchema.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Schemas
 * @since          0.1.0
 *
 * @date           21.06.20
 */

namespace FastyBird\AuthNode\Schemas\Identities;

use FastyBird\AuthNode\Entities;

/**
 * Machine account identity entity schema
 *
 * @package         FastyBird:AuthNode!
 * @subpackage      Schemas
 *
 * @author          Adam Kadlec <adam.kadlec@fastybird.com>
 *
 * @phpstan-extends IdentitySchema<Entities\Identities\IMachineAccountIdentity>
 */
final class MachineAccountIdentitySchema extends IdentitySchema
{

	/**
	 * Define entity schema type string
	 */
	public const SCHEMA_TYPE = 'auth-node/machine-account-identity';

	/**
	 * {@inheritDoc}
	 */
	public function getEntityClass(): string
	{
		return Entities\Identities\MachineAccountIdentity::class;
	}

	/**
	 * @return string
	 */
	public function getType(): string
	{
		return self::SCHEMA_TYPE;
	}

}
