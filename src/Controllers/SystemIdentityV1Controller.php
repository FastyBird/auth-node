<?php declare(strict_types = 1);

/**
 * SystemIdentityV1Controller.php
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

use Doctrine;
use FastyBird\AccountsNode\Entities;
use FastyBird\AccountsNode\Helpers;
use FastyBird\AccountsNode\Models;
use FastyBird\NodeWebServer\Exceptions as NodeWebServerExceptions;
use FastyBird\NodeWebServer\Http as NodeWebServerHttp;
use Fig\Http\Message\StatusCodeInterface;
use Nette\Utils;
use Psr\Http\Message;
use Throwable;

/**
 * System identity controller
 *
 * @package        FastyBird:AccountsNode!
 * @subpackage     Controllers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class SystemIdentityV1Controller extends BaseV1Controller
{

	/** @var Models\Identities\IIdentityRepository */
	private $identityRepository;

	/** @var Models\Identities\IIdentitiesManager */
	private $identitiesManager;

	/** @var Models\Accounts\IAccountsManager */
	private $accountsManager;

	/** @var Helpers\SecurityHash */
	private $securityHash;

	/** @var string */
	protected $translationDomain = 'node.systemIdentity';

	public function __construct(
		Models\Identities\IIdentityRepository $identityRepository,
		Models\Identities\IIdentitiesManager $identitiesManager,
		Models\Accounts\IAccountsManager $accountsManager,
		Helpers\SecurityHash $securityHash
	) {
		$this->identityRepository = $identityRepository;
		$this->identitiesManager = $identitiesManager;
		$this->accountsManager = $accountsManager;

		$this->securityHash = $securityHash;
	}

	/**
	 * @param Message\ServerRequestInterface $request
	 * @param NodeWebServerHttp\Response $response
	 *
	 * @return NodeWebServerHttp\Response
	 *
	 * @throws NodeWebServerExceptions\JsonApiErrorException
	 * @throws Doctrine\DBAL\ConnectionException
	 */
	public function update(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		$document = $this->createDocument($request);

		if ($this->user->getAccount() === null) {
			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_FORBIDDEN,
				$this->translator->translate('//node.base.messages.forbidden.heading'),
				$this->translator->translate('//node.base.messages.forbidden.message')
			);
		}

		$attributes = $document->getResource()->getAttributes();

		/** @var Entities\Identities\System|null $identity */
		$identity = $this->identityRepository->findOneForAccount($this->user->getAccount(), Entities\Identities\System::class);

		if ($identity === null) {
			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_NOT_FOUND,
				$this->translator->translate('messages.notFound.heading'),
				$this->translator->translate('messages.notFound.message')
			);
		}

		if (
			!$attributes->has('password')
			|| !$attributes->get('password')->has('current')
			|| !$identity->verifyPassword($attributes->toArray()['password']['current'])
		) {
			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('messages.invalidOld.heading'),
				$this->translator->translate('messages.invalidOld.message'),
				[
					'pointer' => '/data/attributes/password/current',
				]
			);
		}

		if (
			!$attributes->has('password')
			|| !$attributes->get('password')->has('new')
		) {
			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('//userProfile.api.base.messages.missingMandatory.heading'),
				$this->translator->translate('//userProfile.api.base.messages.missingMandatory.message'),
				[
					'pointer' => '/data/attributes/password/new',
				]
			);
		}

		try {
			// Start transaction connection to the database
			$this->getOrmConnection()->beginTransaction();

			$update = new Utils\ArrayHash();
			$update->offsetSet('password', $attributes->toArray()['password']['new']);

			// Update item in database
			$this->identitiesManager->update($identity, $update);

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
				$this->translator->translate('messages.notUpdated.heading'),
				$this->translator->translate('messages.notUpdated.message')
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
	 * @throws NodeWebServerExceptions\JsonApiErrorException
	 * @throws Doctrine\DBAL\ConnectionException
	 */
	public function requestPassword(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		$document = $this->createDocument($request);

		$attributes = $document->getResource()->getAttributes()->toArray();

		$identity = $this->findIdentity($attributes['credentials']['uid']);

		if ($identity === null) {
			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_NOT_FOUND,
				$this->translator->translate('messages.notFound.heading'),
				$this->translator->translate('messages.notFound.message'),
				[
					'pointer' => '/data/attributes/credentials/uid',
				]
			);
		}

		$account = $identity->getAccount();

		if ($account->isDeleted()) {
			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_NOT_FOUND,
				$this->translator->translate('messages.notFound.heading'),
				$this->translator->translate('messages.notFound.message'),
				[
					'pointer' => '/data/attributes/credentials/uid',
				]
			);

		} elseif ($account->isNotActivated()) {
			$hash = $account->getRequestHash();

			if ($hash === null || !$this->securityHash->isValid($hash)) {
				// Verification hash is expired, create new one for user
				$this->accountsManager->update($account, Utils\ArrayHash::from([
					'requestHash' => $this->securityHash->getRecoveryKey(),
				]));
			}

			// TODO: Send new user email

			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('messages.notActivated.heading'),
				$this->translator->translate('messages.notActivated.message'),
				[
					'pointer' => '/data/attributes/credentials/uid',
				]
			);

		} elseif ($account->isBlocked()) {
			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('messages.blocked.heading'),
				$this->translator->translate('messages.blocked.message'),
				[
					'pointer' => '/data/attributes/credentials/uid',
				]
			);
		}

		try {
			// Start transaction connection to the database
			$this->getOrmConnection()->beginTransaction();

			// Update entity
			$this->accountsManager->update($account, Utils\ArrayHash::from([
				'requestHash' => $this->securityHash->getRecoveryKey(),
			]));

			// TODO: Send reset password email

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
				$this->translator->translate('messages.requestNotSent.heading'),
				$this->translator->translate('messages.requestNotSent.message'),
				[
					'pointer' => '/data/attributes/credentials/uid',
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
	 * @throws NodeWebServerExceptions\JsonApiErrorException
	 * @throws Doctrine\DBAL\ConnectionException
	 */
	public function validate(
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

		$document = $this->createDocument($request);

		$attributes = $document->getResource()->getAttributes();

		/** @var Entities\Identities\System|null $identity */
		$identity = $this->identityRepository->findOneForAccount($this->user->getAccount(), Entities\Identities\System::class);

		if ($identity === null) {
			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_NOT_FOUND,
				$this->translator->translate('messages.notFound.heading'),
				$this->translator->translate('messages.notFound.message')
			);
		}

		if (
			!$attributes->has('password')
			|| !$attributes->get('password')->has('current')
			|| !$identity->verifyPassword($attributes->toArray()['password']['current'])
		) {
			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('messages.invalidOld.heading'),
				$this->translator->translate('messages.invalidOld.message'),
				[
					'pointer' => '/data/attributes/password/current',
				]
			);
		}

		/** @var NodeWebServerHttp\Response $response */
		$response = $response
			->withStatus(StatusCodeInterface::STATUS_NO_CONTENT);

		return $response;
	}

	/**
	 * @param string $uid
	 *
	 * @return Entities\Identities\System|null
	 */
	private function findIdentity(string $uid): ?Entities\Identities\System
	{
		/** @var Entities\Identities\System|null $identity */
		$identity = $this->identityRepository->findOneByUid($uid, Entities\Identities\System::class);

		if ($identity === null) {
			/** @var Entities\Identities\System|null $identity */
			$identity = $this->identityRepository->findOneByEmail($uid, Entities\Identities\System::class);
		}

		return $identity;
	}

}
