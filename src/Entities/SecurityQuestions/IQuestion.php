<?php declare(strict_types = 1);

/**
 * IQuestion.php
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

namespace FastyBird\AuthNode\Entities\SecurityQuestions;

use FastyBird\AuthNode\Entities;
use FastyBird\NodeDatabase\Entities as NodeDatabaseEntities;
use IPub\DoctrineTimestampable;

/**
 * Security question entity interface
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IQuestion extends NodeDatabaseEntities\IEntity,
	DoctrineTimestampable\Entities\IEntityCreated,
	DoctrineTimestampable\Entities\IEntityUpdated
{

	public const CUSTOM_QUESTION = 'custom';

	/**
	 * @return Entities\Accounts\IAccount
	 */
	public function getAccount(): Entities\Accounts\IAccount;

	/**
	 * @param string $question
	 *
	 * @return void
	 */
	public function setQuestion(string $question): void;

	/**
	 * @return string
	 */
	public function getQuestion(): string;

	/**
	 * @param string $answer
	 *
	 * @return void
	 */
	public function setAnswer(string $answer): void;

	/**
	 * @return string
	 */
	public function getAnswer(): string;

	/**
	 * @param bool $custom
	 *
	 * @return void
	 */
	public function setCustom(bool $custom): void;

	/**
	 * @return bool
	 */
	public function isCustom(): bool;

}
