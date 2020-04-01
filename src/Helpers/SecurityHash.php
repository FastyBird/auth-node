<?php declare(strict_types = 1);

/**
 * SecurityHash.php
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

namespace FastyBird\AccountsNode\Helpers;

use DateTimeImmutable;
use FastyBird\AccountsNode\Models;
use FastyBird\NodeLibs\Helpers as NodeLibsHelpers;
use Nette;
use Nette\Utils;

/**
 * Verification hash helper
 *
 * @package        FastyBird:AccountsNode!
 * @subpackage     Helpers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class SecurityHash
{

	use Nette\SmartObject;

	private const SEPARATOR = '##';

	/** @var  string */
	private $interval;

	/** @var Models\Accounts\IAccountRepository */
	private $accountRepository;

	/** @var NodeLibsHelpers\DateFactory */
	private $dateTimeFactory;

	public function __construct(
		Models\Accounts\IAccountRepository $accountRepository,
		NodeLibsHelpers\DateFactory $dateTimeFactory,
		string $interval = '+ 1 hour'
	) {
		$this->interval = $interval;

		$this->accountRepository = $accountRepository;

		$this->dateTimeFactory = $dateTimeFactory;
	}

	/**
	 * @param string $interval
	 *
	 * @return void
	 */
	public function setInterval(string $interval): void
	{
		$this->interval = $interval;
	}

	/**
	 * @param string|null $interval
	 *
	 * @return string
	 */
	public function createKey(?string $interval = null): string
	{
		/** @var DateTimeImmutable $now */
		$now = $this->dateTimeFactory->getNow();

		/** @var DateTimeImmutable $datetime */
		$datetime = $now->modify($interval ?? $this->interval);

		return base64_encode(Utils\Random::generate(12) . self::SEPARATOR . $datetime->getTimestamp());
	}

	/**
	 * @param string $key
	 *
	 * @return bool
	 */
	public function isValid(string $key): bool
	{
		$pieces = explode(self::SEPARATOR, base64_decode($key));

		if (count($pieces) === 2) {
			[, $timestamp] = $pieces;

			$datetime = Utils\DateTime::from($timestamp);

			if ($datetime >= $this->dateTimeFactory->getNow()) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @return string
	 */
	public function getRecoveryKey(): string
	{
		$key = $this->createKey();

		$account = $this->accountRepository->findOneByHash($key);

		if ($account !== null) {
			$key = $this->getRecoveryKey();
		}

		return $key;
	}

}
