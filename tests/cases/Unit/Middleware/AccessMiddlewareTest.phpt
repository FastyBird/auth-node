<?php declare(strict_types = 1);

namespace Tests\Cases;

use FastyBird\AuthNode\Router;
use FastyBird\NodeWebServer\Http;
use Fig\Http\Message\RequestMethodInterface;
use React\Http\Io\ServerRequest;
use Tester\Assert;
use Tests\Tools;

require_once __DIR__ . '/../../../bootstrap.php';
require_once __DIR__ . '/../DbTestCase.php';

final class AccessMiddlewareTest extends DbTestCase
{

	public function setUp(): void
	{
		$this->registerDatabaseSchemaFile(__DIR__ . '/../../../sql/access.check.sql');

		parent::setUp();
	}

	/**
	 * @param string $url
	 * @param string $token
	 * @param int $statusCode
	 * @param string $fixture
	 *
	 * @dataProvider ./../../../fixtures/Middleware/permissionAnnotation.php
	 */
	public function testPermissionAnnotation(string $url, string $token, int $statusCode, string $fixture): void
	{
		/** @var Router\Router $router */
		$router = $this->getContainer()->getByType(Router\Router::class);

		$request = new ServerRequest(
			RequestMethodInterface::METHOD_GET,
			$url,
			[
				'authorization' => $token,
			]
		);

		$response = $router->handle($request);

		Tools\JsonAssert::assertFixtureMatch(
			$fixture,
			(string) $response->getBody()
		);
		Assert::same($statusCode, $response->getStatusCode());
		Assert::type(Http\Response::class, $response);
	}

}

$test_case = new AccessMiddlewareTest();
$test_case->run();