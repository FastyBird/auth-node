<?php declare(strict_types = 1);

namespace Tests\Cases;

use FastyBird\AuthModule\Entities as AuthModuleEntities;
use FastyBird\AuthModule\Models as AuthModuleModels;
use FastyBird\AuthModule\Queries as AuthModuleQueries;
use FastyBird\AuthNode\Models;
use FastyBird\AuthNode\Queries;
use FastyBird\RabbitMqPlugin\Publishers as RabbitMqPluginPublishers;
use Mockery;
use Nette\Utils;
use Ramsey\Uuid;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';
require_once __DIR__ . '/../DbTestCase.php';

/**
 * @testCase
 */
final class IdentityEntitySubscriberTest extends DbTestCase
{

	private const ACCOUNT_TEST_ID = '16e5db29-0006-4484-ac38-5cdea5a008f5';

	public function setUp(): void
	{
		parent::setUp();

		$rabbitPublisher = Mockery::mock(RabbitMqPluginPublishers\RabbitMqPublisher::class);
		$rabbitPublisher
			->shouldReceive('publish');

		$this->mockContainerService(
			RabbitMqPluginPublishers\IRabbitMqPublisher::class,
			$rabbitPublisher
		);
	}

	public function testCreateEntity(): void
	{
		/** @var AuthModuleModels\Accounts\IAccountRepository $accountRepository */
		$accountRepository = $this->getContainer()->getByType(AuthModuleModels\Accounts\AccountRepository::class);

		$findAccount = new AuthModuleQueries\FindAccountsQuery();
		$findAccount->byId(Uuid\Uuid::fromString(self::ACCOUNT_TEST_ID));

		$account = $accountRepository->findOneBy($findAccount, AuthModuleEntities\Accounts\MachineAccount::class);

		$createIdentity = Utils\ArrayHash::from([
			'entity'   => AuthModuleEntities\Identities\MachineAccountIdentity::class,
			'account'  => $account,
			'password' => 'randomPassword',
			'uid'      => 'newUsername',
		]);

		/** @var AuthModuleModels\Identities\IIdentitiesManager $identitiesManager */
		$identitiesManager = $this->getContainer()->getByType(AuthModuleModels\Identities\IdentitiesManager::class);

		/** @var AuthModuleEntities\Identities\MachineAccountIdentity $identity */
		$identity = $identitiesManager->create($createIdentity);

		/** @var Models\Vernemq\IAccountRepository $verneMQRepository */
		$verneMQRepository = $this->getContainer()->getByType(Models\Vernemq\AccountRepository::class);

		$findAccount = new Queries\FindVerneMqAccountsQuery();
		$findAccount->forAccount($account);

		$verneMQAccount = $verneMQRepository->findOneBy($findAccount);

		Assert::notNull($verneMQAccount);
		Assert::same($identity->getId()->toString(), $verneMQAccount->getIdentity()->getId()->toString());
		Assert::same(hash('sha256', $identity->getPassword(), false), $verneMQAccount->getPassword());
		Assert::same('newUsername', $verneMQAccount->getUsername());
	}

	public function testUpdateEntityPasswordUsername(): void
	{
		/** @var AuthModuleModels\Identities\IIdentityRepository $identityRepository */
		$identityRepository = $this->getContainer()->getByType(AuthModuleModels\Identities\IdentityRepository::class);

		$findIdentity = new AuthModuleQueries\FindIdentitiesQuery();
		$findIdentity->byUid('deviceUsername');

		$identity = $identityRepository->findOneBy($findIdentity, AuthModuleEntities\Identities\MachineAccountIdentity::class);

		$updateIdentity = Utils\ArrayHash::from([
			'password' => 'randomPassword',
			'uid'      => 'newUsername',
		]);

		/** @var AuthModuleModels\Identities\IIdentitiesManager $identitiesManager */
		$identitiesManager = $this->getContainer()->getByType(AuthModuleModels\Identities\IdentitiesManager::class);

		/** @var AuthModuleEntities\Identities\MachineAccountIdentity $identity */
		$identity = $identitiesManager->update($identity, $updateIdentity);

		/** @var Models\Vernemq\IAccountRepository $verneMQRepository */
		$verneMQRepository = $this->getContainer()->getByType(Models\Vernemq\AccountRepository::class);

		$findAccount = new Queries\FindVerneMqAccountsQuery();
		$findAccount->forAccount($identity->getAccount());

		$verneMQAccount = $verneMQRepository->findOneBy($findAccount);

		Assert::notNull($verneMQAccount);
		Assert::same($identity->getId()->toString(), $verneMQAccount->getIdentity()->getId()->toString());
		Assert::same(hash('sha256', $identity->getPassword(), false), $verneMQAccount->getPassword());
		Assert::same('deviceUsername', $verneMQAccount->getUsername());
	}

	public function testUpdateEntityPassword(): void
	{
		/** @var AuthModuleModels\Identities\IIdentityRepository $identityRepository */
		$identityRepository = $this->getContainer()->getByType(AuthModuleModels\Identities\IdentityRepository::class);

		$findIdentity = new AuthModuleQueries\FindIdentitiesQuery();
		$findIdentity->byUid('deviceUsername');

		$identity = $identityRepository->findOneBy($findIdentity, AuthModuleEntities\Identities\MachineAccountIdentity::class);

		$updateIdentity = Utils\ArrayHash::from([
			'password' => 'randomPassword',
		]);

		/** @var AuthModuleModels\Identities\IIdentitiesManager $identitiesManager */
		$identitiesManager = $this->getContainer()->getByType(AuthModuleModels\Identities\IdentitiesManager::class);

		/** @var AuthModuleEntities\Identities\MachineAccountIdentity $identity */
		$identity = $identitiesManager->update($identity, $updateIdentity);

		/** @var Models\Vernemq\IAccountRepository $verneMQRepository */
		$verneMQRepository = $this->getContainer()->getByType(Models\Vernemq\AccountRepository::class);

		$findAccount = new Queries\FindVerneMqAccountsQuery();
		$findAccount->forAccount($identity->getAccount());

		$verneMQAccount = $verneMQRepository->findOneBy($findAccount);

		Assert::notNull($verneMQAccount);
		Assert::same($identity->getId()->toString(), $verneMQAccount->getIdentity()->getId()->toString());
		Assert::same(hash('sha256', $identity->getPassword(), false), $verneMQAccount->getPassword());
		Assert::same('deviceUsername', $verneMQAccount->getUsername());
	}

	public function testDeleteEntity(): void
	{
		/** @var AuthModuleModels\Identities\IIdentityRepository $identityRepository */
		$identityRepository = $this->getContainer()->getByType(AuthModuleModels\Identities\IdentityRepository::class);

		$findIdentity = new AuthModuleQueries\FindIdentitiesQuery();
		$findIdentity->byUid('deviceUsername');

		$identity = $identityRepository->findOneBy($findIdentity, AuthModuleEntities\Identities\MachineAccountIdentity::class);

		/** @var AuthModuleModels\Identities\IIdentitiesManager $identitiesManager */
		$identitiesManager = $this->getContainer()->getByType(AuthModuleModels\Identities\IdentitiesManager::class);

		$identitiesManager->delete($identity);

		/** @var Models\Vernemq\IAccountRepository $verneMQRepository */
		$verneMQRepository = $this->getContainer()->getByType(Models\Vernemq\AccountRepository::class);

		$findAccount = new Queries\FindVerneMqAccountsQuery();
		$findAccount->forAccount($identity->getAccount());

		$verneMQAccount = $verneMQRepository->findOneBy($findAccount);

		Assert::null($verneMQAccount);
	}

}

$test_case = new IdentityEntitySubscriberTest();
$test_case->run();
