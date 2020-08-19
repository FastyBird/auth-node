<?php declare(strict_types = 1);

/**
 * AccountsManager.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Models
 * @since          0.1.0
 *
 * @date           30.03.20
 */

namespace FastyBird\AuthNode\Models\Accounts;

use FastyBird\AuthNode;
use FastyBird\AuthNode\Entities;
use FastyBird\AuthNode\Exceptions;
use FastyBird\AuthNode\Models;
use FastyBird\AuthNode\Queries;
use IPub\DoctrineCrud\Crud;
use Nette;
use Nette\Utils;

/**
 * Accounts entities manager
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class AccountsManager implements IAccountsManager
{

	use Nette\SmartObject;

	/** @var Models\Roles\IRoleRepository */
	private $roleRepository;

	/** @var Crud\IEntityCrud */
	private $entityCrud;

	public function __construct(
		Models\Roles\IRoleRepository $roleRepository,
		Crud\IEntityCrud $entityCrud
	) {
		$this->roleRepository = $roleRepository;

		// Entity CRUD for handling entities
		$this->entityCrud = $entityCrud;
	}

	/**
	 * {@inheritDoc}
	 */
	public function create(
		Utils\ArrayHash $values
	): Entities\Accounts\IAccount {
		if ($values->offsetExists('entity')) {
			if ($values->offsetGet('entity') === Entities\Accounts\UserAccount::class) {
				if (!$values->offsetExists('roles')) {
					$values->offsetSet('roles', $this->getDefaultRoles(AuthNode\Constants::USER_ACCOUNT_DEFAULT_ROLES));
				}

			} elseif ($values->offsetGet('entity') === Entities\Accounts\MachineAccount::class) {
				$values->offsetSet('roles', $this->getDefaultRoles(AuthNode\Constants::MACHINE_ACCOUNT_DEFAULT_ROLES));
			}
		}

		/** @var Entities\Accounts\IAccount $entity */
		$entity = $this->entityCrud->getEntityCreator()->create($values);

		return $entity;
	}

	/**
	 * {@inheritDoc}
	 */
	public function update(
		Entities\Accounts\IAccount $entity,
		Utils\ArrayHash $values
	): Entities\Accounts\IAccount {
		/** @var Entities\Accounts\IAccount $entity */
		$entity = $this->entityCrud->getEntityUpdater()->update($values, $entity);

		return $entity;
	}

	/**
	 * {@inheritDoc}
	 */
	public function delete(
		Entities\Accounts\IAccount $entity
	): bool {
		// Delete entity from database
		return $this->entityCrud->getEntityDeleter()->delete($entity);
	}

	/**
	 * @param string[] $roleNames
	 *
	 * @return Entities\Roles\IRole[]
	 */
	private function getDefaultRoles(array $roleNames): array
	{
		$roles = [];

		foreach ($roleNames as $roleName) {
			$findRole = new Queries\FindRolesQuery();
			$findRole->byName($roleName);

			$role = $this->roleRepository->findOneBy($findRole);

			if ($role === null) {
				throw new Exceptions\InvalidStateException('Default role is not created');
			}

			$roles[] = $role;
		}

		return $roles;
	}

}
