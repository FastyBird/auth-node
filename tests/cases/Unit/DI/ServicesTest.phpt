<?php declare(strict_types = 1);

namespace Tests\Cases;

use FastyBird\AccountsNode\Commands;
use FastyBird\AccountsNode\Controllers;
use FastyBird\AccountsNode\Hydrators;
use FastyBird\AccountsNode\Middleware;
use FastyBird\AccountsNode\Models;
use FastyBird\AccountsNode\Schemas;
use FastyBird\AccountsNode\Subscribers;
use FastyBird\NodeLibs\Boot;
use Ninjify\Nunjuck\TestCase\BaseTestCase;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';

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

		Assert::notNull($container->getByType(Middleware\AccountMiddleware::class));

		Assert::notNull($container->getByType(Commands\Accounts\CreateCommand::class));
		Assert::notNull($container->getByType(Commands\Roles\CreateCommand::class));

		Assert::notNull($container->getByType(Subscribers\EmailEntitySubscriber::class));

		Assert::notNull($container->getByType(Models\Accounts\AccountRepository::class));
		Assert::notNull($container->getByType(Models\Emails\EmailRepository::class));
		Assert::notNull($container->getByType(Models\Identities\IdentityRepository::class));
		Assert::notNull($container->getByType(Models\Privileges\PrivilegeRepository::class));
		Assert::notNull($container->getByType(Models\Resources\ResourceRepository::class));
		Assert::notNull($container->getByType(Models\Roles\RoleRepository::class));
		Assert::notNull($container->getByType(Models\Tokens\TokenRepository::class));

		Assert::notNull($container->getByType(Models\Accounts\AccountsManager::class));
		Assert::notNull($container->getByType(Models\Emails\EmailsManager::class));
		Assert::notNull($container->getByType(Models\Identities\IdentitiesManager::class));
		Assert::notNull($container->getByType(Models\Privileges\PrivilegesManager::class));
		Assert::notNull($container->getByType(Models\Resources\ResourcesManager::class));
		Assert::notNull($container->getByType(Models\Roles\RolesManager::class));
		Assert::notNull($container->getByType(Models\SecurityQuestions\QuestionsManager::class));
		Assert::notNull($container->getByType(Models\Tokens\TokensManager::class));

		Assert::notNull($container->getByType(Controllers\AccountV1Controller::class));
		Assert::notNull($container->getByType(Controllers\EmailsV1Controller::class));
		Assert::notNull($container->getByType(Controllers\SecurityQuestionV1Controller::class));
		Assert::notNull($container->getByType(Controllers\SessionV1Controller::class));
		Assert::notNull($container->getByType(Controllers\SystemIdentityV1Controller::class));
		Assert::notNull($container->getByType(Controllers\RolesV1Controller::class));
		Assert::notNull($container->getByType(Controllers\RoleChildrenV1Controller::class));

		Assert::notNull($container->getByType(Schemas\AccountSchema::class));
		Assert::notNull($container->getByType(Schemas\EmailSchema::class));
		Assert::notNull($container->getByType(Schemas\SecurityQuestionSchema::class));
		Assert::notNull($container->getByType(Schemas\SessionSchema::class));
		Assert::notNull($container->getByType(Schemas\SystemIdentitySchema::class));
		Assert::notNull($container->getByType(Schemas\Roles\RoleSchema::class));

		Assert::notNull($container->getByType(Hydrators\AccountHydrator::class));
		Assert::notNull($container->getByType(Hydrators\EmailHydrator::class));
		Assert::notNull($container->getByType(Hydrators\SecurityQuestionHydrator::class));
		Assert::notNull($container->getByType(Hydrators\Roles\RoleHydrator::class));
	}

}

$test_case = new ServicesTest();
$test_case->run();
