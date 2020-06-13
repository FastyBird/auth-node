<?php declare(strict_types = 1);

namespace Tests\Cases;

use DateTimeImmutable;
use FastyBird\AuthNode\Controllers;
use FastyBird\AuthNode\Router;
use FastyBird\NodeLibs\Helpers as NodeLibsHelpers;
use FastyBird\NodeWebServer\Http;
use Fig\Http\Message\RequestMethodInterface;
use Mockery;
use React\Http\Io\ServerRequest;
use Tester\Assert;
use Tests\Tools;

require_once __DIR__ . '/../../../bootstrap.php';
require_once __DIR__ . '/../DbTestCase.php';

final class AccountV1ControllerTest extends DbTestCase
{

	/**
	 * @param string $url
	 * @param string $token
	 * @param int $statusCode
	 * @param string $fixture
	 *
	 * @dataProvider ./../../../fixtures/Controllers/accountRead.php
	 */
	public function testRead(string $url, string $token, int $statusCode, string $fixture): void
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

		$dateTimeFactory = Mockery::mock(NodeLibsHelpers\DateFactory::class);
		$dateTimeFactory
			->shouldReceive('getNow')
			->andReturn(new DateTimeImmutable('2020-04-01T12:00:00+00:00'));

		$this->mockContainerService(
			NodeLibsHelpers\IDateFactory::class,
			$dateTimeFactory
		);

		/** @var Controllers\SessionV1Controller $controller */
		$controller = $this->getContainer()->getByType(Controllers\SessionV1Controller::class);
		$controller->injectDateFactory($dateTimeFactory);

		$response = $router->handle($request);

		$body = (string) $response->getBody();

		Tools\JsonAssert::assertFixtureMatch($fixture, $body);
		Assert::same($statusCode, $response->getStatusCode());
		Assert::type(Http\Response::class, $response);
	}

	/**
	 * @param string $url
	 * @param string $token
	 * @param string $body
	 * @param int $statusCode
	 * @param string $fixture
	 *
	 * @dataProvider ./../../../fixtures/Controllers/accountUpdate.php
	 */
	public function testUpdate(string $url, string $token, string $body, int $statusCode, string $fixture): void
	{
		/** @var Router\Router $router */
		$router = $this->getContainer()->getByType(Router\Router::class);

		$request = new ServerRequest(
			RequestMethodInterface::METHOD_PATCH,
			$url,
			[
				'authorization' => $token,
			],
			$body
		);

		$dateTimeFactory = Mockery::mock(NodeLibsHelpers\DateFactory::class);
		$dateTimeFactory
			->shouldReceive('getNow')
			->andReturn(new DateTimeImmutable('2020-04-01T12:00:00+00:00'));

		$this->mockContainerService(
			NodeLibsHelpers\IDateFactory::class,
			$dateTimeFactory
		);

		/** @var Controllers\SessionV1Controller $controller */
		$controller = $this->getContainer()->getByType(Controllers\SessionV1Controller::class);
		$controller->injectDateFactory($dateTimeFactory);

		$response = $router->handle($request);

		$body = (string) $response->getBody();

		Tools\JsonAssert::assertFixtureMatch($fixture, $body);
		Assert::same($statusCode, $response->getStatusCode());
		Assert::type(Http\Response::class, $response);
	}

	/**
	 * @param string $url
	 * @param string $token
	 * @param int $statusCode
	 * @param string $fixture
	 *
	 * @dataProvider ./../../../fixtures/Controllers/accountRelationships.php
	 */
	public function testReadRelationship(string $url, string $token, int $statusCode, string $fixture): void
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

		$dateTimeFactory = Mockery::mock(NodeLibsHelpers\DateFactory::class);
		$dateTimeFactory
			->shouldReceive('getNow')
			->andReturn(new DateTimeImmutable('2020-04-01T12:00:00+00:00'));

		$this->mockContainerService(
			NodeLibsHelpers\IDateFactory::class,
			$dateTimeFactory
		);

		/** @var Controllers\SessionV1Controller $controller */
		$controller = $this->getContainer()->getByType(Controllers\SessionV1Controller::class);
		$controller->injectDateFactory($dateTimeFactory);

		$response = $router->handle($request);

		$body = (string) $response->getBody();

		Tools\JsonAssert::assertFixtureMatch($fixture, $body);
		Assert::same($statusCode, $response->getStatusCode());
		Assert::type(Http\Response::class, $response);
	}

}

$test_case = new AccountV1ControllerTest();
$test_case->run();
