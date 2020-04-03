<?php declare(strict_types = 1);

namespace Tests\Cases;

use FastyBird\AccountsNode\Entities;
use FastyBird\AccountsNode\Models;
use FastyBird\AccountsNode\Queries;
use Nette\Utils;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';
require_once __DIR__ . '/../DbTestCase.php';

final class EmailEntitySubscriberTest extends DbTestCase
{

	public function testChangeDefault(): void
	{
		/** @var Models\Emails\IEmailRepository $repository */
		$repository = $this->getContainer()->getByType(Models\Emails\EmailRepository::class);

		/** @var Models\Emails\IEmailsManager $manager */
		$manager = $this->getContainer()->getByType(Models\Emails\EmailsManager::class);

		$defaultEmail = $repository->findOneByAddress('john.doe@fastybird.com');

		Assert::true($defaultEmail->isDefault());

		$email = $repository->findOneByAddress('john.doe@fastybird.ovh');

		$manager->update($email, Utils\ArrayHash::from([
			'default' => true,
		]));

		$defaultEmail = $repository->findOneByAddress('john.doe@fastybird.com');

		Assert::false($defaultEmail->isDefault());

		$findEntityQuery = new Queries\FindIdentitiesQuery();
		$findEntityQuery->forAccount($defaultEmail->getAccount());

		/** @var Models\Identities\IIdentityRepository $repository */
		$repository = $this->getContainer()->getByType(Models\Identities\IdentityRepository::class);

		$identity = $repository->findOneBy($findEntityQuery, Entities\Identities\System::class);

		Assert::same('john.doe@fastybird.ovh', $identity->getEmail());
		Assert::same('john.doe@fastybird.com', $identity->getUid());
	}

}

$test_case = new EmailEntitySubscriberTest();
$test_case->run();
