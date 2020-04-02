<?php declare(strict_types = 1);

/**
 * SecurityQuestionHydrator.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AccountsNode!
 * @subpackage     Hydrators
 * @since          0.1.0
 *
 * @date           31.03.20
 */

namespace FastyBird\AccountsNode\Hydrators;

use FastyBird\AccountsNode\Entities;
use IPub\JsonAPIDocument;

/**
 * Security question entity hydrator
 *
 * @package        FastyBird:AccountsNode!
 * @subpackage     Hydrators
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class SecurityQuestionHydrator extends Hydrator
{

	/** @var string */
	protected $entityIdentifier = self::IDENTIFIER_KEY;

	/** @var string[] */
	protected $attributes = [
		'question',
		'custom',
		'answer',
	];

	/**
	 * {@inheritdoc}
	 */
	protected function getEntityName(): string
	{
		return Entities\SecurityQuestions\Question::class;
	}

	/**
	 * @param JsonAPIDocument\Objects\IStandardObject<mixed> $attributes
	 *
	 * @return bool
	 */
	protected function hydrateCustomAttribute(JsonAPIDocument\Objects\IStandardObject $attributes): bool
	{
		$custom = $attributes->get('custom');

		return $custom === Entities\SecurityQuestions\IQuestion::CUSTOM_QUESTION;
	}

}
