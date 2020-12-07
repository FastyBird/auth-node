<?php declare(strict_types = 1);

namespace Tests\Cases;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use FastyBird\AuthModule\Entities as AuthModuleEntities;
use FastyBird\AuthModule\Models as AuthModuleModels;
use FastyBird\AuthModule\Queries as AuthModuleQueries;
use FastyBird\AuthNode;
use FastyBird\AuthNode\Consumers;
use FastyBird\AuthNode\Models;
use FastyBird\AuthNode\Queries;
use FastyBird\RabbitMqPlugin\Publishers as RabbitMqPluginPublishers;
use Mockery;
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

		$rabbitPublisher = Mockery::mock(RabbitMqPluginPublishers\RabbitMqPublisher::class);
		$rabbitPublisher
			->shouldReceive('publish');

		$this->mockContainerService(
			RabbitMqPluginPublishers\IRabbitMqPublisher::class,
			$rabbitPublisher
		);
	}

	/**
	 * @param string $routingKey
	 * @param string $payload
	 * @param string|null $id
	 *
	 * @dataProvider ./../../../fixtures/Consumers/deviceCreatedMessage.php
	 */
	public function testProcessMessageDeviceCreated(string $routingKey, string $payload, ?string $id): void
	{
		/** @var Consumers\DeviceMessageHandler $handler */
		$handler = $this->getContainer()->getByType(Consumers\DeviceMessageHandler::class);

		$handler->process($routingKey, AuthNode\Constants::RABBIT_MQ_DEVICES_ORIGIN, $payload);

		/** @var AuthModuleModels\Accounts\IAccountRepository $accountRepository */
		$accountRepository = $this->getContainer()->getByType(AuthModuleModels\Accounts\IAccountRepository::class);

		$findAccount = new AuthModuleQueries\FindAccountsQuery();
		$findAccount->byId(Uuid\Uuid::fromString($id));

		$account = $accountRepository->findOneBy($findAccount);

		Assert::type(AuthModuleEntities\Accounts\MachineAccount::class, $account);
		Assert::count(count(AuthNode\Constants::MACHINE_ACCOUNT_DEFAULT_ROLES), $account->getRoles());

		foreach ($account->getRoles() as $role) {
			Assert::true(in_array($role->getName(), AuthNode\Constants::MACHINE_ACCOUNT_DEFAULT_ROLES, true));
		}
	}

	/**
	 * @param string $routingKey
	 * @param string $payload
	 *
	 * @dataProvider ./../../../fixtures/Consumers/deviceDeletedMessage.php
	 */
	public function testProcessMessageDeviceDeleted(string $routingKey, string $payload): void
	{
		/** @var Models\Vernemq\IAccountRepository $accountRepository */
		$accountRepository = $this->getContainer()->getByType(Models\Vernemq\IAccountRepository::class);

		$findAccounts = new Queries\FindVerneMqAccountsQuery();

		$accounts = $accountRepository->findAllBy($findAccounts);

		/** @var Consumers\DeviceMessageHandler $handler */
		$handler = $this->getContainer()->getByType(Consumers\DeviceMessageHandler::class);

		$handler->process($routingKey, AuthNode\Constants::RABBIT_MQ_DEVICES_ORIGIN, $payload);

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
