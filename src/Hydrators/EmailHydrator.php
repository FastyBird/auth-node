<?php declare(strict_types = 1);

/**
 * EmailHydrator.php
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

use Contributte\Translation;
use Doctrine\Common;
use FastyBird\AccountsNode\Entities;
use FastyBird\AccountsNode\Security;
use FastyBird\NodeWebServer\Exceptions as NodeWebServerExceptions;
use Fig\Http\Message\StatusCodeInterface;

/**
 * Email entity hydrator
 *
 * @package        FastyBird:AccountsNode!
 * @subpackage     Hydrators
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class EmailHydrator extends Hydrator
{

	/** @var string */
	protected $entityIdentifier = self::IDENTIFIER_KEY;

	/** @var string[] */
	protected $attributes = [
		'address',
		'is_default',
		'account',
	];

	/** @var Security\User */
	private $user;

	public function __construct(
		Security\User $user,
		Common\Persistence\ManagerRegistry $managerRegistry,
		Translation\Translator $translator
	) {
		parent::__construct($managerRegistry, $translator);

		$this->user = $user;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function getEntityName(): string
	{
		return Entities\Emails\Email::class;
	}

	/**
	 * @return Entities\Accounts\IAccount
	 *
	 * @throws NodeWebServerExceptions\JsonApiErrorException
	 */
	protected function hydrateAccountAttribute(): Entities\Accounts\IAccount
	{
		$account = $this->user->getAccount();

		if ($account === null) {
			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('//node.base.messages.accountNotFound.heading'),
				$this->translator->translate('//node.base.messages.accountNotFound.message')
			);
		}

		return $account;
	}

}
