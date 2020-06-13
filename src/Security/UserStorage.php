<?php declare(strict_types = 1);

/**
 * UserStorage.php
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
use Nette\Security as NS;

/**
 * Application user storage for authentication
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Security
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class UserStorage implements NS\IUserStorage
{

	/** @var Entities\Identities\IIdentity|null */
	private $identity = null;

	/** @var int */
	private $logoutReason;

	/**
	 * {@inheritDoc}
	 */
	public function setAuthenticated(bool $state)
	{
		if (!$state) {
			$this->logoutReason = self::MANUAL;
		}

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isAuthenticated(): bool
	{
		return $this->getIdentity() !== null;
	}

	/**
	 * @param Entities\Identities\IIdentity|null $identity
	 *
	 * @return static
	 */
	public function setIdentity(?NS\IIdentity $identity = null)
	{
		$this->identity = $identity;

		return $this;
	}

	/**
	 * @return Entities\Identities\IIdentity|null
	 */
	public function getIdentity(): ?NS\IIdentity
	{
		return $this->identity;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setExpiration(?string $expire, int $flags = 0)
	{
		// Nothing to do here...
		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getLogoutReason(): ?int
	{
		return $this->logoutReason;
	}

}
