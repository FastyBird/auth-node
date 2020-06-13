<?php declare(strict_types = 1);

/**
 * SecurityHash.php
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

namespace FastyBird\AuthNode\Helpers;

use DateTimeImmutable;
use FastyBird\NodeLibs\Helpers as NodeLibsHelpers;
use Nette;
use Nette\Utils;

/**
 * Verification hash helper
 *
 * @package        FastyBird:AuthNode!
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

	/** @var NodeLibsHelpers\IDateFactory */
	private $dateTimeFactory;

	public function __construct(
		NodeLibsHelpers\IDateFactory $dateTimeFactory,
		string $interval = '+ 1 hour'
	) {
		$this->interval = $interval;

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
		$encoded = base64_decode($key, true);

		if ($encoded === false) {
			return false;
		}

		$pieces = explode(self::SEPARATOR, $encoded);

		if (count($pieces) === 2) {
			[, $timestamp] = $pieces;

			$datetime = Utils\DateTime::from($timestamp);

			if ($datetime >= $this->dateTimeFactory->getNow()) {
				return true;
			}
		}

		return false;
	}

}
