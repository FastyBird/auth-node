<?php declare(strict_types = 1);

/**
 * AccountIdentitiesV1Controller.php
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

use Doctrine;
use FastyBird\AuthNode\Controllers;
use FastyBird\AuthNode\Entities;
use FastyBird\AuthNode\Models;
use FastyBird\AuthNode\Queries;
use FastyBird\AuthNode\Router;
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
final class AccountIdentitiesV1Controller extends BaseV1Controller
{

	use Controllers\Finders\TIdentityFinder;

	/** @var Models\Identities\IIdentitiesManager */
	private $identitiesManager;

	/** @var Models\Identities\IIdentityRepository */
	protected $identityRepository;

	/** @var string */
	protected $translationDomain = 'node.identities';

	public function __construct(
		Models\Identities\IIdentityRepository $identityRepository,
		Models\Identities\IIdentitiesManager $identitiesManager
	) {
		$this->identityRepository = $identityRepository;
		$this->identitiesManager = $identitiesManager;
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
	public function index(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		$findQuery = new Queries\FindIdentitiesQuery();
		$findQuery->forAccount($this->findAccount());

		$identities = $this->identityRepository->getResultSet($findQuery);

		return $response
			->withEntity(NodeWebServerHttp\ScalarEntity::from($identities));
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
		// Find identity
		$identity = $this->findIdentity($request, $this->findAccount());

		return $response
			->withEntity(NodeWebServerHttp\ScalarEntity::from($identity));
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
	public function update(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		$document = $this->createDocument($request);

		$identity = $this->findIdentity($request, $this->findAccount());

		$this->validateIdentifier($request, $document);

		try {
			// Start transaction connection to the database
			$this->getOrmConnection()->beginTransaction();

			if (
				$document->getResource()->getType() === Schemas\Identities\UserAccountIdentitySchema::SCHEMA_TYPE
				&& $identity instanceof Entities\Identities\IUserAccountIdentity
			) {
				$attributes = $document->getResource()->getAttributes();

				if (
					!$attributes->has('password')
					|| !$attributes->get('password')->has('current')
				) {
					throw new NodeJsonApiExceptions\JsonApiErrorException(
						StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
						$this->translator->translate('//node.base.messages.missingAttribute.heading'),
						$this->translator->translate('//node.base.messages.missingAttribute.message'),
						[
							'pointer' => '/data/attributes/password/current',
						]
					);
				}

				if (
					!$attributes->has('password')
					|| !$attributes->get('password')->has('new')
				) {
					throw new NodeJsonApiExceptions\JsonApiErrorException(
						StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
						$this->translator->translate('//node.base.messages.missingAttribute.heading'),
						$this->translator->translate('//node.base.messages.missingAttribute.message'),
						[
							'pointer' => '/data/attributes/password/new',
						]
					);
				}

				if (!$identity->verifyPassword((string) $attributes->get('password')->get('current'))) {
					throw new NodeJsonApiExceptions\JsonApiErrorException(
						StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
						$this->translator->translate('//node.base.messages.invalidAttribute.heading'),
						$this->translator->translate('//node.base.messages.invalidAttribute.message'),
						[
							'pointer' => '/data/attributes/password/current',
						]
					);
				}

				$update = new Utils\ArrayHash();
				$update->offsetSet('password', (string) $attributes->get('password')->get('new'));

				// Update item in database
				$this->identitiesManager->update($identity, $update);

			} else {
				throw new NodeJsonApiExceptions\JsonApiErrorException(
					StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
					$this->translator->translate('//node.base.messages.invalidType.heading'),
					$this->translator->translate('//node.base.messages.invalidType.message'),
					[
						'pointer' => '/data/type',
					]
				);
			}

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
				$this->translator->translate('//node.base.messages.notUpdated.heading'),
				$this->translator->translate('//node.base.messages.notUpdated.message')
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
		$identity = $this->findIdentity($request, $this->findAccount());

		// & relation entity name
		$relationEntity = strtolower($request->getAttribute(Router\Router::RELATION_ENTITY));

		if ($relationEntity === Schemas\Identities\IdentitySchema::RELATIONSHIPS_ACCOUNT) {
			return $response
				->withEntity(NodeWebServerHttp\ScalarEntity::from($identity->getAccount()));
		}

		return parent::readRelationship($request, $response);
	}

	/**
	 * @return Entities\Accounts\IAccount
	 *
	 * @throws NodeJsonApiExceptions\JsonApiErrorException
	 */
	private function findAccount(): Entities\Accounts\IAccount
	{
		if ($this->user->getAccount() === null) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_FORBIDDEN,
				$this->translator->translate('//node.base.messages.forbidden.heading'),
				$this->translator->translate('//node.base.messages.forbidden.message')
			);
		}

		return $this->user->getAccount();
	}

}
