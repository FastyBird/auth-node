<?php declare(strict_types = 1);

/**
 * AuthenticateV1Controller.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Controllers
 * @since          0.1.3
 *
 * @date           16.04.20
 */

namespace FastyBird\AuthNode\Controllers;

use FastyBird\AuthNode\Models;
use FastyBird\AuthNode\Queries;
use FastyBird\NodeWebServer\Http as NodeWebServerHttp;
use Fig\Http\Message\StatusCodeInterface;
use Nette\Utils;
use Psr\Http\Message;

/**
 * User authentication controller
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Controllers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class AuthenticateV1Controller extends BaseV1Controller
{

	/** @var Models\Vernemq\IAccountRepository */
	private $verneAccountRepository;

	public function __construct(
		Models\Vernemq\IAccountRepository $verneAccountRepository
	) {
		$this->verneAccountRepository = $verneAccountRepository;
	}

	/**
	 * @param Message\ServerRequestInterface $request
	 * @param NodeWebServerHttp\Response $response
	 *
	 * @return NodeWebServerHttp\Response
	 *
	 * @throws Utils\JsonException
	 */
	public function vernemq(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		try {
			$data = Utils\ArrayHash::from(Utils\Json::decode($request->getBody()->getContents(), Utils\Json::FORCE_ARRAY));

		} catch (Utils\JsonException $ex) {
			/** @var NodeWebServerHttp\Response $response */
			$response = $response
				->withStatus(StatusCodeInterface::STATUS_FORBIDDEN);

			return $response;
		}

		$username = $data->offsetExists('username') && $data->offsetGet('username') !== null ? (string) $data->offsetGet('username') : null;
		$password = $data->offsetExists('password') && $data->offsetGet('password') !== null ? (string) $data->offsetGet('password') : null;

		if (
			$username === null
			|| $username === ''
			|| $password === null
			|| $password === ''
		) {
			/** @var NodeWebServerHttp\Response $response */
			$response = $response
				->withStatus(StatusCodeInterface::STATUS_FORBIDDEN);

			return $response;
		}

		$findAccount = new Queries\FindVerneMqAccountsQuery();
		$findAccount->byUsername($username);

		$verneMqAccount = $this->verneAccountRepository->findOneBy($findAccount);

		if ($verneMqAccount === null) {
			/** @var NodeWebServerHttp\Response $response */
			$response = $response
				->withStatus(StatusCodeInterface::STATUS_FORBIDDEN);

			return $response;
		}

		if (hash('sha256', $password, false) !== $verneMqAccount->getPassword()) {
			/** @var NodeWebServerHttp\Response $response */
			$response = $response
				->withStatus(StatusCodeInterface::STATUS_FORBIDDEN);

			return $response;
		}

		$response
			->getBody()
			->write(Utils\Json::encode([
				'result'        => 'ok',
				'publish_acl'   => $verneMqAccount->getPublishAcl(),
				'subscribe_acl' => $verneMqAccount->getSubscribeAcl(),
			]));

		return $response;
	}

}
