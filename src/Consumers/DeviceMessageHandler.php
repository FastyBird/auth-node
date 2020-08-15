<?php declare(strict_types = 1);

/**
 * DeviceMessageHandler.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
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
use FastyBird\AuthNode;
use FastyBird\AuthNode\Entities;
use FastyBird\AuthNode\Exceptions;
use FastyBird\AuthNode\Models;
use FastyBird\AuthNode\Queries;
use FastyBird\NodeAuth;
use FastyBird\NodeExchange\Consumers as NodeExchangeConsumers;
use FastyBird\NodeExchange\Exceptions as NodeExchangeExceptions;
use FastyBird\NodeMetadata;
use FastyBird\NodeMetadata\Loaders as NodeMetadataLoaders;
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
final class DeviceMessageHandler implements NodeExchangeConsumers\IMessageHandler
{

	use Nette\SmartObject;

	/** @var Models\Accounts\IAccountRepository */
	private $accountRepository;

	/** @var Models\Accounts\IAccountsManager */
	private $accountsManager;

	/** @var Models\Roles\IRoleRepository */
	private $roleRepository;

	/** @var NodeMetadataLoaders\ISchemaLoader */
	private $schemaLoader;

	/** @var Log\LoggerInterface */
	private $logger;

	/** @var Common\Persistence\ManagerRegistry */
	private $managerRegistry;

	public function __construct(
		Models\Accounts\IAccountRepository $accountRepository,
		Models\Accounts\IAccountsManager $accountsManager,
		Models\Roles\IRoleRepository $roleRepository,
		NodeMetadataLoaders\ISchemaLoader $schemaLoader,
		Log\LoggerInterface $logger,
		Common\Persistence\ManagerRegistry $managerRegistry
	) {
		$this->accountRepository = $accountRepository;
		$this->accountsManager = $accountsManager;
		$this->roleRepository = $roleRepository;

		$this->schemaLoader = $schemaLoader;
		$this->logger = $logger;
		$this->managerRegistry = $managerRegistry;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws NodeExchangeExceptions\TerminateException
	 * @throws DBAL\ConnectionException
	 */
	public function process(
		string $routingKey,
		Utils\ArrayHash $message
	): bool {
		try {
			switch ($routingKey) {
				case AuthNode\Constants::RABBIT_MQ_DEVICES_CREATED_ENTITY_ROUTING_KEY:
					$findAccount = new Queries\FindAccountsQuery();
					$findAccount->byId(Uuid\Uuid::fromString($message->offsetGet('id')));

					$account = $this->accountRepository->findOneBy($findAccount);

					if ($account === null) {
						// Start transaction connection to the database
						$this->getOrmConnection()->beginTransaction();

						$findRole = new Queries\FindRolesQuery();
						$findRole->byName(NodeAuth\Constants::ROLE_USER);

						$role = $this->roleRepository->findOneBy($findRole);

						$create = Utils\ArrayHash::from([
							'id'     => Uuid\Uuid::fromString($message->offsetGet('id')),
							'entity' => Entities\Accounts\MachineAccount::class,
							'status' => AuthNode\Types\AccountStatusType::get(AuthNode\Types\AccountStatusType::STATE_ACTIVATED),
							'roles'  => [
								$role,
							],
						]);

						$this->accountsManager->create($create);

						// Commit all changes into database
						$this->getOrmConnection()->commit();
					}
					break;

				case AuthNode\Constants::RABBIT_MQ_DEVICES_DELETED_ENTITY_ROUTING_KEY:
					$findAccount = new Queries\FindAccountsQuery();
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
			throw new NodeExchangeExceptions\TerminateException('An error occurred: ' . $ex->getMessage(), $ex->getCode(), $ex);

		} finally {
			// Revert all changes when error occur
			if ($this->getOrmConnection()->isTransactionActive()) {
				$this->getOrmConnection()->rollBack();
			}
		}

		$this->logger->info('[CONSUMER] Successfully consumed entity message');

		return true;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getSchema(string $routingKey, string $origin): ?string
	{
		if ($origin === AuthNode\Constants::NODE_DEVICES_ORIGIN) {
			switch ($routingKey) {
				case AuthNode\Constants::RABBIT_MQ_DEVICES_CREATED_ENTITY_ROUTING_KEY:
				case AuthNode\Constants::RABBIT_MQ_DEVICES_DELETED_ENTITY_ROUTING_KEY:
					return $this->schemaLoader->load(NodeMetadata\Constants::RESOURCES_FOLDER . '/schemas/devices-node/entity.device.json');
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
