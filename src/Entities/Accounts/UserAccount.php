<?php declare(strict_types = 1);

/**
 * UserAccount.php
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

namespace FastyBird\AuthNode\Entities\Accounts;

use Doctrine\Common;
use Doctrine\ORM\Mapping as ORM;
use FastyBird\AuthNode\Entities;
use IPub\DoctrineCrud\Mapping\Annotation as IPubDoctrine;
use Ramsey\Uuid;
use Throwable;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="fb_accounts_users",
 *     options={
 *       "collate"="utf8mb4_general_ci",
 *       "charset"="utf8mb4",
 *       "comment"="User accounts"
 *     },
 *     indexes={
 *       @ORM\Index(name="account_request_hash_idx", columns={"account_request_hash"})
 *     }
 * )
 */
class UserAccount extends Account implements IUserAccount
{

	/**
	 * @var Entities\Accounts\IUserAccount|null
	 *
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\ManyToOne(targetEntity="FastyBird\AuthNode\Entities\Accounts\UserAccount", inversedBy="children")
	 * @ORM\JoinColumn(name="parent_id", referencedColumnName="account_id", nullable=true, onDelete="set null")
	 */
	private $parent;

	/**
	 * @var Common\Collections\Collection<int, Entities\Accounts\IUserAccount>
	 *
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\OneToMany(targetEntity="FastyBird\AuthNode\Entities\Accounts\UserAccount", mappedBy="parent")
	 */
	private $children;

	/**
	 * @var Entities\Details\IDetails
	 *
	 * @IPubDoctrine\Crud(is={"required", "writable"})
	 * @ORM\OneToOne(targetEntity="FastyBird\AuthNode\Entities\Details\Details", mappedBy="account", cascade={"persist", "remove"})
	 */
	private $details;

	/**
	 * @var string|null
	 *
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\Column(type="string", name="account_request_hash", nullable=true, options={"default": null})
	 */
	private $requestHash = null;

	/**
	 * @var Common\Collections\Collection<int, Entities\Emails\IEmail>
	 *
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\OneToMany(targetEntity="FastyBird\AuthNode\Entities\Emails\Email", mappedBy="account", cascade={"persist", "remove"}, orphanRemoval=true)
	 */
	private $emails;

	/**
	 * @param Uuid\UuidInterface|null $id
	 *
	 * @throws Throwable
	 */
	public function __construct(
		?Uuid\UuidInterface $id = null
	) {
		parent::__construct($id);

		$this->emails = new Common\Collections\ArrayCollection();
		$this->children = new Common\Collections\ArrayCollection();
	}

	/**
	 * {@inheritDoc}
	 */
	public function setParent(Entities\Accounts\IUserAccount $account): void
	{
		$this->parent = $account;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getParent(): ?Entities\Accounts\IUserAccount
	{
		return $this->parent;
	}

	/**
	 * {@inheritDoc}
	 */
	public function removeParent(): void
	{
		$this->parent = null;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setChildren(array $children): void
	{
		$this->children = new Common\Collections\ArrayCollection();

		// Process all passed entities...
		/** @var Entities\Accounts\IUserAccount $entity */
		foreach ($children as $entity) {
			if (!$this->children->contains($entity)) {
				// ...and assign them to collection
				$this->children->add($entity);
			}
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function addChild(Entities\Accounts\IUserAccount $child): void
	{
		// Check if collection does not contain inserting entity
		if (!$this->children->contains($child)) {
			// ...and assign it to collection
			$this->children->add($child);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function getChildren(): array
	{
		return $this->children->toArray();
	}

	/**
	 * {@inheritDoc}
	 */
	public function removeChild(Entities\Accounts\IUserAccount $child): void
	{
		// Check if collection contain removing entity...
		if ($this->children->contains($child)) {
			// ...and remove it from collection
			$this->children->removeElement($child);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDetails(): Entities\Details\IDetails
	{
		return $this->details;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setRequestHash(string $requestHash): void
	{
		$this->requestHash = $requestHash;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getRequestHash(): ?string
	{
		return $this->requestHash;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setEmails(array $emails): void
	{
		$this->emails = new Common\Collections\ArrayCollection();

		// Process all passed entities...
		/** @var Entities\Emails\IEmail $entity */
		foreach ($emails as $entity) {
			if (!$this->emails->contains($entity)) {
				// ...and assign them to collection
				$this->emails->add($entity);
			}
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function addEmail(Entities\Emails\IEmail $email): void
	{
		// Check if collection does not contain inserting entity
		if (!$this->emails->contains($email)) {
			// ...and assign it to collection
			$this->emails->add($email);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function getEmails(): array
	{
		return $this->emails->toArray();
	}

	/**
	 * {@inheritDoc}
	 */
	public function removeEmail(Entities\Emails\IEmail $email): void
	{
		// Check if collection contain removing entity...
		if ($this->emails->contains($email)) {
			// ...and remove it from collection
			$this->emails->removeElement($email);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function getEmail(?string $id = null): ?Entities\Emails\IEmail
	{
		if ($this->emails !== null) {
			$email = $this->emails
				->filter(function (Entities\Emails\IEmail $row) use ($id): bool {
					return $id !== null ? $row->getId()->equals(Uuid\Uuid::fromString($id)) : $row->isDefault();
				})
				->first();

			return $email !== false ? $email : null;
		}

		return null;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getName(): string
	{
		return $this->details->getLastName() . ' ' . $this->details->getFirstName();
	}

	/**
	 * {@inheritDoc}
	 *
	 * TODO: Should be refactored
	 */
	public function getLanguage(): string
	{
		return 'en';
	}

}
