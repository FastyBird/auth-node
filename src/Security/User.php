<?php declare(strict_types = 1);

/**
 * User.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AccountsNode!
 * @subpackage     Security
 * @since          0.1.0
 *
 * @date           31.03.20
 */

namespace FastyBird\AccountsNode\Security;

use FastyBird\AccountsNode\Entities;
use FastyBird\AccountsNode\Models;
use Nette\Security as NS;

/**
 * Application user
 *
 * @package        FastyBird:AccountsNode!
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
		Authorizator $authorizator,
		Models\Roles\IRoleRepository $roleRepository
	) {
		parent::__construct($storage, $authenticator, $authorizator);

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

			return $account !== null ? $account->getName() : 'Registered';
		}

		return 'Guest';
	}

	/**
	 * @return Entities\Roles\IRole[]
	 */
	public function getRoles(): array
	{
		if (!$this->isLoggedIn()) {
			$role = $this->roleRepository->findOneByKeyName(Entities\Roles\IRole::ROLE_ANONYMOUS);

			return $role !== null ? [$role] : [];
		}

		$account = $this->getAccount();

		if ($account !== null) {
			return $account->getRoles();
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
		if ($this->isLoggedIn() && $this->getRoles() === []) {
			return true;
		}

		foreach ($this->getRoles() as $role) {
			if (
				$this->getAuthorizator() === null
				|| $this->getAuthorizator()->isAllowed($role->getRoleId(), $resource, $privilege)
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
