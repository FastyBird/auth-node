<?php declare(strict_types = 1);

/**
 * MachineAccountIdentity.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
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
use IPub\DoctrineCrud\Mapping\Annotation as IPubDoctrine;
use Throwable;

/**
 * Machine account identity entity
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
class MachineAccountIdentity extends Identity implements IMachineAccountIdentity
{

	/**
	 * @var string
	 *
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\Column(type="text", name="identity_token", nullable=false)
	 */
	private $password;

	/**
	 * @param Entities\Accounts\IMachineAccount $account
	 * @param string $uid
	 * @param string $password
	 *
	 * @throws Throwable
	 */
	public function __construct(
		Entities\Accounts\IMachineAccount $account,
		string $uid,
		string $password
	) {
		parent::__construct($account, $uid);

		$this->setPassword($password);
	}

	/**
	 * {@inheritDoc}
	 */
	public function setPassword(string $password): void
	{
		$this->password = $password;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getPassword(): string
	{
		return $this->password;
	}

	/**
	 * {@inheritDoc}
	 */
	public function verifyPassword(string $password): bool
	{
		return $this->password === $password;
	}

}
