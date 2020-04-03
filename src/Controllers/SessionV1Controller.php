<?php declare(strict_types = 1);

/**
 * SessionV1Controller.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AccountsNode!
 * @subpackage     Controllers
 * @since          0.1.0
 *
 * @date           31.03.20
 */

namespace FastyBird\AccountsNode\Controllers;

use DateTimeImmutable;
use Doctrine;
use FastyBird\AccountsNode\Entities;
use FastyBird\AccountsNode\Exceptions;
use FastyBird\AccountsNode\Models;
use FastyBird\AccountsNode\Router;
use FastyBird\AccountsNode\Schemas;
use FastyBird\AccountsNode\Security;
use FastyBird\AccountsNode\Types;
use FastyBird\NodeWebServer\Exceptions as NodeWebServerExceptions;
use FastyBird\NodeWebServer\Http as NodeWebServerHttp;
use Fig\Http\Message\StatusCodeInterface;
use Nette\Utils;
use Psr\Http\Message;
use Throwable;

/**
 * User session controller
 *
 * @package        FastyBird:AccountsNode!
 * @subpackage     Controllers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class SessionV1Controller extends BaseV1Controller
{

	/** @var Models\Tokens\ITokenRepository */
	private $tokenRepository;

	/** @var Models\Tokens\ITokensManager */
	private $tokensManager;

	/** @var Models\Identities\IIdentityRepository */
	private $identityRepository;

	/** @var Security\TokenReader */
	private $tokenReader;

	/** @var string */
	protected $translationDomain = 'node.session';

	public function __construct(
		Models\Tokens\ITokenRepository $tokenRepository,
		Models\Tokens\ITokensManager $tokensManager,
		Models\Identities\IIdentityRepository $identityRepository,
		Security\TokenReader $tokenReader
	) {
		$this->tokenRepository = $tokenRepository;
		$this->tokensManager = $tokensManager;
		$this->identityRepository = $identityRepository;

		$this->tokenReader = $tokenReader;
	}

	/**
	 * @param Message\ServerRequestInterface $request
	 * @param NodeWebServerHttp\Response $response
	 *
	 * @return NodeWebServerHttp\Response
	 *
	 * @throws NodeWebServerExceptions\IJsonApiException
	 */
	public function read(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		$token = $this->getToken($request);

		return $response
			->withEntity(NodeWebServerHttp\ScalarEntity::from($token));
	}

	/**
	 * @param Message\ServerRequestInterface $request
	 * @param NodeWebServerHttp\Response $response
	 *
	 * @return NodeWebServerHttp\Response
	 *
	 * @throws NodeWebServerExceptions\IJsonApiException
	 * @throws Doctrine\DBAL\ConnectionException
	 */
	public function create(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		$document = $this->createDocument($request);

		$attributes = $document->getResource()->getAttributes();

		if (!$attributes->has('uid')) {
			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('//node.base.messages.missingRequired.heading'),
				$this->translator->translate('//node.base.messages.missingRequired.message'),
				[
					'pointer' => '/data/attributes/uid',
				]
			);
		}

		if (!$attributes->has('password')) {
			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('//node.base.messages.missingRequired.heading'),
				$this->translator->translate('//node.base.messages.missingRequired.message'),
				[
					'pointer' => '/data/attributes/password',
				]
			);
		}

		try {
			// Login user with system authenticator
			$this->user->login((string) $attributes->get('uid'), (string) $attributes->get('password'));

		} catch (Throwable $ex) {
			if ($ex instanceof Exceptions\AccountNotFoundException) {
				if ($ex->getCode() === Security\Authenticator::IDENTITY_EMAIL_NOT_FOUND) {
					throw new NodeWebServerExceptions\JsonApiErrorException(
						StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
						$this->translator->translate('messages.unknownAccount.heading'),
						$this->translator->translate('messages.unknownAccount.message')
					);
				}

				throw new NodeWebServerExceptions\JsonApiErrorException(
					StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
					$this->translator->translate('messages.unknownAccount.heading'),
					$this->translator->translate('messages.unknownAccount.message')
				);

			} elseif ($ex instanceof Exceptions\AuthenticationFailedException) {
				switch ($ex->getCode()) {
					case Security\Authenticator::ACCOUNT_PROFILE_BLOCKED:
						throw new NodeWebServerExceptions\JsonApiErrorException(
							StatusCodeInterface::STATUS_FORBIDDEN,
							$this->translator->translate('messages.accountBlocked.heading'),
							$this->translator->translate('messages.accountBlocked.message')
						);

					case Security\Authenticator::ACCOUNT_PROFILE_DELETED:
						throw new NodeWebServerExceptions\JsonApiErrorException(
							StatusCodeInterface::STATUS_FORBIDDEN,
							$this->translator->translate('messages.accountDeleted.heading'),
							$this->translator->translate('messages.accountDeleted.message')
						);

					default:
						throw new NodeWebServerExceptions\JsonApiErrorException(
							StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
							$this->translator->translate('messages.unknownAccount.heading'),
							$this->translator->translate('messages.unknownAccount.message')
						);
				}

			} else {
				// Log catched exception
				$this->logger->error('[CONTROLLER] ' . $ex->getMessage(), [
					'exception' => [
						'message' => $ex->getMessage(),
						'code'    => $ex->getCode(),
					],
				]);

				throw new NodeWebServerExceptions\JsonApiErrorException(
					StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
					$this->translator->translate('messages.notCreated.heading'),
					$this->translator->translate('messages.notCreated.message')
				);
			}
		}

		try {
			// Start transaction connection to the database
			$this->getOrmConnection()->beginTransaction();

			$values = Utils\ArrayHash::from([
				'entity'    => Entities\Tokens\AccessToken::class,
				'validTill' => $this->getNow()->modify(Entities\Tokens\IAccessToken::TOKEN_EXPIRATION),
				'status'    => Types\TokenStatusType::get(Types\TokenStatusType::STATE_ACTIVE),
				'identity'  => $this->user->getIdentity(),
			]);

			$accessToken = $this->tokensManager->create($values);

			$values = Utils\ArrayHash::from([
				'entity'      => Entities\Tokens\RefreshToken::class,
				'accessToken' => $accessToken,
				'validTill'   => $this->getNow()->modify(Entities\Tokens\IRefreshToken::TOKEN_EXPIRATION),
				'status'      => Types\TokenStatusType::get(Types\TokenStatusType::STATE_ACTIVE),
			]);

			$this->tokensManager->create($values);

			// Commit all changes into database
			$this->getOrmConnection()->commit();

		} catch (Throwable $ex) {
			// Revert all changes when error occur
			$this->getOrmConnection()->rollback();

			// Log catched exception
			$this->logger->error('[CONTROLLER] ' . $ex->getMessage(), [
				'exception' => [
					'message' => $ex->getMessage(),
					'code'    => $ex->getCode(),
				],
			]);

			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('messages.notCreated.heading'),
				$this->translator->translate('messages.notCreated.message'),
				[
					'pointer' => '/data/attributes/uid',
				]
			);
		}

		/** @var NodeWebServerHttp\Response $response */
		$response = $response
			->withEntity(NodeWebServerHttp\ScalarEntity::from($accessToken))
			->withStatus(StatusCodeInterface::STATUS_CREATED);

		return $response;
	}

	/**
	 * @param Message\ServerRequestInterface $request
	 * @param NodeWebServerHttp\Response $response
	 *
	 * @return NodeWebServerHttp\Response
	 *
	 * @throws NodeWebServerExceptions\IJsonApiException
	 * @throws Doctrine\DBAL\ConnectionException
	 */
	public function update(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		$document = $this->createDocument($request);

		$attributes = $document->getResource()->getAttributes();

		if (!$attributes->has('refresh')) {
			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('//node.base.messages.missingRequired.heading'),
				$this->translator->translate('//node.base.messages.missingRequired.message'),
				[
					'pointer' => '/data/attributes/refresh',
				]
			);
		}

		/** @var Entities\Tokens\IRefreshToken|null $refreshToken */
		$refreshToken = $this->tokenRepository->findOneByToken((string) $attributes->get('refresh'), Entities\Tokens\RefreshToken::class);

		if ($refreshToken === null) {
			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_NOT_FOUND,
				$this->translator->translate('messages.invalidRefreshToken.heading'),
				$this->translator->translate('messages.invalidRefreshToken.message')
			);
		}

		if (
			$refreshToken->getValidTill() !== null
			&& $refreshToken->getValidTill() < $this->dateFactory->getNow()
		) {
			// Remove expired tokens
			$this->tokensManager->delete($refreshToken->getAccessToken());

			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_NOT_FOUND,
				$this->translator->translate('messages.refreshTokenExpired.heading'),
				$this->translator->translate('messages.refreshTokenExpired.message')
			);
		}

		$accessToken = $refreshToken->getAccessToken();

		try {
			// Start transaction connection to the database
			$this->getOrmConnection()->beginTransaction();

			// Auto-login user
			$this->user->login($accessToken->getIdentity());

			$values = Utils\ArrayHash::from([
				'entity'    => Entities\Tokens\AccessToken::class,
				'validTill' => $this->getNow()->modify(Entities\Tokens\IAccessToken::TOKEN_EXPIRATION),
				'status'    => Types\TokenStatusType::get(Types\TokenStatusType::STATE_ACTIVE),
				'identity'  => $this->user->getIdentity(),
			]);

			$newAccessToken = $this->tokensManager->create($values);

			$values = Utils\ArrayHash::from([
				'entity'      => Entities\Tokens\RefreshToken::class,
				'accessToken' => $newAccessToken,
				'validTill'   => $this->getNow()->modify(Entities\Tokens\IRefreshToken::TOKEN_EXPIRATION),
				'status'      => Types\TokenStatusType::get(Types\TokenStatusType::STATE_ACTIVE),
			]);

			$this->tokensManager->create($values);

			$this->tokensManager->delete($refreshToken);
			$this->tokensManager->delete($accessToken);

			// Commit all changes into database
			$this->getOrmConnection()->commit();

		} catch (NodeWebServerExceptions\IJsonApiException $ex) {
			// Revert all changes when error occur
			$this->getOrmConnection()->rollBack();

			throw $ex;

		} catch (Throwable $ex) {
			// Revert all changes when error occur
			$this->getOrmConnection()->rollback();

			// Log catched exception
			$this->logger->error('[CONTROLLER] ' . $ex->getMessage(), [
				'exception' => [
					'message' => $ex->getMessage(),
					'code'    => $ex->getCode(),
				],
			]);

			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('messages.refreshingTokenFailed.heading'),
				$this->translator->translate('messages.refreshingTokenFailed.message'),
				[
					'pointer' => '/data/attributes/uid',
				]
			);
		}

		/** @var NodeWebServerHttp\Response $response */
		$response = $response
			->withEntity(NodeWebServerHttp\ScalarEntity::from($newAccessToken))
			->withStatus(StatusCodeInterface::STATUS_CREATED);

		return $response;
	}

	/**
	 * @param Message\ServerRequestInterface $request
	 * @param NodeWebServerHttp\Response $response
	 *
	 * @return NodeWebServerHttp\Response
	 *
	 * @throws NodeWebServerExceptions\IJsonApiException
	 * @throws Doctrine\DBAL\ConnectionException
	 */
	public function delete(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		$accessToken = $this->getToken($request);

		try {
			// Start transaction connection to the database
			$this->getOrmConnection()->beginTransaction();

			if ($accessToken->getRefreshToken() !== null) {
				$this->tokensManager->delete($accessToken->getRefreshToken());
			}

			$this->tokensManager->delete($accessToken);

			$this->user->logout(true);

			// Commit all changes into database
			$this->getOrmConnection()->commit();

		} catch (NodeWebServerExceptions\IJsonApiException $ex) {
			// Revert all changes when error occur
			$this->getOrmConnection()->rollBack();

			throw $ex;

		} catch (Throwable $ex) {
			// Revert all changes when error occur
			$this->getOrmConnection()->rollback();

			// Log catched exception
			$this->logger->error('[CONTROLLER] ' . $ex->getMessage(), [
				'exception' => [
					'message' => $ex->getMessage(),
					'code'    => $ex->getCode(),
				],
			]);

			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('messages.destroyingSessionFailed.heading'),
				$this->translator->translate('messages.destroyingSessionFailed.message'),
				[
					'pointer' => '/data/attributes/uid',
				]
			);
		}

		/** @var NodeWebServerHttp\Response $response */
		$response = $response
			->withStatus(StatusCodeInterface::STATUS_NO_CONTENT);

		return $response;
	}

	/**
	 * @param Message\ServerRequestInterface $request
	 * @param NodeWebServerHttp\Response $response
	 *
	 * @return NodeWebServerHttp\Response
	 *
	 * @throws NodeWebServerExceptions\IJsonApiException
	 */
	public function readRelationship(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		if ($this->user->getAccount() === null) {
			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_FORBIDDEN,
				$this->translator->translate('//node.base.messages.forbidden.heading'),
				$this->translator->translate('//node.base.messages.forbidden.message')
			);
		}

		$relationEntity = strtolower($request->getAttribute(Router\Router::RELATION_ENTITY));

		if ($relationEntity === Schemas\SessionSchema::RELATIONSHIPS_ACCOUNT) {
			return $response
				->withEntity(NodeWebServerHttp\ScalarEntity::from($this->user->getAccount()));
		}

		$this->throwUnknownRelation($relationEntity);

		return $response;
	}

	/**
	 * @param Message\ServerRequestInterface $request
	 * @param NodeWebServerHttp\Response $response
	 *
	 * @return NodeWebServerHttp\Response
	 *
	 * @throws NodeWebServerExceptions\IJsonApiException
	 */
	public function validate(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		$document = $this->createDocument($request);

		$attributes = $document->getResource()->getAttributes();

		if (!$attributes->has('uid')) {
			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('//node.base.messages.missingRequired.heading'),
				$this->translator->translate('//node.base.messages.missingRequired.message'),
				[
					'pointer' => '/data/attributes/uid',
				]
			);
		}

		$account = $this->findIdentityAccount((string) $attributes->get('uid'));

		if ($account === null) {
			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_NOT_FOUND,
				$this->translator->translate('messages.unknownAccount.heading'),
				$this->translator->translate('messages.unknownAccount.message'),
				[
					'pointer' => '/data/attributes/uid',
				]
			);

		} elseif ($account->isDeleted()) {
			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_NOT_FOUND,
				$this->translator->translate('messages.unknownAccount.heading'),
				$this->translator->translate('messages.unknownAccount.message'),
				[
					'pointer' => '/data/attributes/uid',
				]
			);
		}

		/** @var NodeWebServerHttp\Response $response */
		$response = $response
			->withStatus(StatusCodeInterface::STATUS_NO_CONTENT);

		return $response;
	}

	/**
	 * @return DateTimeImmutable
	 */
	private function getNow(): DateTimeImmutable
	{
		/** @var DateTimeImmutable $now */
		$now = $this->dateFactory->getNow();

		return $now;
	}

	/**
	 * @param string $uid
	 *
	 * @return Entities\Accounts\IAccount|null
	 */
	private function findIdentityAccount(string $uid): ?Entities\Accounts\IAccount
	{
		/** @var Entities\Identities\System|null $identity */
		$identity = $this->identityRepository->findOneByUid($uid, Entities\Identities\System::class);

		if ($identity === null) {
			/** @var Entities\Identities\System|null $identity */
			$identity = $this->identityRepository->findOneByEmail($uid, Entities\Identities\System::class);
		}

		return $identity ? $identity->getAccount() : null;
	}

	/**
	 * @param Message\ServerRequestInterface $request
	 *
	 * @return Entities\Tokens\IAccessToken
	 *
	 * @throws NodeWebServerExceptions\IJsonApiException
	 */
	private function getToken(Message\ServerRequestInterface $request): Entities\Tokens\IAccessToken
	{
		$token = $this->tokenReader->read($request);

		if (
			$token === null
			|| !$token instanceof Entities\Tokens\IAccessToken
			|| !$token->isValid()
		) {
			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_FORBIDDEN,
				$this->translator->translate('//node.base.messages.forbidden.heading'),
				$this->translator->translate('//node.base.messages.forbidden.message')
			);
		}

		if (
			$this->user->getAccount() !== null
			&& $token->getIdentity()->getAccount()->getId()->equals($this->user->getAccount()->getId())
		) {
			return $token;
		}

		throw new NodeWebServerExceptions\JsonApiErrorException(
			StatusCodeInterface::STATUS_FORBIDDEN,
			$this->translator->translate('//node.base.messages.forbidden.heading'),
			$this->translator->translate('//node.base.messages.forbidden.message')
		);
	}

}
