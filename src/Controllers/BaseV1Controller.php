<?php declare(strict_types = 1);

/**
 * BaseV1Controller.php
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

use Contributte\Translation;
use Doctrine\DBAL\Connection;
use FastyBird\AuthNode\Exceptions;
use FastyBird\AuthNode\Security;
use FastyBird\NodeJsonApi\Exceptions as NodeJsonApiExceptions;
use FastyBird\NodeLibs\Helpers as NodeLibsHelpers;
use Fig\Http\Message\StatusCodeInterface;
use IPub\JsonAPIDocument;
use Nette;
use Nette\Utils;
use Nettrine\ORM;
use Psr\Http\Message\ServerRequestInterface;
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

	/** @var NodeLibsHelpers\IDateFactory */
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
	 * @param NodeLibsHelpers\IDateFactory $dateFactory
	 *
	 * @return void
	 */
	public function injectDateFactory(NodeLibsHelpers\IDateFactory $dateFactory): void
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
	 * @param ServerRequestInterface $request
	 *
	 * @return JsonAPIDocument\IDocument<JsonAPIDocument\Objects\StandardObject>
	 *
	 * @throws NodeJsonApiExceptions\IJsonApiException
	 */
	protected function createDocument(ServerRequestInterface $request): JsonAPIDocument\IDocument
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
	 * @param string|null $relationEntity
	 *
	 * @throws NodeJsonApiExceptions\IJsonApiException
	 */
	protected function throwUnknownRelation(?string $relationEntity): void
	{
		if ($relationEntity !== null) {
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
