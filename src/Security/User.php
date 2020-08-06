<?php declare(strict_types = 1);

/**
 * User.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
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
use FastyBird\NodeAuth\Constants as NodeAuthConstants;
use Nette\Security as NS;

/**
 * Application user
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Security
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class User extends NS\User
{

	/** @var Models\Roles\IRoleRepository */
	private $roleRepository;

	public function __construct(
		UserStorage $storage,
		Authenticator $authenticator,
		Models\Roles\IRoleRepository $roleRepository
	) {
		parent::__construct($storage, $authenticator);

		$this->roleRepository = $roleRepository;
	}

	/**
	 * @return string|null
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
	 */
	public function getId()
	{
		return $this->getAccount() !== null ? $this->getAccount()->getPlainId() : null;
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		if ($this->isLoggedIn()) {
			$account = $this->getAccount();

			return $account !== null ? ($account instanceof Entities\Accounts\IUserAccount ? $account->getName() : 'Machine') : 'Registered';
		}

		return 'Guest';
	}

	/**
	 * @return string[]
	 */
	public function getRoles(): array
	{
		if (!$this->isLoggedIn()) {
			$role = $this->roleRepository->findOneByName(NodeAuthConstants::ROLE_ANONYMOUS);

			return $role !== null ? [$role->getRoleId()] : [];
		}

		$account = $this->getAccount();

		if ($account !== null) {
			return array_map(function (Entities\Roles\IRole $role): string {
				return $role->getRoleId();
			}, $account->getRoles());
		}

		return [];
	}

	/**
	 * @param mixed $resource
	 * @param mixed $privilege
	 *
	 * @return bool
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function isAllowed($resource = NS\IAuthorizator::ALL, $privilege = NS\IAuthorizator::ALL): bool
	{
		// User is logged in but application is without ACL system
		if ($this->getRoles() === []) {
			return true;
		}

		foreach ($this->getRoles() as $role) {
			if (
				$this->getAuthorizator() === null
				|| $this->getAuthorizator()->isAllowed($role, $resource, $privilege)
			) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @return Entities\Accounts\IAccount|null
	 */
	public function getAccount(): ?Entities\Accounts\IAccount
	{
		if ($this->isLoggedIn()) {
			$identity = $this->getIdentity();

			if ($identity instanceof Entities\Identities\IIdentity) {
				return $identity->getAccount();
			}
		}

		return null;
	}

}
