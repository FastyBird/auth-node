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

use FastyBird\NodeJsonApi\Hydrators as NodeJsonApiHydrators;

/**
 * Profile email entity hydrator
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Hydrators
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class ProfileEmailHydrator extends NodeJsonApiHydrators\Hydrator
{

	use TEmailHydrator;

	/** @var string */
	protected $entityIdentifier = self::IDENTIFIER_KEY;

	/** @var string[] */
	protected $attributes = [
		0 => 'address',

		'default' => 'default',
		'private' => 'visibility',
	];

	/** @var string */
	protected $translationDomain = 'node.emails';

}
