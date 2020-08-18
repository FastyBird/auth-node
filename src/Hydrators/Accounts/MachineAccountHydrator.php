<?php declare(strict_types = 1);

/**
 * MachineAccountHydrator.php
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

use FastyBird\AuthNode\Entities;
use FastyBird\AuthNode\Types;
use FastyBird\NodeJsonApi\Exceptions as NodeJsonApiExceptions;
use FastyBird\NodeJsonApi\Hydrators as NodeJsonApiHydrators;
use Fig\Http\Message\StatusCodeInterface;
use IPub\JsonAPIDocument;
use Nette\Utils;

/**
 * Machine account entity hydrator
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Hydrators
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class MachineAccountHydrator extends NodeJsonApiHydrators\Hydrator
{

	/** @var string */
	protected $entityIdentifier = self::IDENTIFIER_KEY;

	/** @var string[] */
	protected $attributes = [
		'device',
		'state',
	];

	/**
	 * {@inheritDoc}
	 */
	protected function getEntityName(): string
	{
		return Entities\Accounts\MachineAccount::class;
	}

	/**
	 * @param JsonAPIDocument\Objects\IStandardObject<mixed> $attributes
	 *
	 * @return Types\AccountStateType
	 */
	protected function hydrateStateAttribute(
		JsonAPIDocument\Objects\IStandardObject $attributes
	): Types\AccountStateType {
		var_dump('TEST');
		if (!Types\AccountStateType::isValidValue((string) $attributes->get('state'))) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('//node.base.messages.attributeInvalid.heading'),
				$this->translator->translate('//node.base.messages.attributeInvalid.message'),
				[
					'pointer' => '/data/attributes/state',
				]
			);
		}

		return Types\AccountStateType::get((string) $attributes->get('state'));
	}

}
