<?php declare(strict_types = 1);

/**
 * AuthenticateV1Controller.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AccountsNode!
 * @subpackage     Controllers
 * @since          0.1.3
 *
 * @date           16.04.20
 */

namespace FastyBird\AccountsNode\Controllers;

use Doctrine;
use FastyBird\AccountsNode\Exceptions;
use FastyBird\AccountsNode\Models;
use FastyBird\NodeWebServer\Http as NodeWebServerHttp;
use Fig\Http\Message\StatusCodeInterface;
use Nette\Utils;
use Psr\Http\Message;
use stdClass;
use Throwable;

/**
 * User authentication controller
 *
 * @package        FastyBird:AccountsNode!
 * @subpackage     Controllers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class AuthenticateV1Controller extends BaseV1Controller
{

	/**
	 * @param Message\ServerRequestInterface $request
	 * @param NodeWebServerHttp\Response $response
	 *
	 * @return NodeWebServerHttp\Response
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

		try {
			// Login user with system authenticator
			$this->user->login($username, $password);

			$publishRule1 = new stdClass();
			$publishRule1->pattern = '/fb/+/+/+/+/+/+/+/+';

			$publishRule2 = new stdClass();
			$publishRule2->pattern = '/fb/+/+/$child/+/+/+/+/+/+/+';

			$subscribeRule1 = new stdClass();
			$subscribeRule1->pattern = '/fb/+/+/+/+/+/+/+/+';

			$subscribeRule2 = new stdClass();
			$subscribeRule2->pattern = '/fb/+/+/$child/+/+/+/+/+/+/+';

			$subscribeRule3 = new \stdClass();
			$subscribeRule3->pattern = '$SYS/broker/log/#';

			try {
				/** @var NodeWebServerHttp\Response $response */
				$response
					->getBody()
					->write(Utils\Json::encode([
						'result'        => 'ok',
						'publish_acl'   => [
							$publishRule1,
							$publishRule2,
						],
						'subscribe_acl' => [
							$subscribeRule1,
							$subscribeRule2,
							$subscribeRule3,
						],
					]));

				return $response;

			} catch (Utils\JsonException $ex) {
				/** @var NodeWebServerHttp\Response $response */
				$response = $response
					->withStatus(StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);

				return $response;
			}

		} catch (Throwable $ex) {
			if ($ex instanceof Exceptions\AccountNotFoundException) {
				/** @var NodeWebServerHttp\Response $response */
				$response = $response
					->withStatus(StatusCodeInterface::STATUS_FORBIDDEN);

				return $response;

			} elseif ($ex instanceof Exceptions\AuthenticationFailedException) {
				/** @var NodeWebServerHttp\Response $response */
				$response = $response
					->withStatus(StatusCodeInterface::STATUS_FORBIDDEN);

				return $response;

			} else {
				// Log catched exception
				$this->logger->error('[CONTROLLER] ' . $ex->getMessage(), [
					'exception' => [
						'message' => $ex->getMessage(),
						'code'    => $ex->getCode(),
					],
				]);

				/** @var NodeWebServerHttp\Response $response */
				$response = $response
					->withStatus(StatusCodeInterface::STATUS_FORBIDDEN);

				return $response;
			}
		}
	}

}
