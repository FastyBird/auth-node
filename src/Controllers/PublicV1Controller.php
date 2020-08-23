<?php declare(strict_types = 1);

/**
 * PublicV1Controller.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Controllers
 * @since          0.1.0
 *
 * @date           23.08.20
 */

namespace FastyBird\AuthNode\Controllers;

use Doctrine;
use FastyBird\AuthNode\Controllers;
use FastyBird\AuthNode\Entities;
use FastyBird\AuthNode\Helpers;
use FastyBird\AuthNode\Models;
use FastyBird\AuthNode\Queries;
use FastyBird\AuthNode\Schemas;
use FastyBird\NodeJsonApi\Exceptions as NodeJsonApiExceptions;
use FastyBird\NodeWebServer\Http as NodeWebServerHttp;
use Fig\Http\Message\StatusCodeInterface;
use Nette\Utils;
use Psr\Http\Message;
use Throwable;

/**
 * Account identity controller
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Controllers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class PublicV1Controller extends BaseV1Controller
{

	use Controllers\Finders\TIdentityFinder;

	/** @var Models\Accounts\IAccountsManager */
	private $accountsManager;

	/** @var Helpers\SecurityHash */
	private $securityHash;

	/** @var Models\Identities\IIdentityRepository */
	protected $identityRepository;

	/** @var string */
	protected $translationDomain = 'node.public';

	public function __construct(
		Models\Identities\IIdentityRepository $identityRepository,
		Models\Accounts\IAccountsManager $accountsManager,
		Helpers\SecurityHash $securityHash
	) {
		$this->identityRepository = $identityRepository;
		$this->accountsManager = $accountsManager;

		$this->securityHash = $securityHash;
	}

	/**
	 * @param Message\ServerRequestInterface $request
	 * @param NodeWebServerHttp\Response $response
	 *
	 * @return NodeWebServerHttp\Response
	 *
	 * @Secured
	 * @Secured\User(guest)
	 */
	public function register(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		// TODO: Registration not implemented yet

		/** @var NodeWebServerHttp\Response $response */
		$response = $response
			->withStatus(StatusCodeInterface::STATUS_ACCEPTED);

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
	public function resetIdentity(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		$document = $this->createDocument($request);

		$attributes = $document->getResource()->getAttributes();

		if ($document->getResource()->getType() !== Schemas\Identities\UserAccountIdentitySchema::SCHEMA_TYPE) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('//node.base.messages.invalidType.heading'),
				$this->translator->translate('//node.base.messages.invalidType.message'),
				[
					'pointer' => '/data/type',
				]
			);
		}

		if (!$attributes->has('uid')) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('//node.base.messages.missingAttribute.heading'),
				$this->translator->translate('//node.base.messages.missingAttribute.message'),
				[
					'pointer' => '/data/attributes/uid',
				]
			);
		}

		$findQuery = new Queries\FindIdentitiesQuery();
		$findQuery->byUid($attributes->get('uid'));

		$identity = $this->identityRepository->findOneBy($findQuery);

		if ($identity === null) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_NOT_FOUND,
				$this->translator->translate('//node.base.messages.notFound.heading'),
				$this->translator->translate('//node.base.messages.notFound.message')
			);
		}

		$account = $identity->getAccount();

		if (!$account instanceof Entities\Accounts\IUserAccount) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_NOT_FOUND,
				$this->translator->translate('//node.base.messages.notFound.heading'),
				$this->translator->translate('//node.base.messages.notFound.message')
			);
		}

		if ($account->isDeleted()) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_NOT_FOUND,
				$this->translator->translate('//node.base.messages.notFound.heading'),
				$this->translator->translate('//node.base.messages.notFound.message')
			);

		} elseif ($account->isNotActivated()) {
			$hash = $account->getRequestHash();

			if ($hash === null || !$this->securityHash->isValid($hash)) {
				// Verification hash is expired, create new one for user
				$this->accountsManager->update($account, Utils\ArrayHash::from([
					'requestHash' => $this->securityHash->createKey(),
				]));
			}

			// TODO: Send new user email

			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('messages.notActivated.heading'),
				$this->translator->translate('messages.notActivated.message'),
				[
					'pointer' => '/data/attributes/uid',
				]
			);

		} elseif ($account->isBlocked()) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('messages.blocked.heading'),
				$this->translator->translate('messages.blocked.message'),
				[
					'pointer' => '/data/attributes/uid',
				]
			);
		}

		try {
			// Start transaction connection to the database
			$this->getOrmConnection()->beginTransaction();

			// Update entity
			$this->accountsManager->update($account, Utils\ArrayHash::from([
				'requestHash' => $this->securityHash->createKey(),
			]));

			// TODO: Send reset password email

			// Commit all changes into database
			$this->getOrmConnection()->commit();

		} catch (NodeJsonApiExceptions\IJsonApiException $ex) {
			throw $ex;

		} catch (Throwable $ex) {
			// Log catched exception
			$this->logger->error('[CONTROLLER] ' . $ex->getMessage(), [
				'exception' => [
					'message' => $ex->getMessage(),
					'code'    => $ex->getCode(),
				],
			]);

			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('messages.requestNotSent.heading'),
				$this->translator->translate('messages.requestNotSent.message'),
				[
					'pointer' => '/data/attributes/uid',
				]
			);

		} finally {
			// Revert all changes when error occur
			if ($this->getOrmConnection()->isTransactionActive()) {
				$this->getOrmConnection()->rollBack();
			}
		}

		/** @var NodeWebServerHttp\Response $response */
		$response = $response
			->withStatus(StatusCodeInterface::STATUS_NO_CONTENT);

		return $response;
	}

}
