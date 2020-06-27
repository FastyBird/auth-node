<?php declare(strict_types = 1);

namespace Tests\Cases;

use FastyBird\AuthNode\Models;
use FastyBird\AuthNode\Queries;
use FastyBird\AuthNode\Router;
use FastyBird\NodeWebServer\Http;
use Nette\Utils;
use React\Http\Io\ServerRequest;
use Tester\Assert;
use Tests\Tools;

require_once __DIR__ . '/../../../../bootstrap.php';
require_once __DIR__ . '/../../DbTestCase.php';

final class PrivilegesManagerTest extends DbTestCase
{

	/**
	 * @throws FastyBird\AuthNode\Exceptions\InvalidStateException  Privilege could be assigned only to top level resources
	 */
	public function testCreateInvalid(): void
	{
		/** @var Models\Privileges\IPrivilegesManager $manager */
		$manager = $this->getContainer()->getByType(Models\Privileges\IPrivilegesManager::class);

		/** @var Models\Resources\IResourceRepository $resourceRepository */
		$resourceRepository = $this->getContainer()->getByType(Models\Resources\IResourceRepository::class);

		$findResource = new Queries\FindResourcesQuery();
		$findResource->byName('node entity');

		$resource = $resourceRepository->findOneBy($findResource);

		$manager->create(Utils\ArrayHash::from([
			'name'        => 'test privilege',
			'description' => 'test privilege description',
			'resource'    => $resource,
		]));
	}

}

$test_case = new PrivilegesManagerTest();
$test_case->run();
