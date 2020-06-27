<?php declare(strict_types = 1);

namespace Tests\Cases;

use Contributte\Translation;
use Doctrine\Common;
use FastyBird\AuthNode\Commands;
use FastyBird\AuthNode\Models;
use FastyBird\AuthNode\Queries;
use IPub\SlimRouter;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';
require_once __DIR__ . '/../DbTestCase.php';

final class SynchroniseVernemqTest extends DbTestCase
{

	public function setUp(): void
	{
		$this->registerDatabaseSchemaFile(__DIR__ . '/../../../sql/vernemq.command.sql');

		parent::setUp();
	}

	public function XtestExecute(): void
	{
		/** @var Models\Accounts\IAccountRepository $accountRepository */
		$accountRepository = $this->getContainer()->getByType(Models\Accounts\AccountRepository::class);

		/** @var Models\Vernemq\AccountRepository $verneAccountRepository */
		$verneAccountRepository = $this->getContainer()->getByType(Models\Vernemq\AccountRepository::class);

		/** @var Models\Vernemq\AccountsManager $verneAccountsManager */
		$verneAccountsManager = $this->getContainer()->getByType(Models\Vernemq\AccountsManager::class);

		/** @var Translation\Translator $translator */
		$translator = $this->getContainer()->getByType(Translation\Translator::class);

		/** @var Common\Persistence\ManagerRegistry $managerRegistry */
		$managerRegistry = $this->getContainer()->getByType(Common\Persistence\ManagerRegistry::class);

		/** @var LoggerInterface $logger */
		$logger = $this->getContainer()->getByType(LoggerInterface::class);

		$findQuery = new Queries\FindVerneMqAccountsQuery();

		$accounts = $verneAccountRepository->findAllBy($findQuery);

		Assert::same(1, count($accounts));

		$application = new Application();
		$application->add(new Commands\Synchronisation\VernemqCommand(
			$accountRepository,
			$verneAccountRepository,
			$verneAccountsManager,
			$translator,
			$managerRegistry,
			$logger
		));

		$command = $application->get('fb:auth-node:sync:vernemq');

		$commandTester = new CommandTester($command);
		$commandTester->execute([]);

		$findQuery = new Queries\FindVerneMqAccountsQuery();

		$accounts = $verneAccountRepository->findAllBy($findQuery);

		Assert::same(2, count($accounts));
	}

}

$test_case = new SynchroniseVernemqTest();
$test_case->run();
