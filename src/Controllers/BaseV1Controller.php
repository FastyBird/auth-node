<?php declare(strict_types = 1);

/**
 * BaseV1Controller.php
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

use Contributte\Translation;
use Doctrine\DBAL\Connection;
use FastyBird\AuthNode\Exceptions;
use FastyBird\AuthNode\Router;
use FastyBird\AuthNode\Security;
use FastyBird\DateTimeFactory;
use FastyBird\NodeJsonApi\Exceptions as NodeJsonApiExceptions;
use FastyBird\NodeWebServer\Http as NodeWebServerHttp;
use Fig\Http\Message\RequestMethodInterface;
use Fig\Http\Message\StatusCodeInterface;
use IPub\JsonAPIDocument;
use Nette;
use Nette\Utils;
use Nettrine\ORM;
use Psr\Http\Message;
use Psr\Log\LoggerInterface;

/**
 * API base controller
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Controllers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
abstract class BaseV1Controller
{

	use Nette\SmartObject;

	/** @var Security\User */
	protected $user;

	/** @var DateTimeFactory\DateTimeFactory */
	protected $dateFactory;

	/** @var Translation\PrefixedTranslator */
	protected $translator;

	/** @var ORM\ManagerRegistry */
	protected $managerRegistry;

	/** @var LoggerInterface */
	protected $logger;

	/** @var string */
	protected $translationDomain = '';

	/**
	 * @param Security\User $user
	 *
	 * @return void
	 */
	public function injectUser(Security\User $user): void
	{
		$this->user = $user;
	}

	/**
	 * @param DateTimeFactory\DateTimeFactory $dateFactory
	 *
	 * @return void
	 */
	public function injectDateFactory(DateTimeFactory\DateTimeFactory $dateFactory): void
	{
		$this->dateFactory = $dateFactory;
	}

	/**
	 * @param Translation\Translator $translator
	 *
	 * @return void
	 */
	public function injectTranslator(Translation\Translator $translator): void
	{
		$this->translator = new Translation\PrefixedTranslator($translator, $this->translationDomain);
	}

	/**
	 * @param ORM\ManagerRegistry $managerRegistry
	 *
	 * @return void
	 */
	public function injectManagerRegistry(ORM\ManagerRegistry $managerRegistry): void
	{
		$this->managerRegistry = $managerRegistry;
	}

	/**
	 * @param LoggerInterface $logger
	 *
	 * @return void
	 */
	public function injectLogger(LoggerInterface $logger): void
	{
		$this->logger = $logger;
	}

	/**
	 * @param Message\ServerRequestInterface $request
	 * @param NodeWebServerHttp\Response $response
	 *
	 * @throws NodeJsonApiExceptions\IJsonApiException
	 */
	public function readRelationship(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		// & relation entity name
		$relationEntity = strtolower($request->getAttribute(Router\Router::RELATION_ENTITY));

		if ($relationEntity !== '') {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_NOT_FOUND,
				$this->translator->translate('//node.base.messages.relationNotFound.heading'),
				$this->translator->translate('//node.base.messages.relationNotFound.message', ['relation' => $relationEntity])
			);
		}

		throw new NodeJsonApiExceptions\JsonApiErrorException(
			StatusCodeInterface::STATUS_NOT_FOUND,
			$this->translator->translate('//node.base.messages.unknownRelation.heading'),
			$this->translator->translate('//node.base.messages.unknownRelation.message')
		);
	}

	/**
	 * @param Message\ServerRequestInterface $request
	 *
	 * @return JsonAPIDocument\IDocument<JsonAPIDocument\Objects\StandardObject>
	 *
	 * @throws NodeJsonApiExceptions\IJsonApiException
	 */
	protected function createDocument(Message\ServerRequestInterface $request): JsonAPIDocument\IDocument
	{
		try {
			$document = new JsonAPIDocument\Document(Utils\Json::decode($request->getBody()->getContents()));

		} catch (Utils\JsonException $ex) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_BAD_REQUEST,
				$this->translator->translate('//node.base.messages.notValidJson.heading'),
				$this->translator->translate('//node.base.messages.notValidJson.message')
			);

		} catch (JsonAPIDocument\Exceptions\RuntimeException $ex) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_BAD_REQUEST,
				$this->translator->translate('//node.base.messages.notValidJsonApi.heading'),
				$this->translator->translate('//node.base.messages.notValidJsonApi.message')
			);
		}

		return $document;
	}

	/**
	 * @param Message\ServerRequestInterface $request
	 * @param JsonAPIDocument\IDocument<JsonAPIDocument\Objects\StandardObject> $document
	 *
	 * @return bool
	 *
	 * @throws NodeJsonApiExceptions\JsonApiErrorException
	 */
	protected function validateIdentifier(
		Message\ServerRequestInterface $request,
		JsonAPIDocument\IDocument $document
	): bool {
		if (
			in_array(strtoupper($request->getMethod()), [RequestMethodInterface::METHOD_POST, RequestMethodInterface::METHOD_PATCH], true)
			&& $request->getAttribute(Router\Router::URL_ITEM_ID, null) !== null
			&& $request->getAttribute(Router\Router::URL_ITEM_ID) !== $document->getResource()->getIdentifier()->getId()
		) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_BAD_REQUEST,
				$this->translator->translate('//node.base.messages.invalidIdentifier.heading'),
				$this->translator->translate('//node.base.messages.invalidIdentifier.message')
			);
		}

		return true;
	}

	/**
	 * @return Connection
	 */
	protected function getOrmConnection(): Connection
	{
		$connection = $this->managerRegistry->getConnection();

		if ($connection instanceof Connection) {
			return $connection;
		}

		throw new Exceptions\RuntimeException('Entity manager could not be loaded');
	}

}
