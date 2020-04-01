<?php declare(strict_types = 1);

/**
 * System.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AccountsNode!
 * @subpackage     Entities
 * @since          0.1.0
 *
 * @date           30.03.20
 */

namespace FastyBird\AccountsNode\Entities\Identities;

use Doctrine\ORM\Mapping as ORM;
use FastyBird\AccountsNode\Entities;
use FastyBird\AccountsNode\Exceptions;
use FastyBird\AccountsNode\Helpers;
use IPub\DoctrineCrud\Mapping\Annotation as IPubDoctrine;
use Throwable;

/**
 * Account profile identity entity
 *
 * @package        FastyBird:AccountsNode!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 *
 * @ORM\Entity
 */
class System extends Identity implements Entities\IEntityParams
{

	use Entities\TEntityParams;

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
	 * @param Entities\Accounts\IAccount $account
	 * @param string $uid
	 * @param string $password
	 * @param string|null $email
	 *
	 * @throws Throwable
	 */
	public function __construct(
		Entities\Accounts\IAccount $account,
		string $uid,
		string $password,
		?string $email = null
	) {
		parent::__construct($account, $uid, $email);

		$this->setPassword($password);
	}

	/**
	 * @param string|Helpers\Password $password
	 *
	 * @return void
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
	 * @return Helpers\Password
	 */
	public function getPassword(): Helpers\Password
	{
		return new Helpers\Password($this->password, null, $this->getSalt());
	}

	/**
	 * @param string $rawPassword
	 *
	 * @return bool
	 */
	public function verifyPassword(string $rawPassword): bool
	{
		return $this->getPassword()
			->isEqual($rawPassword, $this->getSalt());
	}

	/**
	 * @param string $salt
	 *
	 * @return void
	 */
	public function setSalt(string $salt): void
	{
		$this->setParam('salt', $salt);
	}

	/**
	 * @return string|null
	 */
	public function getSalt(): ?string
	{
		return $this->getParam('salt', null);
	}

}
