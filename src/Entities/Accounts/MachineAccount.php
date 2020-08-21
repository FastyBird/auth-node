<?php declare(strict_types = 1);

/**
 * MachineAccount.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Entities
 * @since          0.1.0
 *
 * @date           11.06.20
 */

namespace FastyBird\AuthNode\Entities\Accounts;

use Doctrine\ORM\Mapping as ORM;
use IPub\DoctrineCrud\Mapping\Annotation as IPubDoctrine;
use Ramsey\Uuid;
use Throwable;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="fb_accounts_machines",
 *     options={
 *       "collate"="utf8mb4_general_ci",
 *       "charset"="utf8mb4",
 *       "comment"="Machine accounts"
 *     },
 *     uniqueConstraints={
 *       @ORM\UniqueConstraint(name="account_device_unique", columns={"account_device"})
 *     }
 * )
 */
class MachineAccount extends Account implements IMachineAccount
{

	/**
	 * @var string
	 *
	 * @IPubDoctrine\Crud(is={"required", "writable"})
	 * @ORM\Column(type="string", name="account_device", length=150, nullable=false)
	 */
	private $device;

	/**
	 * @param string $device
	 * @param Uuid\UuidInterface|null $id
	 *
	 * @throws Throwable
	 */
	public function __construct(
		string $device,
		?Uuid\UuidInterface $id = null
	) {
		parent::__construct($id);

		$this->device = $device;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDevice(): string
	{
		return $this->device;
	}

	/**
	 * {@inheritDoc}
	 */
	public function toArray(): array
	{
		return array_merge(parent::toArray(), [
			'type'   => 'machine',
			'device' => $this->getDevice(),
		]);
	}

}
