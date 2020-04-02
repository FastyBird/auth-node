<?php declare(strict_types = 1);

namespace Tests\Cases;

use DateTimeImmutable;
use FastyBird\AccountsNode\Controllers;
use FastyBird\AccountsNode\Router;
use FastyBird\NodeLibs\Helpers as NodeLibsHelpers;
use FastyBird\NodeWebServer\Http;
use Fig\Http\Message\RequestMethodInterface;
use Mockery;
use Nette\Utils;
use React\Http\Io\ServerRequest;
use Tester\Assert;
use Tests\Tools;

require_once __DIR__ . '/../../../bootstrap.php';
require_once __DIR__ . '/../DbTestCase.php';

final class SessionV1ControllerTest extends DbTestCase
{

	/**
	 * @param string $url
	 * @param string $token
	 * @param int $statusCode
	 * @param string $fixture
	 *
	 * @dataProvider ./../../../fixtures/Controllers/sessionRead.php
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
	 * @param string $body
	 * @param int $statusCode
	 * @param string $fixture
	 *
	 * @dataProvider ./../../../fixtures/Controllers/sessionCreate.php
	 */
	public function testCreate(string $url, string $body, int $statusCode, string $fixture): void
	{
		/** @var Router\Router $router */
		$router = $this->getContainer()->getByType(Router\Router::class);

		$request = new ServerRequest(
			RequestMethodInterface::METHOD_POST,
			$url,
			[],
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

		$actual = Utils\Json::decode($body, Utils\Json::FORCE_ARRAY);

		Tools\JsonAssert::assertFixtureMatch(
			$fixture,
			$body,
			function (string $expectation) use ($actual): string {
				if (isset($actual['data']['attributes'])) {
					$expectation = str_replace('__ACCESS_TOKEN__', $actual['data']['attributes']['token'], $expectation);
					$expectation = str_replace('__REFRESH_TOKEN__', $actual['data']['attributes']['refresh'], $expectation);
				}

				return $expectation;
			}
		);
		Assert::same($statusCode, $response->getStatusCode());
		Assert::type(Http\Response::class, $response);
	}

	/**
	 * @param string $url
	 * @param string $body
	 * @param int $statusCode
	 * @param string $fixture
	 *
	 * @dataProvider ./../../../fixtures/Controllers/sessionUpdate.php
	 */
	public function testUpdate(string $url, string $body, int $statusCode, string $fixture): void
	{
		/** @var Router\Router $router */
		$router = $this->getContainer()->getByType(Router\Router::class);

		$request = new ServerRequest(
			RequestMethodInterface::METHOD_PATCH,
			$url,
			[],
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

		$actual = Utils\Json::decode($body, Utils\Json::FORCE_ARRAY);

		Tools\JsonAssert::assertFixtureMatch(
			$fixture,
			$body,
			function (string $expectation) use ($actual): string {
				if (isset($actual['data']['attributes'])) {
					$expectation = str_replace('__ACCESS_TOKEN__', $actual['data']['attributes']['token'], $expectation);
					$expectation = str_replace('__REFRESH_TOKEN__', $actual['data']['attributes']['refresh'], $expectation);
				}

				return $expectation;
			}
		);
		Assert::same($statusCode, $response->getStatusCode());
		Assert::type(Http\Response::class, $response);
	}

	/**
	 * @param string $url
	 * @param string $token
	 * @param int $statusCode
	 * @param string $fixture
	 *
	 * @dataProvider ./../../../fixtures/Controllers/sessionDelete.php
	 */
	public function testDelete(string $url, string $token, int $statusCode, string $fixture): void
	{
		/** @var Router\Router $router */
		$router = $this->getContainer()->getByType(Router\Router::class);

		$request = new ServerRequest(
			RequestMethodInterface::METHOD_DELETE,
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
	 * @param int $statusCode
	 * @param string $fixture
	 *
	 * @dataProvider ./../../../fixtures/Controllers/sessionRelationships.php
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

	/**
	 * @param string $url
	 * @param string $body
	 * @param int $statusCode
	 * @param string $fixture
	 *
	 * @dataProvider ./../../../fixtures/Controllers/sessionValidate.php
	 */
	public function testValidate(string $url, string $body, int $statusCode, string $fixture): void
	{
		/** @var Router\Router $router */
		$router = $this->getContainer()->getByType(Router\Router::class);

		$request = new ServerRequest(
			RequestMethodInterface::METHOD_POST,
			$url,
			[],
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

}

$test_case = new SessionV1ControllerTest();
$test_case->run();
