<?php declare(strict_types = 1);

/**
 * Question.php
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

namespace FastyBird\AccountsNode\Entities\SecurityQuestions;

use Doctrine\ORM\Mapping as ORM;
use FastyBird\AccountsNode\Entities;
use IPub\DoctrineCrud\Mapping\Annotation as IPubDoctrine;
use IPub\DoctrineTimestampable;
use Ramsey\Uuid;
use Throwable;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="fb_security_questions",
 *     options={
 *       "collate"="utf8mb4_general_ci",
 *       "charset"="utf8mb4",
 *       "comment"="Accounts security questions"
 *     }
 * )
 */
class Question extends Entities\Entity implements IQuestion
{

	use DoctrineTimestampable\Entities\TEntityCreated;
	use DoctrineTimestampable\Entities\TEntityUpdated;

	/**
	 * @var Uuid\UuidInterface
	 *
	 * @ORM\Id
	 * @ORM\Column(type="uuid_binary", name="question_id")
	 * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
	 */
	protected $id;

	/**
	 * @var Entities\Accounts\IAccount
	 *
	 * @ORM\OneToOne(targetEntity="FastyBird\AccountsNode\Entities\Accounts\Account", inversedBy="securityQuestion")
	 * @ORM\JoinColumn(name="account_id", referencedColumnName="account_id", unique=true, onDelete="cascade", nullable=false)
	 */
	private $account;

	/**
	 * @var string
	 *
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\Column(type="string", name="question_question", length=250, nullable=false)
	 */
	private $question;

	/**
	 * @var string
	 *
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\Column(type="string", name="question_answer", length=250, nullable=false)
	 */
	private $answer;

	/**
	 * @var bool
	 *
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\Column(type="boolean", name="question_custom", length=1, nullable=false, options={"default": false})
	 */
	private $custom = false;

	/**
	 * @param Entities\Accounts\IAccount $account
	 * @param string $question
	 * @param string $answer
	 * @param Uuid\UuidInterface|null $id
	 *
	 * @throws Throwable
	 */
	public function __construct(
		Entities\Accounts\IAccount $account,
		string $question,
		string $answer,
		?Uuid\UuidInterface $id = null
	) {
		$this->id = $id ?? Uuid\Uuid::uuid4();

		$this->account = $account;
		$this->question = $question;
		$this->answer = $answer;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getAccount(): Entities\Accounts\IAccount
	{
		return $this->account;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setQuestion(string $question): void
	{
		$this->question = $question;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getQuestion(): string
	{
		return $this->question;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setAnswer(string $answer): void
	{
		$this->answer = $answer;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getAnswer(): string
	{
		return $this->answer;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setCustom(bool $custom): void
	{
		$this->custom = $custom;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isCustom(): bool
	{
		return $this->custom;
	}

}
