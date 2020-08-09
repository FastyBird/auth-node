<?php declare(strict_types = 1);

namespace Tests\Cases;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use FastyBird\AuthNode\Consumers;
use FastyBird\AuthNode\Entities;
use FastyBird\AuthNode\Models;
use FastyBird\AuthNode\Queries;
use Nette\Utils;
use Ramsey\Uuid;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';
require_once __DIR__ . '/../DbTestCase.php';

/**
 * @testCase
 */
final class DeviceMessageHandlerTest extends DbTestCase
{

	public function setUp(): void
	{
		$this->registerDatabaseSchemaFile(__DIR__ . '/../../../sql/deviceExchangecConsumer.sql');

		parent::setUp();
	}

	/**
	 * @param string $routingKey
	 * @param Utils\ArrayHash $message
	 * @param string|null $verneUsername
	 *
	 * @dataProvider ./../../../fixtures/Consumers/deviceCreatedMessage.php
	 */
	public function testProcessMessageDeviceCreated(string $routingKey, Utils\ArrayHash $message, ?string $id): void
	{
		/** @var Consumers\DeviceMessageHandler $handler */
		$handler = $this->getContainer()->getByType(Consumers\DeviceMessageHandler::class);

		$handler->process($routingKey, $message);

		/** @var Models\Accounts\IAccountRepository $accountRepository */
		$accountRepository = $this->getContainer()->getByType(Models\Accounts\IAccountRepository::class);

		$findAccount = new Queries\FindAccountsQuery();
		$findAccount->byId(Uuid\Uuid::fromString($id));

		$account = $accountRepository->findOneBy($findAccount);

		Assert::type(Entities\Accounts\MachineAccount::class, $account);
	}

	/**
	 * @param string $routingKey
	 * @param Utils\ArrayHash $message
	 *
	 * @dataProvider ./../../../fixtures/Consumers/deviceDeletedMessage.php
	 */
	public function testProcessMessageDeviceDeleted(string $routingKey, Utils\ArrayHash $message): void
	{
		/** @var Models\Vernemq\IAccountRepository $accountRepository */
		$accountRepository = $this->getContainer()->getByType(Models\Vernemq\IAccountRepository::class);

		$findAccounts = new Queries\FindVerneMqAccountsQuery();

		$accounts = $accountRepository->findAllBy($findAccounts);

		/** @var Consumers\DeviceMessageHandler $handler */
		$handler = $this->getContainer()->getByType(Consumers\DeviceMessageHandler::class);

		$handler->process($routingKey, $message);

		/** @var EntityManager $em */
		$em = $this->getContainer()->getByType(EntityManagerInterface::class);

		$em->flush();
		$em->clear();

		$findAccounts = new Queries\FindVerneMqAccountsQuery();

		$updatedAccounts = $accountRepository->findAllBy($findAccounts);

		Assert::same(count($accounts) - 1, count($updatedAccounts));
	}

}

$test_case = new DeviceMessageHandlerTest();
$test_case->run();
