<?php declare(strict_types = 1);

namespace Tests\Cases;

use FastyBird\AuthNode\Commands;
use FastyBird\AuthNode\Controllers;
use FastyBird\AuthNode\Hydrators;
use FastyBird\AuthNode\Middleware;
use FastyBird\AuthNode\Models;
use FastyBird\AuthNode\Schemas;
use FastyBird\AuthNode\Subscribers;
use FastyBird\NodeLibs\Boot;
use Ninjify\Nunjuck\TestCase\BaseTestCase;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';

final class ServicesTest extends BaseTestCase
{

	public function XtestServicesRegistration(): void
	{
		$configurator = Boot\Bootstrap::boot();
		$configurator->addParameters([
			'database' => [
				'driver' => 'pdo_sqlite',
			],
		]);

		$container = $configurator->createContainer();

		Assert::notNull($container->getByType(Middleware\AccountMiddleware::class));
		Assert::notNull($container->getByType(Middleware\AccessMiddleware::class));

		Assert::notNull($container->getByType(Commands\Accounts\CreateCommand::class));
		Assert::notNull($container->getByType(Commands\Roles\CreateCommand::class));
		Assert::notNull($container->getByType(Commands\Synchronisation\PermissionsCommand::class));
		Assert::notNull($container->getByType(Commands\Synchronisation\VernemqCommand::class));

		Assert::notNull($container->getByType(Subscribers\EmailEntitySubscriber::class));
		Assert::notNull($container->getByType(Subscribers\IdentityEntitySubscriber::class));

		Assert::notNull($container->getByType(Models\Accounts\AccountRepository::class));
		Assert::notNull($container->getByType(Models\Emails\EmailRepository::class));
		Assert::notNull($container->getByType(Models\Identities\IdentityRepository::class));
		Assert::notNull($container->getByType(Models\Privileges\PrivilegeRepository::class));
		Assert::notNull($container->getByType(Models\Resources\ResourceRepository::class));
		Assert::notNull($container->getByType(Models\Roles\RoleRepository::class));
		Assert::notNull($container->getByType(Models\Tokens\TokenRepository::class));
		Assert::notNull($container->getByType(Models\Vernemq\AccountRepository::class));

		Assert::notNull($container->getByType(Models\Accounts\AccountsManager::class));
		Assert::notNull($container->getByType(Models\Emails\EmailsManager::class));
		Assert::notNull($container->getByType(Models\Identities\IdentitiesManager::class));
		Assert::notNull($container->getByType(Models\Privileges\PrivilegesManager::class));
		Assert::notNull($container->getByType(Models\Resources\ResourcesManager::class));
		Assert::notNull($container->getByType(Models\Roles\RolesManager::class));
		Assert::notNull($container->getByType(Models\Tokens\TokensManager::class));

		Assert::notNull($container->getByType(Controllers\AccountV1Controller::class));
		Assert::notNull($container->getByType(Controllers\AccountEmailsV1Controller::class));
		Assert::notNull($container->getByType(Controllers\SessionV1Controller::class));
		Assert::notNull($container->getByType(Controllers\AccountIdentitiesV1Controller::class));
		Assert::notNull($container->getByType(Controllers\AccountsV1Controller::class));
		Assert::notNull($container->getByType(Controllers\EmailsV1Controller::class));
		Assert::notNull($container->getByType(Controllers\IdentitiesV1Controller::class));
		Assert::notNull($container->getByType(Controllers\RolesV1Controller::class));
		Assert::notNull($container->getByType(Controllers\RoleChildrenV1Controller::class));
		Assert::notNull($container->getByType(Controllers\RoleRulesV1Controller::class));
		Assert::notNull($container->getByType(Controllers\ResourcesV1Controller::class));
		Assert::notNull($container->getByType(Controllers\ResourceChildrenV1Controller::class));
		Assert::notNull($container->getByType(Controllers\ResourcePrivilegesV1Controller::class));
		Assert::notNull($container->getByType(Controllers\PrivilegesV1Controller::class));
		Assert::notNull($container->getByType(Controllers\RulesV1Controller::class));

		Assert::notNull($container->getByType(Schemas\Accounts\UserAccountSchema::class));
		Assert::notNull($container->getByType(Schemas\Emails\EmailSchema::class));
		Assert::notNull($container->getByType(Schemas\Sessions\SessionSchema::class));
		Assert::notNull($container->getByType(Schemas\Identities\UserAccountIdentitySchema::class));
		Assert::notNull($container->getByType(Schemas\Roles\RoleSchema::class));

		Assert::notNull($container->getByType(Hydrators\Accounts\UserAccountHydrator::class));
		Assert::notNull($container->getByType(Hydrators\Emails\EmailHydrator::class));
		Assert::notNull($container->getByType(Hydrators\Roles\RoleHydrator::class));
		Assert::notNull($container->getByType(Hydrators\Resources\ResourceHydrator::class));
		Assert::notNull($container->getByType(Hydrators\Privileges\PrivilegeHydrator::class));
	}

}

$test_case = new ServicesTest();
$test_case->run();
