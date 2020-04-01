<?php declare(strict_types = 1);

/**
 * SecurityQuestionV1Controller.php
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
 * Account security question controller
 *
 * @package        FastyBird:AccountsNode!
 * @subpackage     Controllers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class SecurityQuestionV1Controller extends BaseV1Controller
{

	/** @var Hydrators\SecurityQuestionHydrator */
	private $questionHydrator;

	/** @var Models\SecurityQuestions\IQuestionsManager */
	private $questionsManager;

	/** @var string */
	protected $translationDomain = 'node.securityQuestion';

	public function __construct(
		Hydrators\SecurityQuestionHydrator $questionHydrator,
		Models\SecurityQuestions\IQuestionsManager $questionsManager
	) {
		$this->questionHydrator = $questionHydrator;

		$this->questionsManager = $questionsManager;
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

		$account = $this->user->getAccount();

		if ($account->hasSecurityQuestion()) {
			return $response
				->withEntity(NodeWebServerHttp\ScalarEntity::from($account->getSecurityQuestion()));

		} else {
			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_NOT_FOUND,
				$this->translator->translate('messages.notFound.heading'),
				$this->translator->translate('messages.notFound.message')
			);
		}
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
		if ($this->user->getAccount() === null) {
			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_FORBIDDEN,
				$this->translator->translate('//node.base.messages.forbidden.heading'),
				$this->translator->translate('//node.base.messages.forbidden.message')
			);
		}

		if ($this->user->getAccount()->hasSecurityQuestion()) {
			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_BAD_REQUEST,
				$this->translator->translate('messages.accountHasQuestion.heading'),
				$this->translator->translate('messages.accountHasQuestion.message')
			);
		}

		$document = $this->createDocument($request);

		try {
			// Start transaction connection to the database
			$this->getOrmConnection()->beginTransaction();

			// Create new item in database
			$question = $this->questionsManager->create($this->questionHydrator->hydrate($document->getResource()));

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
				$this->translator->translate('messages.notCreated.heading'),
				$this->translator->translate('messages.notCreated.message')
			);
		}

		/** @var NodeWebServerHttp\Response $response */
		$response = $response
			->withEntity(NodeWebServerHttp\ScalarEntity::from($question))
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
		if ($this->user->getAccount() === null) {
			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_FORBIDDEN,
				$this->translator->translate('//node.base.messages.forbidden.heading'),
				$this->translator->translate('//node.base.messages.forbidden.message')
			);
		}

		$document = $this->createDocument($request);

		if ($request->getAttribute(Router\Router::URL_ITEM_ID) !== $document->getResource()->getIdentifier()->getId()) {
			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_BAD_REQUEST,
				$this->translator->translate('//node.base.messages.invalid.heading'),
				$this->translator->translate('//node.base.messages.invalid.message')
			);
		}

		$question = $this->user->getAccount()->getSecurityQuestion();

		if ($question === null) {
			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_NOT_FOUND,
				$this->translator->translate('messages.notFound.heading'),
				$this->translator->translate('messages.notFound.message')
			);
		}

		try {
			// Start transaction connection to the database
			$this->getOrmConnection()->beginTransaction();

			if ($document->getResource()->getType() === Schemas\SecurityQuestionSchema::SCHEMA_TYPE) {
				$updateQuestionData = $this->questionHydrator->hydrate($document->getResource(), $question);

			} else {
				throw new NodeWebServerExceptions\JsonApiErrorException(
					StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
					$this->translator->translate('messages.invalidType.heading'),
					$this->translator->translate('messages.invalidType.message'),
					[
						'pointer' => '/data/type',
					]
				);
			}

			$question = $this->questionsManager->update($question, $updateQuestionData);

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
			->withEntity(NodeWebServerHttp\ScalarEntity::from($question));

		return $response;
	}

	/**
	 * @param Message\ServerRequestInterface $request
	 * @param NodeWebServerHttp\Response $response
	 *
	 * @return NodeWebServerHttp\Response
	 *
	 * @throws NodeWebServerExceptions\JsonApiErrorException
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

		if ($relationEntity === Schemas\SecurityQuestionSchema::RELATIONSHIPS_ACCOUNT) {
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
		if ($this->user->getAccount() === null) {
			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_FORBIDDEN,
				$this->translator->translate('//node.base.messages.forbidden.heading'),
				$this->translator->translate('//node.base.messages.forbidden.message')
			);
		}

		if ($this->user->getAccount()->getSecurityQuestion() === null) {
			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_NOT_FOUND,
				$this->translator->translate('messages.notFound.heading'),
				$this->translator->translate('messages.notFound.message')
			);
		}

		$document = $this->createDocument($request);

		$attributes = $document->getResource()->getAttributes()->toArray();

		if (
			!isset($attributes['current_answer'])
			|| $this->user->getAccount()->getSecurityQuestion()->getAnswer() !== $attributes['current_answer']
		) {
			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('messages.incorrect.heading'),
				$this->translator->translate('messages.incorrect.message'),
				[
					'pointer' => '/data/attributes/current_answer',
				]
			);
		}

		/** @var NodeWebServerHttp\Response $response */
		$response = $response
			->withStatus(StatusCodeInterface::STATUS_NO_CONTENT);

		return $response;
	}

}
