<?php declare(strict_types = 1);

/**
 * UserAccountHydrator.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Hydrators
 * @since          0.1.0
 *
 * @date           31.03.20
 */

namespace FastyBird\AuthNode\Hydrators\Accounts;

use Contributte\Translation;
use Doctrine\Common;
use FastyBird\AuthNode\Entities;
use FastyBird\AuthNode\Models;
use FastyBird\AuthNode\Queries;
use FastyBird\AuthNode\Schemas;
use FastyBird\AuthNode\Types;
use FastyBird\NodeJsonApi\Exceptions as NodeJsonApiExceptions;
use FastyBird\NodeJsonApi\Hydrators as NodeJsonApiHydrators;
use Fig\Http\Message\StatusCodeInterface;
use IPub\JsonAPIDocument;
use Nette\Utils;
use Ramsey\Uuid;
use stdClass;

/**
 * User account entity hydrator
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Hydrators
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class UserAccountHydrator extends NodeJsonApiHydrators\Hydrator
{

	/** @var string */
	protected $entityIdentifier = self::IDENTIFIER_KEY;

	/** @var string[] */
	protected $attributes = [
		0 => 'details',
		1 => 'state',

		'first_name'  => 'firstName',
		'last_name'   => 'lastName',
		'middle_name' => 'middleName',
	];

	/** @var string[] */
	protected $compositedAttributes = [
		'params',
	];

	/** @var string[] */
	protected $relationships = [
		Schemas\Accounts\UserAccountSchema::RELATIONSHIPS_PARENT,
		Schemas\Accounts\UserAccountSchema::RELATIONSHIPS_ROLES,
	];

	/** @var string */
	protected $translationDomain = 'node.accounts';

	/** @var Models\Accounts\IAccountRepository */
	private $accountRepository;

	/** @var Models\Roles\IRoleRepository */
	private $roleRepository;

	public function __construct(
		Models\Accounts\IAccountRepository $accountRepository,
		Models\Roles\IRoleRepository $roleRepository,
		Common\Persistence\ManagerRegistry $managerRegistry,
		Translation\Translator $translator
	) {
		parent::__construct($managerRegistry, $translator);

		$this->accountRepository = $accountRepository;
		$this->roleRepository = $roleRepository;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function getEntityName(): string
	{
		return Entities\Accounts\UserAccount::class;
	}

	/**
	 * @param JsonAPIDocument\Objects\IStandardObject<mixed> $attributes
	 *
	 * @return string
	 *
	 * @throws NodeJsonApiExceptions\IJsonApiException
	 */
	protected function hydrateFirstNameAttribute(JsonAPIDocument\Objects\IStandardObject $attributes): string
	{
		if (!$attributes->has('first_name')) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('//node.base.messages.missingRequired.heading'),
				$this->translator->translate('//node.base.messages.missingRequired.message'),
				[
					'pointer' => '/data/attributes/details/first_name',
				]
			);
		}

		return (string) $attributes->get('first_name');
	}

	/**
	 * @param JsonAPIDocument\Objects\IStandardObject<mixed> $attributes
	 *
	 * @return string
	 *
	 * @throws NodeJsonApiExceptions\IJsonApiException
	 */
	protected function hydrateLastNameAttribute(JsonAPIDocument\Objects\IStandardObject $attributes): string
	{
		if (!$attributes->has('last_name')) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('//node.base.messages.missingRequired.heading'),
				$this->translator->translate('//node.base.messages.missingRequired.message'),
				[
					'pointer' => '/data/attributes/details/last_name',
				]
			);
		}

		return (string) $attributes->get('last_name');
	}

	/**
	 * @param JsonAPIDocument\Objects\IStandardObject<mixed> $attributes
	 *
	 * @return string|null
	 */
	protected function hydrateMiddleNameAttribute(JsonAPIDocument\Objects\IStandardObject $attributes): ?string
	{
		return $attributes->has('middle_name') && (string) $attributes->get('middle_name') !== '' ? (string) $attributes->get('middle_name') : null;
	}

	/**
	 * @param JsonAPIDocument\Objects\IStandardObject<mixed> $attributes
	 *
	 * @return Utils\ArrayHash|null
	 *
	 * @throws NodeJsonApiExceptions\JsonApiErrorException
	 */
	protected function hydrateDetailsAttribute(
		JsonAPIDocument\Objects\IStandardObject $attributes
	): ?Utils\ArrayHash {
		if ($attributes->has('details')) {
			$details = $attributes->get('details');

			$update = new Utils\ArrayHash();
			$update['entity'] = Entities\Details\Details::class;

			if ($details->has('first_name')) {
				$update->offsetSet('firstName', $details->get('first_name'));

			} else {
				throw new NodeJsonApiExceptions\JsonApiErrorException(
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
				throw new NodeJsonApiExceptions\JsonApiErrorException(
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
	 * @return Types\AccountStateType
	 */
	protected function hydrateStateAttribute(
		JsonAPIDocument\Objects\IStandardObject $attributes
	): Types\AccountStateType {
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

	/**
	 * @param JsonAPIDocument\Objects\IStandardObject<mixed> $attributes
	 *
	 * @return Utils\ArrayHash|null
	 */
	protected function hydrateParamsAttribute(
		JsonAPIDocument\Objects\IStandardObject $attributes
	): ?Utils\ArrayHash {
		$params = Utils\ArrayHash::from([
			'datetime' => [
				'format' => [],
			],
		]);

		if ($attributes->has('week_start')) {
			$params['datetime']->offsetSet('week_start', (int) $attributes->get('week_start'));
		}

		if (
			$attributes->has('datetime')
			&& $attributes->get('datetime') instanceof JsonAPIDocument\Objects\IStandardObject
		) {
			$datetime = $attributes->get('datetime');

			if ($datetime->has('timezone')) {
				$params['datetime']->offsetSet('zone', (string) $datetime->get('timezone'));
			}

			if ($datetime->has('date_format')) {
				$params['datetime']['format']->offsetSet('date', (string) $datetime->get('date_format'));
			}

			if ($datetime->has('time_format')) {
				$params['datetime']['format']->offsetSet('time', (string) $datetime->get('time_format'));
			}
		}

		return $params;
	}

	/**
	 * @param JsonAPIDocument\Objects\IRelationship<mixed> $relationship
	 *
	 * @return Entities\Accounts\IUserAccount
	 *
	 * @throws NodeJsonApiExceptions\IJsonApiException
	 */
	protected function hydrateParentRelationship(
		JsonAPIDocument\Objects\IRelationship $relationship
	): Entities\Accounts\IUserAccount {
		if (
			!$relationship->isHasOne()
			|| $relationship->getIdentifier() === null
			|| !Uuid\Uuid::isValid($relationship->getIdentifier()->getId())
			|| !$relationship->getData() instanceof JsonAPIDocument\Objects\IResourceIdentifier
			|| $relationship->getData()->get('type') !== Schemas\Accounts\UserAccountSchema::SCHEMA_TYPE
		) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_NOT_FOUND,
				$this->translator->translate('//node.base.messages.relationNotFound.heading'),
				$this->translator->translate('//node.base.messages.relationNotFound.message'),
				[
					'pointer' => '/data/relationships/parent/data/id',
				]
			);
		}

		$findQuery = new Queries\FindAccountsQuery();
		$findQuery->byId(Uuid\Uuid::fromString($relationship->getIdentifier()->getId()));

		/** @var Entities\Accounts\IUserAccount|null $account */
		$account = $this->accountRepository->findOneBy($findQuery);

		if ($account === null) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_NOT_FOUND,
				$this->translator->translate('//node.base.messages.relationNotFound.heading'),
				$this->translator->translate('//node.base.messages.relationNotFound.message'),
				[
					'pointer' => '/data/relationships/parent/data/id',
				]
			);
		}

		return $account;
	}

	/**
	 * @param JsonAPIDocument\Objects\IRelationship<mixed> $relationship
	 *
	 * @return Entities\Roles\IRole[]
	 *
	 * @throws NodeJsonApiExceptions\IJsonApiException
	 */
	protected function hydrateRolesRelationship(
		JsonAPIDocument\Objects\IRelationship $relationship
	): ?array {
		if (!$relationship->isHasMany()) {
			return null;
		}

		$roles = [];

		foreach ($relationship as $rolesRelation) {
			/** @var stdClass $roleRelation */
			foreach ($rolesRelation as $roleRelation) {
				try {
					$findQuery = new Queries\FindRolesQuery();
					$findQuery->byId(Uuid\Uuid::fromString($roleRelation->id));

					$role = $this->roleRepository->findOneBy($findQuery);

					if ($role !== null) {
						$roles[] = $role;
					}

				} catch (Uuid\Exception\InvalidUuidStringException $ex) {
					throw new NodeJsonApiExceptions\JsonApiErrorException(
						StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
						$this->translator->translate('//node.base.messages.identifierInvalid.heading'),
						$this->translator->translate('//node.base.messages.identifierInvalid.message'),
						[
							'pointer' => '/data/relationships/roles/data/id',
						]
					);
				}
			}
		}

		return $roles;
	}

}
