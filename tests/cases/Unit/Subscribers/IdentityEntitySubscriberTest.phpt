<?php declare(strict_types = 1);

namespace Tests\Cases;

use FastyBird\AuthNode\Entities;
use FastyBird\AuthNode\Models;
use FastyBird\AuthNode\Queries;
use FastyBird\NodeExchange\Publishers as NodeExchangePublishers;
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

		$rabbitPublisher = Mockery::mock(NodeExchangePublishers\RabbitMqPublisher::class);
		$rabbitPublisher
			->shouldReceive('publish');

		$this->mockContainerService(
			NodeExchangePublishers\IRabbitMqPublisher::class,
			$rabbitPublisher
		);
	}

	public function testCreateEntity(): void
	{
		/** @var Models\Accounts\IAccountRepository $accountRepository */
		$accountRepository = $this->getContainer()->getByType(Models\Accounts\AccountRepository::class);

		$findAccount = new Queries\FindAccountsQuery();
		$findAccount->byId(Uuid\Uuid::fromString(self::ACCOUNT_TEST_ID));

		$account = $accountRepository->findOneBy($findAccount, Entities\Accounts\MachineAccount::class);

		$createIdentity = Utils\ArrayHash::from([
			'entity'   => Entities\Identities\MachineAccountIdentity::class,
			'account'  => $account,
			'password' => 'randomPassword',
			'uid'      => 'newUsername',
		]);

		/** @var Models\Identities\IIdentitiesManager $identitiesManager */
		$identitiesManager = $this->getContainer()->getByType(Models\Identities\IdentitiesManager::class);

		/** @var Entities\Identities\MachineAccountIdentity $identity */
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
		/** @var Models\Identities\IIdentityRepository $identityRepository */
		$identityRepository = $this->getContainer()->getByType(Models\Identities\IdentityRepository::class);

		$findIdentity = new Queries\FindIdentitiesQuery();
		$findIdentity->byUid('deviceUsername');

		$identity = $identityRepository->findOneBy($findIdentity, Entities\Identities\MachineAccountIdentity::class);

		$updateIdentity = Utils\ArrayHash::from([
			'password' => 'randomPassword',
			'uid'      => 'newUsername',
		]);

		/** @var Models\Identities\IIdentitiesManager $identitiesManager */
		$identitiesManager = $this->getContainer()->getByType(Models\Identities\IdentitiesManager::class);

		/** @var Entities\Identities\MachineAccountIdentity $identity */
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
		/** @var Models\Identities\IIdentityRepository $identityRepository */
		$identityRepository = $this->getContainer()->getByType(Models\Identities\IdentityRepository::class);

		$findIdentity = new Queries\FindIdentitiesQuery();
		$findIdentity->byUid('deviceUsername');

		$identity = $identityRepository->findOneBy($findIdentity, Entities\Identities\MachineAccountIdentity::class);

		$updateIdentity = Utils\ArrayHash::from([
			'password' => 'randomPassword',
		]);

		/** @var Models\Identities\IIdentitiesManager $identitiesManager */
		$identitiesManager = $this->getContainer()->getByType(Models\Identities\IdentitiesManager::class);

		/** @var Entities\Identities\MachineAccountIdentity $identity */
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
		/** @var Models\Identities\IIdentityRepository $identityRepository */
		$identityRepository = $this->getContainer()->getByType(Models\Identities\IdentityRepository::class);

		$findIdentity = new Queries\FindIdentitiesQuery();
		$findIdentity->byUid('deviceUsername');

		$identity = $identityRepository->findOneBy($findIdentity, Entities\Identities\MachineAccountIdentity::class);

		/** @var Models\Identities\IIdentitiesManager $identitiesManager */
		$identitiesManager = $this->getContainer()->getByType(Models\Identities\IdentitiesManager::class);

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
