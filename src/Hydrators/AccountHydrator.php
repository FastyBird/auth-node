<?php declare(strict_types = 1);

/**
 * AccountHydrator.php
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
use FastyBird\NodeWebServer\Exceptions as NodeWebServerExceptions;
use Fig\Http\Message\StatusCodeInterface;
use IPub\JsonAPIDocument;
use Nette\Utils;

/**
 * Account entity hydrator
 *
 * @package        FastyBird:AccountsNode!
 * @subpackage     Hydrators
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class AccountHydrator extends Hydrator
{

	/** @var string */
	protected $entityIdentifier = self::IDENTIFIER_KEY;

	/** @var string[] */
	protected $attributes = [
		'details',
		'params',
	];

	/**
	 * {@inheritdoc}
	 */
	protected function getEntityName(): string
	{
		return Entities\Accounts\Account::class;
	}

	/**
	 * @param JsonAPIDocument\Objects\IStandardObject<mixed> $attributes
	 * @param Entities\Accounts\IAccount $entity
	 *
	 * @return Utils\ArrayHash|null
	 *
	 * @throws NodeWebServerExceptions\JsonApiErrorException
	 */
	protected function hydrateDetailsAttribute(
		JsonAPIDocument\Objects\IStandardObject $attributes,
		Entities\Accounts\IAccount $entity
	): ?Utils\ArrayHash {
		if ($attributes->has('details')) {
			$details = $attributes->get('details');

			$update = new Utils\ArrayHash();
			$update['entity'] = Entities\Details\Details::class;
			$update['id'] = $entity->getDetails()->getId();

			if ($details->has('first_name')) {
				$update->offsetSet('firstName', $details->get('first_name'));

			} else {
				throw new NodeWebServerExceptions\JsonApiErrorException(
					StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
					$this->translator->translate('//node.base.messages.missingRequired.heading'),
					$this->translator->translate('//node.base.messages.missingRequired.message'),
					[
						'pointer' => '/data/attributes/details/first_name',
					]
				);
			}

			if ($details->has('last_name')) {
				$update->offsetSet('lastName', $details->get('last_name'));

			} else {
				throw new NodeWebServerExceptions\JsonApiErrorException(
					StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
					$this->translator->translate('//node.base.messages.missingRequired.heading'),
					$this->translator->translate('//node.base.messages.missingRequired.message'),
					[
						'pointer' => '/data/attributes/details/last_name',
					]
				);
			}

			if ($details->has('middle_name') && $details->get('middle_name') !== '') {
				$update->offsetSet('middleName', $details->get('middle_name'));

			} else {
				$update->offsetSet('middleName', null);
			}

			return $update;
		}

		return null;
	}

	/**
	 * @param JsonAPIDocument\Objects\IStandardObject<mixed> $attributes
	 *
	 * @return Utils\ArrayHash|null
	 */
	protected function hydrateParamsAttribute(
		JsonAPIDocument\Objects\IStandardObject $attributes
	): ?Utils\ArrayHash {
		if ($attributes->has('params')) {
			if ($attributes->get('params') instanceof JsonAPIDocument\Objects\IStandardObject) {
				return Utils\ArrayHash::from($attributes->get('params')->toArray());

			} elseif ($attributes->get('params') !== null) {
				return Utils\ArrayHash::from((array) $attributes->get('params'));
			}
		}

		return null;
	}

}
