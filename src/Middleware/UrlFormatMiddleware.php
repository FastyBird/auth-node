<?php declare(strict_types = 1);

/**
 * UrlFormatMiddleware.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Middleware
 * @since          0.1.0
 *
 * @date           21.06.20
 */

namespace FastyBird\AuthNode\Middleware;

use Contributte\Translation;
use FastyBird\AuthNode\Security;
use FastyBird\NodeJsonApi\Exceptions as NodeJsonApiExceptions;
use Fig\Http\Message\StatusCodeInterface;
use IPub\SlimRouter\Http;
use Nette\Utils;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Access token check middleware
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Middleware
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class UrlFormatMiddleware implements MiddlewareInterface
{

	/** @var Security\User */
	private $user;

	/** @var Translation\Translator */
	private $translator;

	public function __construct(
		Security\User $user,
		Translation\Translator $translator
	) {
		$this->user = $user;

		$this->translator = $translator;
	}

	/**
	 * @param ServerRequestInterface $request
	 * @param RequestHandlerInterface $handler
	 *
	 * @return ResponseInterface
	 *
	 * @throws NodeJsonApiExceptions\IJsonApiException
	 * @throws Translation\Exceptions\InvalidArgument
	 */
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		$response = $handler->handle($request);

		if (
			$this->user->isLoggedIn()
			&& (
				Utils\Strings::startsWith($request->getUri()->getPath(), '/v1/session')
				|| Utils\Strings::startsWith($request->getUri()->getPath(), '/v1/me')
			)
		) {
			if ($this->user->getAccount() === null) {
				throw new NodeJsonApiExceptions\JsonApiErrorException(
					StatusCodeInterface::STATUS_BAD_REQUEST,
					$this->translator->translate('//node.base.messages.failed.heading'),
					$this->translator->translate('//node.base.messages.failed.message')
				);
			}

			$body = $response->getBody();
			$body->rewind();

			$content = $body->getContents();
			$content = str_replace('\/v1\/accounts\/' . $this->user->getAccount()->getPlainId(), '\/v1\/me', $content);

			$response = $response->withBody(Http\Stream::fromBodyString($content));
		}

		return $response;
	}

}
