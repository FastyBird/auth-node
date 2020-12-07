<?php declare(strict_types = 1);

/**
 * DeviceMessageHandler.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Consumers
 * @since          0.1.0
 *
 * @date           05.08.20
 */

namespace FastyBird\AuthNode\Consumers;

use Doctrine\Common;
use Doctrine\DBAL;
use Doctrine\DBAL\Connection;
use FastyBird\AuthModule\Entities as AuthModuleEntities;
use FastyBird\AuthModule\Models as AuthModuleModels;
use FastyBird\AuthModule\Queries as AuthModuleQueries;
use FastyBird\AuthModule\Types as AuthModuleTypes;
use FastyBird\AuthNode;
use FastyBird\AuthNode\Exceptions;
use FastyBird\ModulesMetadata;
use FastyBird\ModulesMetadata\Loaders as ModulesMetadataLoaders;
use FastyBird\ModulesMetadata\Schemas as ModulesMetadataSchemas;
use FastyBird\RabbitMqPlugin\Consumers as RabbitMqPluginConsumers;
use FastyBird\RabbitMqPlugin\Exceptions as RabbitMqPluginExceptions;
use Nette;
use Nette\Utils;
use Psr\Log;
use Ramsey\Uuid;
use Throwable;

/**
 * Device message consumer
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Consumers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class DeviceMessageHandler implements RabbitMqPluginConsumers\IMessageHandler
{

	use Nette\SmartObject;

	/** @var AuthModuleModels\Accounts\IAccountRepository */
	private $accountRepository;

	/** @var AuthModuleModels\Accounts\IAccountsManager */
	private $accountsManager;

	/** @var ModulesMetadataLoaders\ISchemaLoader */
	private $schemaLoader;

	/** @var ModulesMetadataSchemas\IValidator */
	private $validator;

	/** @var Log\LoggerInterface */
	private $logger;

	/** @var Common\Persistence\ManagerRegistry */
	private $managerRegistry;

	public function __construct(
		AuthModuleModels\Accounts\IAccountRepository $accountRepository,
		AuthModuleModels\Accounts\IAccountsManager $accountsManager,
		ModulesMetadataLoaders\ISchemaLoader $schemaLoader,
		ModulesMetadataSchemas\IValidator $validator,
		Log\LoggerInterface $logger,
		Common\Persistence\ManagerRegistry $managerRegistry
	) {
		$this->accountRepository = $accountRepository;
		$this->accountsManager = $accountsManager;

		$this->schemaLoader = $schemaLoader;
		$this->validator = $validator;
		$this->logger = $logger;
		$this->managerRegistry = $managerRegistry;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws RabbitMqPluginExceptions\TerminateException
	 * @throws DBAL\ConnectionException
	 */
	public function process(
		string $routingKey,
		string $origin,
		string $payload
	): bool {
		$schema = $this->getSchema($routingKey, $origin);

		if ($schema === null) {
			return true;
		}

		try {
			$message = $this->validator->validate($payload, $schema);

		} catch (Throwable $ex) {
			$this->logger->error('[FB:NODE:CONSUMER] ' . $ex->getMessage(), [
				'exception' => [
					'message' => $ex->getMessage(),
					'code'    => $ex->getCode(),
				],
			]);

			return true;
		}

		try {
			switch ($routingKey) {
				case AuthNode\Constants::RABBIT_MQ_DEVICES_CREATED_ENTITY_ROUTING_KEY:
					$findAccount = new AuthModuleQueries\FindAccountsQuery();
					$findAccount->byId(Uuid\Uuid::fromString($message->offsetGet('id')));

					$account = $this->accountRepository->findOneBy($findAccount);

					if ($account === null) {
						if ($message->offsetGet('parent') === null) {
							$findAccount = new AuthModuleQueries\FindAccountsQuery();
							$findAccount->byId(Uuid\Uuid::fromString($message->offsetGet('owner')));

							$owner = $this->accountRepository->findOneBy($findAccount);

							if ($owner !== null) {
								// Start transaction connection to the database
								$this->getOrmConnection()->beginTransaction();

								$create = Utils\ArrayHash::from([
									'id'     => Uuid\Uuid::fromString($message->offsetGet('id')),
									'device' => $message->offsetGet('device'),
									'entity' => AuthModuleEntities\Accounts\MachineAccount::class,
									'state'  => AuthModuleTypes\AccountStateType::get(AuthModuleTypes\AccountStateType::STATE_ACTIVE),
									'owner'  => $owner,
								]);

								$this->accountsManager->create($create);

								// Commit all changes into database
								$this->getOrmConnection()->commit();
							}
						}

					} elseif ($message->offsetGet('parent') !== null) {
						// Start transaction connection to the database
						$this->getOrmConnection()->beginTransaction();

						$this->accountsManager->delete($account);

						// Commit all changes into database
						$this->getOrmConnection()->commit();
					}
					break;

				case AuthNode\Constants::RABBIT_MQ_DEVICES_DELETED_ENTITY_ROUTING_KEY:
					$findAccount = new AuthModuleQueries\FindAccountsQuery();
					$findAccount->byId(Uuid\Uuid::fromString($message->offsetGet('id')));

					$account = $this->accountRepository->findOneBy($findAccount);

					if ($account !== null) {
						// Start transaction connection to the database
						$this->getOrmConnection()->beginTransaction();

						$this->accountsManager->delete($account);

						// Commit all changes into database
						$this->getOrmConnection()->commit();
					}
					break;

				default:
					throw new Exceptions\InvalidStateException('Unknown routing key');
			}

		} catch (Exceptions\InvalidStateException $ex) {
			return false;

		} catch (Throwable $ex) {
			throw new RabbitMqPluginExceptions\TerminateException('An error occurred: ' . $ex->getMessage(), $ex->getCode(), $ex);

		} finally {
			// Revert all changes when error occur
			if ($this->getOrmConnection()->isTransactionActive()) {
				$this->getOrmConnection()->rollBack();
			}
		}

		$this->logger->info('[CONSUMER] Successfully consumed entity message', [
			'routingKey' => $routingKey,
		]);

		return true;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getSchema(string $routingKey, string $origin): ?string
	{
		if ($origin === AuthNode\Constants::RABBIT_MQ_DEVICES_ORIGIN) {
			switch ($routingKey) {
				case AuthNode\Constants::RABBIT_MQ_DEVICES_CREATED_ENTITY_ROUTING_KEY:
				case AuthNode\Constants::RABBIT_MQ_DEVICES_DELETED_ENTITY_ROUTING_KEY:
					return $this->schemaLoader->load(ModulesMetadata\Constants::RESOURCES_FOLDER . '/schemas/devices-module/entity.device.json');
			}
		}

		return null;
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
