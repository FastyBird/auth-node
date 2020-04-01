<?php declare(strict_types = 1);

/**
 * AccountV1Controller.php
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
use FastyBird\AccountsNode\Hydrators;
use FastyBird\AccountsNode\Models;
use FastyBird\AccountsNode\Router;
use FastyBird\AccountsNode\Schemas;
use FastyBird\NodeWebServer\Exceptions as NodeWebServerExceptions;
use FastyBird\NodeWebServer\Http as NodeWebServerHttp;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message;
use Throwable;

/**
 * Account controller
 *
 * @package        FastyBird:AccountsNode!
 * @subpackage     Controllers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class AccountV1Controller extends BaseV1Controller
{

	/** @var Hydrators\AccountHydrator */
	private $accountHydrator;

	/** @var Models\Accounts\IAccountsManager */
	private $accountsManager;

	/** @var string */
	protected $translationDomain = 'node.account';

	public function __construct(
		Hydrators\AccountHydrator $accountHydrator,
		Models\Accounts\IAccountsManager $accountsManager
	) {
		$this->accountHydrator = $accountHydrator;

		$this->accountsManager = $accountsManager;
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
		if ($this->user->getAccount() === null) {
			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_FORBIDDEN,
				$this->translator->translate('//node.base.messages.forbidden.heading'),
				$this->translator->translate('//node.base.messages.forbidden.message')
			);
		}

		return $response
			->withEntity(NodeWebServerHttp\ScalarEntity::from($this->user->getAccount()));
	}

	/**
	 * @param Message\ServerRequestInterface $request
	 * @param NodeWebServerHttp\Response $response
	 *
	 * @return NodeWebServerHttp\Response
	 */
	public function create(
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
	 * @throws NodeWebServerExceptions\IJsonApiException
	 * @throws Doctrine\DBAL\ConnectionException
	 */
	public function update(
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

		try {
			// Start transaction connection to the database
			$this->getOrmConnection()->beginTransaction();

			$account = $this->accountsManager->update(
				$this->user->getAccount(),
				$this->accountHydrator->hydrate($document->getResource(), $this->user->getAccount())
			);

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
				$this->translator->translate('messages.notUpdated.heading'),
				$this->translator->translate('messages.notUpdated.message')
			);
		}

		/** @var NodeWebServerHttp\Response $response */
		$response = $response
			->withEntity(NodeWebServerHttp\ScalarEntity::from($account));

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
	public function delete(
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

		// TODO: Closing account not implemented yet

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

		if ($relationEntity === Schemas\AccountSchema::RELATIONSHIPS_EMAILS) {
			return $response
				->withEntity(NodeWebServerHttp\ScalarEntity::from($this->user->getAccount()->getEmails()));

		} elseif ($relationEntity === Schemas\AccountSchema::RELATIONSHIPS_QUESTION) {
			if ($this->user->getAccount()->hasSecurityQuestion()) {
				return $response
					->withEntity(NodeWebServerHttp\ScalarEntity::from($this->user->getAccount()->getSecurityQuestion()));
			}

		} elseif ($relationEntity === Schemas\AccountSchema::RELATIONSHIPS_ROLES) {
			return $response
				->withEntity(NodeWebServerHttp\ScalarEntity::from($this->user->getAccount()->getRoles()));
		}

		$this->throwUnknownRelation($relationEntity);

		return $response;
	}

}
