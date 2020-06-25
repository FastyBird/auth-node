<?php declare(strict_types = 1);

/**
 * Authorizator.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Security
 * @since          0.1.0
 *
 * @date           31.03.20
 */

namespace FastyBird\AuthNode\Security;

use FastyBird\AuthNode\Entities;
use FastyBird\AuthNode\Models;
use FastyBird\AuthNode\Queries;
use Nette;
use Nette\Security as NS;

/**
 * Account authenticator
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Security
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class Authorizator extends NS\Permission implements NS\IAuthorizator
{

	use Nette\SmartObject;

	/** @var bool */
	private $initialized = false;

	/** @var Models\Roles\IRoleRepository */
	private $roleRepository;

	/** @var Models\Resources\IResourceRepository */
	private $resourceRepository;

	public function __construct(
		Models\Roles\IRoleRepository $roleRepository,
		Models\Resources\IResourceRepository $resourceRepository
	) {
		$this->roleRepository = $roleRepository;
		$this->resourceRepository = $resourceRepository;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isAllowed($role = self::ALL, $resource = self::ALL, $privilege = self::ALL): bool
	{
		if (!$this->initialized) {
			$this->initialize();
		}

		return parent::isAllowed($role, $resource, $privilege);
	}

	/**
	 * @return void
	 */
	private function initialize(): void
	{
		$this->initialized = true;

		$findResource = new Queries\FindResourcesQuery();
		$findResource->withoutParent();

		// Get all available resources
		$resources = $this->resourceRepository->findAllBy($findResource);

		foreach ($resources as $resource) {
			$this->registerResource($resource);
		}

		// Get all available roles
		$roles = $this->roleRepository->findAll();

		// Register all available roles
		foreach ($roles as $role) {
			$this->registerRole($role);

			// Allow all privileges for administrator
			if ($role->isAdministrator()) {
				$this->allow($role->getRoleId(), self::ALL, self::ALL);

				// For others apply setup privileges
			} else {
				foreach ($role->getRules() as $rule) {
					if ($rule->hasAccess()) {
						$this->allow(
							$role->getRoleId(),
							$rule->getResource()->getResourceId(),
							$rule->getPrivilege()->getPrivilegeId()
						);

					} else {
						$this->deny(
							$role->getRoleId(),
							$rule->getResource()->getResourceId(),
							$rule->getPrivilege()->getPrivilegeId()
						);
					}
				}
			}
		}
	}

	/**
	 * @param Entities\Roles\IRole $role
	 *
	 * @return void
	 */
	private function registerRole(
		Entities\Roles\IRole $role
	): void {
		$roleParent = $role->getParent();

		if ($roleParent !== null) {
			$this->registerRole($roleParent);
		}

		// Assign role to application permission checker
		if (!$this->hasRole($role->getRoleId())) {
			$this->addRole($role->getRoleId(), $roleParent !== null ? $roleParent->getRoleId() : null);
		}
	}

	/**
	 * @param Entities\Resources\IResource $resource
	 *
	 * @return void
	 */
	private function registerResource(
		Entities\Resources\IResource $resource
	): void {
		$resourceParent = $resource->getParent();

		// Assign resource to application permission checker
		$this->addResource($resource->getResourceId(), $resourceParent !== null ? $resourceParent->getResourceId() : null);

		foreach ($resource->getChildren() as $child) {
			$this->registerResource($child);
		}
	}

}
