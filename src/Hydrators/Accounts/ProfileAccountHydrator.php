<?php declare(strict_types = 1);

/**
 * ProfileAccountHydrator.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Hydrators
 * @since          0.1.0
 *
 * @date           19.08.20
 */

namespace FastyBird\AuthNode\Hydrators\Accounts;

/**
 * User account entity hydrator
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Hydrators
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class ProfileAccountHydrator extends AccountHydrator
{

	use TUserAccountHydrator;

	/** @var string[] */
	protected $attributes = [
		0 => 'details',

		'first_name'  => 'firstName',
		'last_name'   => 'lastName',
		'middle_name' => 'middleName',
	];

	/** @var string[] */
	protected $compositedAttributes = [
		'params',
	];

}
