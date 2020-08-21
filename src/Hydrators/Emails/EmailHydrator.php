<?php declare(strict_types = 1);

/**
 * EmailHydrator.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Hydrators
 * @since          0.1.0
 *
 * @date           31.03.20
 */

namespace FastyBird\AuthNode\Hydrators\Emails;

use FastyBird\AuthNode\Schemas;
use FastyBird\NodeJsonApi\Hydrators as NodeJsonApiHydrators;

/**
 * Email entity hydrator
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Hydrators
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class EmailHydrator extends NodeJsonApiHydrators\Hydrator
{

	use TEmailHydrator;

	/** @var string */
	protected $entityIdentifier = self::IDENTIFIER_KEY;

	/** @var string[] */
	protected $attributes = [
		0 => 'address',

		'is_default'  => 'default',
		'is_private'  => 'visibility',
		'is_verified' => 'verified',
	];

	/** @var string[] */
	protected $relationships = [
		Schemas\Emails\EmailSchema::RELATIONSHIPS_ACCOUNT,
	];

	/** @var string */
	protected $translationDomain = 'node.emails';

}
