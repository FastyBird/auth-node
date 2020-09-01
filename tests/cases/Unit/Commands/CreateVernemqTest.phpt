<?php declare(strict_types = 1);

namespace Tests\Cases;

use Contributte\Translation;
use Doctrine\Common;
use FastyBird\AuthNode\Commands;
use FastyBird\AuthNode\Entities;
use FastyBird\AuthNode\Models;
use FastyBird\AuthNode\Queries;
use FastyBird\NodeAuth;
use Nette\Utils;
use Psr\Log\LoggerInterface;
use stdClass;
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
			'clientid' => 'vmqclientId',
			'role'     => NodeAuth\Constants::ROLE_MANAGER,
		]);

		$findQuery = new Queries\FindVerneMqAccountsQuery();
		$findQuery->byUsername('vmqusername');

		$account = $accountRepository->findOneBy($findQuery);

		$subsItem1 = new stdClass();
		$subsItem1->pattern = '/fb/#';

		$subsItem2 = new stdClass();
		$subsItem2->pattern = '$SYS/broker/log/#';

		$pubItem1 = new stdClass();
		$pubItem1->pattern = '/fb/#';

		Assert::type(Entities\Vernemq\Account::class, $account);
		Assert::equal(Utils\Json::decode(Utils\Json::encode([$subsItem1, $subsItem2])), $account->getSubscribeAcl());
		Assert::equal(Utils\Json::decode(Utils\Json::encode([$pubItem1])), $account->getPublishAcl());
		Assert::same('vmqclientId', $account->getClientId());
	}

}

$test_case = new CreateVernemqTest();
$test_case->run();
