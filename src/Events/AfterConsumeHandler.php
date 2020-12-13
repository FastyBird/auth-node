<?php declare(strict_types = 1);

/**
 * AfterConsumeHandler.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Events
 * @since          0.1.0
 *
 * @date           15.04.20
 */

namespace FastyBird\AuthNode\Events;

use Doctrine\Persistence;
use Nette;

/**
 * After message consumed handler
 *
 * @package         FastyBird:AuthNode!
 * @subpackage      Events
 *
 * @author          Adam Kadlec <adam.kadlec@fastybird.com>
 */
class AfterConsumeHandler
{

	use Nette\SmartObject;

	/** @var Persistence\ManagerRegistry */
	private $managerRegistry;

	public function __construct(
		Persistence\ManagerRegistry $managerRegistry
	) {
		$this->managerRegistry = $managerRegistry;
	}

	/**
	 * @return void
	 */
	public function __invoke(): void
	{
		$em = $this->managerRegistry->getManager();

		// Flushing and then clearing Doctrine's entity manager allows
		// for more memory to be released by PHP
		$em->flush();
		$em->clear();

		// Just in case PHP would choose not to run garbage collection,
		// we run it manually at the end of each batch so that memory is
		// regularly released
		gc_collect_cycles();
	}

}