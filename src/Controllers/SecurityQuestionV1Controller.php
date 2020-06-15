<?php declare(strict_types = 1);

/**
 * SecurityQuestionV1Controller.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Controllers
 * @since          0.1.0
 *
 * @date           31.03.20
 */

namespace FastyBird\AuthNode\Controllers;

use Doctrine;
use FastyBird\AuthNode\Entities;
use FastyBird\AuthNode\Hydrators;
use FastyBird\AuthNode\Models;
use FastyBird\AuthNode\Router;
use FastyBird\AuthNode\Schemas;
use FastyBird\NodeJsonApi\Exceptions as NodeJsonApiExceptions;
use FastyBird\NodeWebServer\Http as NodeWebServerHttp;
use Fig\Http\Message\StatusCodeInterface;
use IPub\DoctrineCrud\Exceptions as DoctrineCrudExceptions;
use Psr\Http\Message;
use Throwable;

/**
 * Account security question controller
 *
 * @package        FastyBird:AuthNode!
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
	 * @throws NodeJsonApiExceptions\IJsonApiException
	 */
	public function read(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		if (
			$this->user->getAccount() === null
			|| !$this->user->getAccount() instanceof Entities\Accounts\IUserAccount
			|| $this->user->getAccount()->getPlainId() !== $request->getAttribute(Router\Router::URL_ACCOUNT_ID)
		) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
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
			throw new NodeJsonApiExceptions\JsonApiErrorException(
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
	 * @throws NodeJsonApiExceptions\IJsonApiException
	 * @throws Doctrine\DBAL\ConnectionException
	 */
	public function create(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		if (
			$this->user->getAccount() === null
			|| !$this->user->getAccount() instanceof Entities\Accounts\IUserAccount
			|| $this->user->getAccount()->getPlainId() !== $request->getAttribute(Router\Router::URL_ACCOUNT_ID)
		) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_FORBIDDEN,
				$this->translator->translate('//node.base.messages.forbidden.heading'),
				$this->translator->translate('//node.base.messages.forbidden.message')
			);
		}

		if ($this->user->getAccount()->hasSecurityQuestion()) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_BAD_REQUEST,
				$this->translator->translate('messages.accountHasQuestion.heading'),
				$this->translator->translate('messages.accountHasQuestion.message')
			);
		}

		$document = $this->createDocument($request);

		try {
			// Start transaction connection to the database
			$this->getOrmConnection()->beginTransaction();

			if ($document->getResource()->getType() === Schemas\SecurityQuestionSchema::SCHEMA_TYPE) {
				$createData = $this->questionHydrator->hydrate($document);
				$createData->offsetSet('account', $this->user->getAccount());

				// Create new item in database
				$question = $this->questionsManager->create($createData);

			} else {
				throw new NodeJsonApiExceptions\JsonApiErrorException(
					StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
					$this->translator->translate('messages.invalidType.heading'),
					$this->translator->translate('messages.invalidType.message'),
					[
						'pointer' => '/data/type',
					]
				);
			}

			// Commit all changes into database
			$this->getOrmConnection()->commit();

		} catch (DoctrineCrudExceptions\EntityCreationException $ex) {
			// Revert all changes when error occur
			$this->getOrmConnection()->rollBack();

			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('//node.base.messages.missingRequired.heading'),
				$this->translator->translate('//node.base.messages.missingRequired.message'),
				[
					'pointer' => 'data/attributes/' . $ex->getField(),
				]
			);

		} catch (NodeJsonApiExceptions\IJsonApiException $ex) {
			// Revert all changes when error occur
			$this->getOrmConnection()->rollBack();

			throw $ex;

		} catch (Throwable $ex) {
			// Revert all changes when error occur
			$this->getOrmConnection()->rollBack();

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
	 * @throws NodeJsonApiExceptions\IJsonApiException
	 * @throws Doctrine\DBAL\ConnectionException
	 */
	public function update(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		if (
			$this->user->getAccount() === null
			|| !$this->user->getAccount() instanceof Entities\Accounts\IUserAccount
			|| $this->user->getAccount()->getPlainId() !== $request->getAttribute(Router\Router::URL_ACCOUNT_ID)
		) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_FORBIDDEN,
				$this->translator->translate('//node.base.messages.forbidden.heading'),
				$this->translator->translate('//node.base.messages.forbidden.message')
			);
		}

		$question = $this->user->getAccount()->getSecurityQuestion();

		if ($question === null) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_NOT_FOUND,
				$this->translator->translate('messages.notFound.heading'),
				$this->translator->translate('messages.notFound.message')
			);
		}

		$document = $this->createDocument($request);

		$attributes = $document->getResource()->getAttributes();

		if ($document->getResource()->getIdentifier()->getId() !== $question->getPlainId()) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_BAD_REQUEST,
				$this->translator->translate('//node.base.messages.identifierInvalid.heading'),
				$this->translator->translate('//node.base.messages.identifierInvalid.message')
			);
		}

		if (!$attributes->has('question')) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('//node.base.messages.missingRequired.heading'),
				$this->translator->translate('//node.base.messages.missingRequired.message'),
				[
					'pointer' => '/data/attributes/question',
				]
			);
		}

		if (!$attributes->has('answer')) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('//node.base.messages.missingRequired.heading'),
				$this->translator->translate('//node.base.messages.missingRequired.message'),
				[
					'pointer' => '/data/attributes/answer',
				]
			);
		}

		try {
			// Start transaction connection to the database
			$this->getOrmConnection()->beginTransaction();

			if ($document->getResource()->getType() === Schemas\SecurityQuestionSchema::SCHEMA_TYPE) {
				$updateQuestionData = $this->questionHydrator->hydrate($document, $question);

			} else {
				throw new NodeJsonApiExceptions\JsonApiErrorException(
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

		} catch (NodeJsonApiExceptions\IJsonApiException $ex) {
			// Revert all changes when error occur
			$this->getOrmConnection()->rollBack();

			throw $ex;

		} catch (Throwable $ex) {
			// Revert all changes when error occur
			$this->getOrmConnection()->rollBack();

			// Log catched exception
			$this->logger->error('[CONTROLLER] ' . $ex->getMessage(), [
				'exception' => [
					'message' => $ex->getMessage(),
					'code'    => $ex->getCode(),
				],
			]);

			throw new NodeJsonApiExceptions\JsonApiErrorException(
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
	 * @throws NodeJsonApiExceptions\IJsonApiException
	 */
	public function readRelationship(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		if (
			$this->user->getAccount() === null
			|| !$this->user->getAccount() instanceof Entities\Accounts\IUserAccount
			|| $this->user->getAccount()->getPlainId() !== $request->getAttribute(Router\Router::URL_ACCOUNT_ID)
		) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_FORBIDDEN,
				$this->translator->translate('//node.base.messages.forbidden.heading'),
				$this->translator->translate('//node.base.messages.forbidden.message')
			);
		}

		if (!$this->user->getAccount()->hasSecurityQuestion()) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_NOT_FOUND,
				$this->translator->translate('messages.notFound.heading'),
				$this->translator->translate('messages.notFound.message')
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
	 * @throws NodeJsonApiExceptions\IJsonApiException
	 */
	public function validate(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		if (
			$this->user->getAccount() === null
			|| !$this->user->getAccount() instanceof Entities\Accounts\IUserAccount
			|| $this->user->getAccount()->getPlainId() !== $request->getAttribute(Router\Router::URL_ACCOUNT_ID)
		) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_FORBIDDEN,
				$this->translator->translate('//node.base.messages.forbidden.heading'),
				$this->translator->translate('//node.base.messages.forbidden.message')
			);
		}

		$question = $this->user->getAccount()->getSecurityQuestion();

		if ($question === null) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_NOT_FOUND,
				$this->translator->translate('messages.notFound.heading'),
				$this->translator->translate('messages.notFound.message')
			);
		}

		$document = $this->createDocument($request);

		$attributes = $document->getResource()->getAttributes();

		if ($document->getResource()->getIdentifier()->getId() !== $question->getPlainId()) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_BAD_REQUEST,
				$this->translator->translate('//node.base.messages.identifierInvalid.heading'),
				$this->translator->translate('//node.base.messages.identifierInvalid.message')
			);
		}

		if ($document->getResource()->getType() !== Schemas\SecurityQuestionSchema::SCHEMA_TYPE) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('messages.invalidType.heading'),
				$this->translator->translate('messages.invalidType.message'),
				[
					'pointer' => '/data/type',
				]
			);
		}

		if (!$attributes->has('current_answer')) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('//node.base.messages.missingRequired.heading'),
				$this->translator->translate('//node.base.messages.missingRequired.message'),
				[
					'pointer' => '/data/attributes/current_answer',
				]
			);
		}

		if ($question->getAnswer() !== $attributes->get('current_answer')) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
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
