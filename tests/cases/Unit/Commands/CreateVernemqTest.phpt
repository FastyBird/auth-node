<?php declare(strict_types = 1);

namespace Tests\Cases;

use Contributte\Translation;
use Doctrine\Common;
use FastyBird\AuthNode\Commands;
use FastyBird\AuthNode\Entities;
use FastyBird\AuthNode\Models;
use FastyBird\AuthNode\Queries;
use FastyBird\NodeAuth;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';
require_once __DIR__ . '/../DbTestCase.php';

/**
 * @testCase
 */
final class CreateVernemqTest extends DbTestCase
{

	public function testExecute(): void
	{
		/** @var Models\Vernemq\AccountsManager $verneAccountsManager */
		$accountsManager = $this->getContainer()->getByType(Models\Vernemq\AccountsManager::class);

		/** @var Models\Roles\IRoleRepository $roleRepository */
		$roleRepository = $this->getContainer()->getByType(Models\Roles\RoleRepository::class);

		/** @var Models\Vernemq\AccountRepository $accountRepository */
		$accountRepository = $this->getContainer()->getByType(Models\Vernemq\AccountRepository::class);

		/** @var Translation\Translator $translator */
		$translator = $this->getContainer()->getByType(Translation\Translator::class);

		/** @var Common\Persistence\ManagerRegistry $managerRegistry */
		$managerRegistry = $this->getContainer()->getByType(Common\Persistence\ManagerRegistry::class);

		/** @var LoggerInterface $logger */
		$logger = $this->getContainer()->getByType(LoggerInterface::class);

		$application = new Application();
		$application->add(new Commands\Vernemq\CreateCommand(
			$accountsManager,
			$roleRepository,
			$translator,
			$managerRegistry,
			$logger
		));

		$command = $application->get('fb:auth-node:create:vernemq');

		$commandTester = new CommandTester($command);
		$commandTester->execute([
			'username' => 'vmqusername',
			'password' => 'vmqpassword',
			'role'     => NodeAuth\Constants::ROLE_ADMINISTRATOR,
		]);

		$findQuery = new Queries\FindVerneMqAccountsQuery();
		$findQuery->byUsername('vmqusername');

		$account = $accountRepository->findOneBy($findQuery);

		Assert::type(Entities\Vernemq\Account::class, $account);
		Assert::same(['/fb/#', '$SYS/broker/log/#'], $account->getSubscribeAcl());
		Assert::same(['/fb/#'], $account->getPublishAcl());
	}

}

$test_case = new CreateVernemqTest();
$test_case->run();
