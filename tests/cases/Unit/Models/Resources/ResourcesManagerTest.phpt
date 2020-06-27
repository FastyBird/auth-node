<?php declare(strict_types = 1);

namespace Tests\Cases;

use FastyBird\AuthNode\Entities;
use FastyBird\AuthNode\Models;
use FastyBird\AuthNode\Queries;
use Nette\Utils;

require_once __DIR__ . '/../../../../bootstrap.php';
require_once __DIR__ . '/../../DbTestCase.php';

final class ResourcesManagerTest extends DbTestCase
{

	/**
	 * @throws FastyBird\AuthNode\Exceptions\InvalidStateException  Privilege could be assigned only to top level resources
	 */
	public function testCreateInvalid(): void
	{
		/** @var Models\Resources\IResourcesManager $manager */
		$manager = $this->getContainer()->getByType(Models\Resources\IResourcesManager::class);

		/** @var Models\Resources\IResourceRepository $resourceRepository */
		$resourceRepository = $this->getContainer()->getByType(Models\Resources\IResourceRepository::class);

		$findResource = new Queries\FindResourcesQuery();
		$findResource->byName('node entity');

		$resource = $resourceRepository->findOneBy($findResource);

		$manager->create(Utils\ArrayHash::from([
			'name'        => 'test privilege',
			'description' => 'test privilege description',
			'origin'      => 'com.test.origin',
			'parent'      => $resource,
			'privileges'  => Utils\ArrayHash::from([
				'entity'      => Entities\Privileges\Privilege::class,
				'name'        => 'test privilege',
				'description' => 'test privilege description',
			]),
		]));
	}

}

$test_case = new ResourcesManagerTest();
$test_case->run();
