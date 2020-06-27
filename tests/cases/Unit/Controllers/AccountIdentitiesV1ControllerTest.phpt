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

final class AccountIdentitiesV1ControllerTest extends DbTestCase
{

	/**
	 * @param string $url
	 * @param string $token
	 * @param string $body
	 * @param int $statusCode
	 * @param string $fixture
	 *
	 * @dataProvider ./../../../fixtures/Controllers/account.identitiesUpdate.php
	 */
	public function XtestUpdate(string $url, string $token, string $body, int $statusCode, string $fixture): void
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

		$response = $router->handle($request);

		Tools\JsonAssert::assertFixtureMatch(
			$fixture,
			(string) $response->getBody()
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
	 * @dataProvider ./../../../fixtures/Controllers/account.identitiesRequestPassword.php
	 */
	public function XtestRequestPassword(string $url, string $body, int $statusCode, string $fixture): void
	{
		/** @var Router\Router $router */
		$router = $this->getContainer()->getByType(Router\Router::class);

		$request = new ServerRequest(
			RequestMethodInterface::METHOD_POST,
			$url,
			[],
			$body
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

$test_case = new AccountIdentitiesV1ControllerTest();
$test_case->run();
