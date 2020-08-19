<?php declare(strict_types = 1);

/**
 * UserAccountHydrator.php
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

namespace FastyBird\AuthNode\Hydrators\Accounts;

use Contributte\Translation;
use Doctrine\Common;
use FastyBird\AuthNode\Models;
use FastyBird\AuthNode\Schemas;
use FastyBird\NodeJsonApi\Hydrators as NodeJsonApiHydrators;

/**
 * User account entity hydrator
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Hydrators
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class UserAccountHydrator extends NodeJsonApiHydrators\Hydrator
{

	use TUserAccountHydrator;

	/** @var string */
	protected $entityIdentifier = self::IDENTIFIER_KEY;

	/** @var string[] */
	protected $attributes = [
		0 => 'details',
		1 => 'state',

		'first_name'  => 'firstName',
		'last_name'   => 'lastName',
		'middle_name' => 'middleName',
	];

	/** @var string[] */
	protected $compositedAttributes = [
		'params',
	];

	/** @var string[] */
	protected $relationships = [
		Schemas\Accounts\UserAccountSchema::RELATIONSHIPS_PARENT,
		Schemas\Accounts\UserAccountSchema::RELATIONSHIPS_ROLES,
	];

	/** @var string */
	protected $translationDomain = 'node.accounts';

	/** @var Models\Accounts\IAccountRepository */
	protected $accountRepository;

	/** @var Models\Roles\IRoleRepository */
	protected $roleRepository;

	public function __construct(
		Models\Accounts\IAccountRepository $accountRepository,
		Models\Roles\IRoleRepository $roleRepository,
		Common\Persistence\ManagerRegistry $managerRegistry,
		Translation\Translator $translator
	) {
		parent::__construct($managerRegistry, $translator);

		$this->accountRepository = $accountRepository;
		$this->roleRepository = $roleRepository;
	}

}
