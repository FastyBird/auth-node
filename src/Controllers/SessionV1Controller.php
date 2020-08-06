<?php declare(strict_types = 1);

/**
 * SessionV1Controller.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Controllers
 * @since          0.1.0
 *
 * @date           31.03.20
 */

namespace FastyBird\AuthNode\Controllers;

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine;
use FastyBird\AuthNode\Entities;
use FastyBird\AuthNode\Exceptions;
use FastyBird\AuthNode\Router;
use FastyBird\AuthNode\Schemas;
use FastyBird\AuthNode\Security;
use FastyBird\NodeAuth\Models as NodeAuthModels;
use FastyBird\NodeAuth\Queries as NodeAuthQueries;
use FastyBird\NodeAuth\Security as NodeAuthSecurity;
use FastyBird\NodeAuth\Types as NodeAuthTypes;
use FastyBird\NodeJsonApi\Exceptions as NodeJsonApiExceptions;
use FastyBird\NodeWebServer\Http as NodeWebServerHttp;
use Fig\Http\Message\StatusCodeInterface;
use Nette\Utils;
use Psr\Http\Message;
use Ramsey\Uuid;
use Throwable;

/**
 * User session controller
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Controllers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class SessionV1Controller extends BaseV1Controller
{

	/** @var NodeAuthModels\Tokens\ITokenRepository */
	private $tokenRepository;

	/** @var NodeAuthModels\Tokens\ITokensManager */
	private $tokensManager;

	/** @var NodeAuthSecurity\TokenReader */
	private $tokenReader;

	/** @var NodeAuthSecurity\TokenBuilder */
	private $tokenBuilder;

	/** @var string */
	protected $translationDomain = 'node.session';

	public function __construct(
		NodeAuthModels\Tokens\ITokenRepository $tokenRepository,
		NodeAuthModels\Tokens\ITokensManager $tokensManager,
		NodeAuthSecurity\TokenReader $tokenReader,
		NodeAuthSecurity\TokenBuilder $tokenBuilder
	) {
		$this->tokenRepository = $tokenRepository;
		$this->tokensManager = $tokensManager;

		$this->tokenReader = $tokenReader;
		$this->tokenBuilder = $tokenBuilder;
	}

	/**
	 * @param Message\ServerRequestInterface $request
	 * @param NodeWebServerHttp\Response $response
	 *
	 * @return NodeWebServerHttp\Response
	 *
	 * @throws NodeJsonApiExceptions\IJsonApiException
	 *
	 * @Secured
	 * @Secured\User(loggedIn)
	 */
	public function read(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		$accessToken = $this->getToken($request);

		return $response
			->withEntity(NodeWebServerHttp\ScalarEntity::from($accessToken));
	}

	/**
	 * @param Message\ServerRequestInterface $request
	 * @param NodeWebServerHttp\Response $response
	 *
	 * @return NodeWebServerHttp\Response
	 *
	 * @throws NodeJsonApiExceptions\IJsonApiException
	 * @throws Doctrine\DBAL\ConnectionException
	 *
	 * @Secured
	 * @Secured\User(guest)
	 */
	public function create(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		$document = $this->createDocument($request);

		$attributes = $document->getResource()->getAttributes();

		if (!$attributes->has('uid')) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('//node.base.messages.missingRequired.heading'),
				$this->translator->translate('//node.base.messages.missingRequired.message'),
				[
					'pointer' => '/data/attributes/uid',
				]
			);
		}

		if (!$attributes->has('password')) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
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
				if ($ex->getCode() === Security\Authenticator::IDENTITY_UID_NOT_FOUND) {
					throw new NodeJsonApiExceptions\JsonApiErrorException(
						StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
						$this->translator->translate('messages.unknownAccount.heading'),
						$this->translator->translate('messages.unknownAccount.message')
					);
				}

				throw new NodeJsonApiExceptions\JsonApiErrorException(
					StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
					$this->translator->translate('messages.unknownAccount.heading'),
					$this->translator->translate('messages.unknownAccount.message')
				);

			} elseif ($ex instanceof Exceptions\AuthenticationFailedException) {
				switch ($ex->getCode()) {
					case Security\Authenticator::ACCOUNT_PROFILE_BLOCKED:
						throw new NodeJsonApiExceptions\JsonApiErrorException(
							StatusCodeInterface::STATUS_FORBIDDEN,
							$this->translator->translate('messages.accountBlocked.heading'),
							$this->translator->translate('messages.accountBlocked.message')
						);

					case Security\Authenticator::ACCOUNT_PROFILE_DELETED:
						throw new NodeJsonApiExceptions\JsonApiErrorException(
							StatusCodeInterface::STATUS_FORBIDDEN,
							$this->translator->translate('messages.accountDeleted.heading'),
							$this->translator->translate('messages.accountDeleted.message')
						);

					default:
						throw new NodeJsonApiExceptions\JsonApiErrorException(
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

				throw new NodeJsonApiExceptions\JsonApiErrorException(
					StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
					$this->translator->translate('messages.notCreated.heading'),
					$this->translator->translate('messages.notCreated.message')
				);
			}
		}

		try {
			// Start transaction connection to the database
			$this->getOrmConnection()->beginTransaction();

			$validTill = $this->getNow()->modify(Entities\Tokens\IAccessToken::TOKEN_EXPIRATION);

			$accessTokenId = Uuid\Uuid::uuid4();

			$values = Utils\ArrayHash::from([
				'id'        => $accessTokenId,
				'entity'    => Entities\Tokens\AccessToken::class,
				'token'     => $this->createToken($accessTokenId, $this->user->getRoles(), $validTill),
				'validTill' => $validTill,
				'status'    => NodeAuthTypes\TokenStatusType::get(NodeAuthTypes\TokenStatusType::STATE_ACTIVE),
				'identity'  => $this->user->getIdentity(),
			]);

			$accessToken = $this->tokensManager->create($values);

			$validTill = $this->getNow()->modify(Entities\Tokens\IRefreshToken::TOKEN_EXPIRATION);

			$refreshTokenId = Uuid\Uuid::uuid4();

			$values = Utils\ArrayHash::from([
				'id'          => $refreshTokenId,
				'entity'      => Entities\Tokens\RefreshToken::class,
				'accessToken' => $accessToken,
				'token'       => $this->createToken($refreshTokenId, [], $validTill),
				'validTill'   => $validTill,
				'status'      => NodeAuthTypes\TokenStatusType::get(NodeAuthTypes\TokenStatusType::STATE_ACTIVE),
			]);

			$this->tokensManager->create($values);

			// Commit all changes into database
			$this->getOrmConnection()->commit();

		} catch (Throwable $ex) {
			// Revert all changes when error occur
			if ($this->getOrmConnection()->isTransactionActive()) {
				$this->getOrmConnection()->rollBack();
			}

			// Log catched exception
			$this->logger->error('[CONTROLLER] ' . $ex->getMessage(), [
				'exception' => [
					'message' => $ex->getMessage(),
					'code'    => $ex->getCode(),
				],
			]);

			throw new NodeJsonApiExceptions\JsonApiErrorException(
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
	 * @throws NodeJsonApiExceptions\IJsonApiException
	 * @throws Doctrine\DBAL\ConnectionException
	 *
	 * @Secured
	 * @Secured\User(guest)
	 */
	public function update(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		$document = $this->createDocument($request);

		$attributes = $document->getResource()->getAttributes();

		if (!$attributes->has('refresh')) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
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
			throw new NodeJsonApiExceptions\JsonApiErrorException(
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

			throw new NodeJsonApiExceptions\JsonApiErrorException(
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

			$validTill = $this->getNow()->modify(Entities\Tokens\IAccessToken::TOKEN_EXPIRATION);

			$accessTokenId = Uuid\Uuid::uuid4();

			$values = Utils\ArrayHash::from([
				'id'        => $accessTokenId,
				'entity'    => Entities\Tokens\AccessToken::class,
				'token'     => $this->createToken($accessTokenId, $this->user->getRoles(), $validTill),
				'validTill' => $validTill,
				'status'    => NodeAuthTypes\TokenStatusType::get(NodeAuthTypes\TokenStatusType::STATE_ACTIVE),
				'identity'  => $this->user->getIdentity(),
			]);

			$newAccessToken = $this->tokensManager->create($values);

			$validTill = $this->getNow()->modify(Entities\Tokens\IRefreshToken::TOKEN_EXPIRATION);

			$refreshTokenId = Uuid\Uuid::uuid4();

			$values = Utils\ArrayHash::from([
				'id'          => $refreshTokenId,
				'entity'      => Entities\Tokens\RefreshToken::class,
				'accessToken' => $newAccessToken,
				'token'       => $this->createToken($refreshTokenId, [], $validTill),
				'validTill'   => $validTill,
				'status'      => NodeAuthTypes\TokenStatusType::get(NodeAuthTypes\TokenStatusType::STATE_ACTIVE),
			]);

			$this->tokensManager->create($values);

			$this->tokensManager->delete($refreshToken);
			$this->tokensManager->delete($accessToken);

			// Commit all changes into database
			$this->getOrmConnection()->commit();

		} catch (Throwable $ex) {
			// Revert all changes when error occur
			if ($this->getOrmConnection()->isTransactionActive()) {
				$this->getOrmConnection()->rollBack();
			}

			// Log catched exception
			$this->logger->error('[CONTROLLER] ' . $ex->getMessage(), [
				'exception' => [
					'message' => $ex->getMessage(),
					'code'    => $ex->getCode(),
				],
			]);

			throw new NodeJsonApiExceptions\JsonApiErrorException(
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
	 * @throws NodeJsonApiExceptions\IJsonApiException
	 * @throws Doctrine\DBAL\ConnectionException
	 *
	 * @Secured
	 * @Secured\User(loggedIn)
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

		} catch (Throwable $ex) {
			// Revert all changes when error occur
			if ($this->getOrmConnection()->isTransactionActive()) {
				$this->getOrmConnection()->rollBack();
			}

			// Log catched exception
			$this->logger->error('[CONTROLLER] ' . $ex->getMessage(), [
				'exception' => [
					'message' => $ex->getMessage(),
					'code'    => $ex->getCode(),
				],
			]);

			throw new NodeJsonApiExceptions\JsonApiErrorException(
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
	 * @throws NodeJsonApiExceptions\IJsonApiException
	 *
	 * @Secured
	 * @Secured\User(loggedIn)
	 */
	public function readRelationship(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		if ($this->user->getAccount() === null) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_FORBIDDEN,
				$this->translator->translate('//node.base.messages.forbidden.heading'),
				$this->translator->translate('//node.base.messages.forbidden.message')
			);
		}

		$relationEntity = strtolower($request->getAttribute(Router\Router::RELATION_ENTITY));

		if ($relationEntity === Schemas\Sessions\SessionSchema::RELATIONSHIPS_ACCOUNT) {
			return $response
				->withEntity(NodeWebServerHttp\ScalarEntity::from($this->user->getAccount()));
		}

		$this->throwUnknownRelation($relationEntity);

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
	 * @param Message\ServerRequestInterface $request
	 *
	 * @return Entities\Tokens\IAccessToken
	 *
	 * @throws NodeJsonApiExceptions\IJsonApiException
	 */
	private function getToken(Message\ServerRequestInterface $request): Entities\Tokens\IAccessToken
	{
		$token = $this->tokenReader->read($request);

		if ($token === null) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_FORBIDDEN,
				$this->translator->translate('//node.base.messages.forbidden.heading'),
				$this->translator->translate('//node.base.messages.forbidden.message')
			);
		}

		$findToken = new NodeAuthQueries\FindTokensQuery();
		$findToken->byToken((string) $token);

		$accessToken = $this->tokenRepository->findOneBy($findToken, Entities\Tokens\AccessToken::class);

		if (
			$this->user->getAccount() !== null
			&& $accessToken instanceof Entities\Tokens\IAccessToken
			&& $accessToken->getIdentity()->getAccount()->getId()->equals($this->user->getAccount()->getId())
		) {
			return $accessToken;
		}

		throw new NodeJsonApiExceptions\JsonApiErrorException(
			StatusCodeInterface::STATUS_FORBIDDEN,
			$this->translator->translate('//node.base.messages.forbidden.heading'),
			$this->translator->translate('//node.base.messages.forbidden.message')
		);
	}

	/**
	 * @param Uuid\UuidInterface $id
	 * @param string[] $roles
	 * @param DateTimeInterface|null $validTill
	 *
	 * @return string
	 *
	 * @throws Throwable
	 */
	private function createToken(
		Uuid\UuidInterface $id,
		array $roles,
		?DateTimeInterface $validTill
	): string {
		return (string) $this->tokenBuilder->build($id->toString(), $roles, $validTill);
	}

}
