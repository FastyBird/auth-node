<?php declare(strict_types = 1);

/**
 * AccountHydrator.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Hydrators
 * @since          0.1.0
 *
 * @date           15.08.20
 */

namespace FastyBird\AuthNode\Hydrators\Accounts;

use FastyBird\AuthNode\Types;
use FastyBird\NodeJsonApi\Exceptions as NodeJsonApiExceptions;
use FastyBird\NodeJsonApi\Hydrators as NodeJsonApiHydrators;
use Fig\Http\Message\StatusCodeInterface;
use IPub\JsonAPIDocument;

/**
 * Machine account entity hydrator
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Hydrators
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
abstract class AccountHydrator extends NodeJsonApiHydrators\Hydrator
{

	/** @var string */
	protected $entityIdentifier = self::IDENTIFIER_KEY;

	/** @var string[] */
	protected $attributes = [
		'state',
	];

	/** @var string */
	protected $translationDomain = 'node.accounts';

	/**
	 * @param JsonAPIDocument\Objects\IStandardObject<mixed> $attributes
	 *
	 * @return Types\AccountStateType
	 */
	protected function hydrateStateAttribute(
		JsonAPIDocument\Objects\IStandardObject $attributes
	): Types\AccountStateType {
		if (!Types\AccountStateType::isValidValue((string) $attributes->get('state'))) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('//node.base.messages.invalidAttribute.heading'),
				$this->translator->translate('//node.base.messages.invalidAttribute.message'),
				[
					'pointer' => '/data/attributes/state',
				]
			);
		}

		return Types\AccountStateType::get((string) $attributes->get('state'));
	}

}
