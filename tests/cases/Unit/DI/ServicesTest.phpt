<?php declare(strict_types = 1);

namespace Tests\Cases;

use FastyBird\AuthNode\Commands;
use FastyBird\AuthNode\Consumers;
use FastyBird\AuthNode\Events;
use FastyBird\AuthNode\Models;
use FastyBird\AuthNode\Subscribers;
use FastyBird\Bootstrap\Boot;
use Ninjify\Nunjuck\TestCase\BaseTestCase;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';

/**
 * @testCase
 */
final class ServicesTest extends BaseTestCase
{

	public function testServicesRegistration(): void
	{
		$configurator = Boot\Bootstrap::boot();
		$configurator->addParameters([
			'database' => [
				'driver' => 'pdo_sqlite',
			],
		]);

		$container = $configurator->createContainer();

		Assert::notNull($container->getByType(Commands\InitializeCommand::class));

		Assert::notNull($container->getByType(Consumers\DeviceMessageHandler::class));

		Assert::notNull($container->getByType(Events\ServerBeforeStartHandler::class));

		Assert::notNull($container->getByType(Subscribers\IdentityEntitySubscriber::class));

		Assert::notNull($container->getByType(Models\Vernemq\AccountRepository::class));

		Assert::notNull($container->getByType(Models\Vernemq\AccountsManager::class));
	}

}

$test_case = new ServicesTest();
$test_case->run();
