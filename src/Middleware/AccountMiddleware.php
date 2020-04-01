<?php declare(strict_types = 1);

/**
 * AccountMiddleware.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AccountsNode!
 * @subpackage     Middleware
 * @since          0.1.0
 *
 * @date           01.04.20
 */

namespace FastyBird\AccountsNode\Middleware;

use Contributte\Translation;
use FastyBird\AccountsNode\Entities;
use FastyBird\AccountsNode\Security;
use FastyBird\NodeWebServer\Exceptions as NodeWebServerExceptions;
use Fig\Http\Message\StatusCodeInterface;
use Nette\Security as NS;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * API key check middleware
 *
 * @package        iPublikuj:UserProfileModule!
 * @subpackage     Middleware
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
final class AccountMiddleware implements MiddlewareInterface
{

	/** @var Security\User */
	private $user;

	/** @var Security\TokenReader */
	private $tokenReader;

	/** @var Translation\Translator */
	private $translator;

	public function __construct(
		Security\User $user,
		Security\TokenReader $tokenReader,
		Translation\Translator $translator
	) {
		$this->user = $user;

		$this->tokenReader = $tokenReader;

		$this->translator = $translator;
	}

	/**
	 * @param ServerRequestInterface $request
	 * @param RequestHandlerInterface $handler
	 *
	 * @return ResponseInterface
	 *
	 * @throws NodeWebServerExceptions\IJsonApiException
	 * @throws Translation\Exceptions\InvalidArgument
	 * @throws NS\AuthenticationException
	 */
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		// Request has to have Authorization header
		if ($request->hasHeader('Authorization')) {
			$token = $this->tokenReader->read();

			if (
				$token === null
				|| !$token instanceof Entities\Tokens\IAccessToken
				|| !$token->isValid()
			) {
				throw new NodeWebServerExceptions\JsonApiErrorException(
					StatusCodeInterface::STATUS_UNAUTHORIZED,
					$this->translator->translate('messages.notAuthorized.heading'),
					$this->translator->translate('messages.notAuthorized.message')
				);

			} else {
				$this->user->login($token->getIdentity());
			}

		} else {
			$this->user->logout(true);
		}

		return $handler->handle($request);
	}

}
