<?php declare(strict_types = 1);

/**
 * UserAccountIdentity.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Entities
 * @since          0.1.0
 *
 * @date           30.03.20
 */

namespace FastyBird\AuthNode\Entities\Identities;

use Doctrine\ORM\Mapping as ORM;
use FastyBird\AuthNode\Entities;
use FastyBird\AuthNode\Exceptions;
use FastyBird\AuthNode\Helpers;
use IPub\DoctrineCrud\Mapping\Annotation as IPubDoctrine;
use Throwable;

/**
 * User account identity entity
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 *
 * @method Entities\Accounts\IUserAccount getAccount()
 *
 * @ORM\Entity
 */
class UserAccountIdentity extends Identity implements IUserAccountIdentity
{

	/**
	 * Identity constants
	 */
	public const UID_MAXIMAL_LENGTH = 50;
	public const PASSWORD_MINIMAL_LENGTH = 8;

	/**
	 * @var string
	 *
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\Column(type="text", name="identity_token", nullable=false)
	 */
	private $password;

	/**
	 * @param Entities\Accounts\IUserAccount $account
	 * @param string $uid
	 * @param string $password
	 *
	 * @throws Throwable
	 */
	public function __construct(
		Entities\Accounts\IUserAccount $account,
		string $uid,
		string $password
	) {
		parent::__construct($account, $uid);

		$this->setPassword($password);
	}

	/**
	 * {@inheritDoc}
	 */
	public function setPassword($password): void
	{
		if ($password instanceof Helpers\Password) {
			$this->password = $password->getHash();

		} elseif (is_string($password)) {
			$password = Helpers\Password::createFromString($password);

			$this->password = $password->getHash();

		} else {
			throw new Exceptions\InvalidArgumentException(sprintf('Provided password value is not valid type. Expected string or instance of %s, instance of %s provided', Helpers\Password::class, gettype($password)));
		}

		$this->setSalt($password->getSalt());
	}

	/**
	 * {@inheritDoc}
	 */
	public function getPassword(): Helpers\Password
	{
		return new Helpers\Password($this->password, null, $this->getSalt());
	}

	/**
	 * {@inheritDoc}
	 */
	public function verifyPassword(string $rawPassword): bool
	{
		return $this->getPassword()
			->isEqual($rawPassword, $this->getSalt());
	}

	/**
	 * {@inheritDoc}
	 */
	public function setSalt(string $salt): void
	{
		$this->setParam('salt', $salt);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getSalt(): ?string
	{
		return $this->getParam('salt', null);
	}

}
