<?php declare(strict_types = 1);

/**
 * SyncCommand.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Commands
 * @since          0.1.0
 *
 * @date           24.06.20
 */

namespace FastyBird\AuthNode\Commands;

use Contributte\Translation;
use Doctrine\Common;
use Doctrine\DBAL\Connection;
use FastyBird\AuthNode;
use FastyBird\AuthNode\Entities;
use FastyBird\AuthNode\Exceptions;
use FastyBird\AuthNode\Models;
use FastyBird\AuthNode\Queries;
use FastyBird\NodesMetadata\Loaders as NodesMetadataLoaders;
use Nette\Security as NS;
use Nette\Utils;
use Psr\Log\LoggerInterface;
use SplObjectStorage;
use Symfony\Component\Console;
use Symfony\Component\Console\Input;
use Symfony\Component\Console\Output;
use Symfony\Component\Console\Style;
use Throwable;

/**
 * Synchronize resources & privileges
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Commands
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class SyncCommand extends Console\Command\Command
{

	/** @var Models\Privileges\IPrivilegeRepository */
	private $privilegeRepository;

	/** @var Models\Privileges\IPrivilegesManager */
	private $privilegesManager;

	/** @var Models\Resources\IResourceRepository */
	private $resourceRepository;

	/** @var Models\Resources\IResourcesManager */
	private $resourcesManager;

	/** @var NodesMetadataLoaders\IMetadataLoader */
	private $metadataLoader;

	/** @var Common\Persistence\ManagerRegistry */
	private $managerRegistry;

	/** @var LoggerInterface */
	private $logger;

	/** @var Translation\PrefixedTranslator */
	private $translator;

	/** @var string */
	private $translationDomain = 'node.commands.sync';

	public function __construct(
		Models\Privileges\IPrivilegeRepository $privilegeRepository,
		Models\Privileges\IPrivilegesManager $privilegesManager,
		Models\Resources\IResourceRepository $resourceRepository,
		Models\Resources\IResourcesManager $resourcesManager,
		NodesMetadataLoaders\IMetadataLoader $metadataLoader,
		Translation\Translator $translator,
		Common\Persistence\ManagerRegistry $managerRegistry,
		LoggerInterface $logger,
		?string $name = null
	) {
		// Modules models
		$this->privilegeRepository = $privilegeRepository;
		$this->privilegesManager = $privilegesManager;
		$this->resourceRepository = $resourceRepository;
		$this->resourcesManager = $resourcesManager;

		$this->metadataLoader = $metadataLoader;

		$this->managerRegistry = $managerRegistry;

		$this->logger = $logger;

		$this->translator = new Translation\PrefixedTranslator($translator, $this->translationDomain);

		parent::__construct($name);
	}

	/**
	 * {@inheritDoc}
	 */
	protected function configure(): void
	{
		parent::configure();

		$this
			->setName('fb:auth-node:sync')
			->addOption('noconfirm', null, Input\InputOption::VALUE_NONE, 'do not ask for any confirmation')
			->setDescription('Synchronize resources & privileges.')
			->setHelp('This command synchronize all resources:privileges from all installed modules');
	}

	/**
	 * {@inheritDoc}
	 */
	protected function execute(Input\InputInterface $input, Output\OutputInterface $output)
	{
		$io = new Style\SymfonyStyle($input, $output);

		$io->title('FB auth node - synchronization');

		$resources = new Utils\ArrayHash();

		// Collect all privileges & resources combinations from modules
		foreach ($this->metadataLoader->load() as $origin => $metadatas) {
			/** @var Utils\ArrayHash $metadata */
			foreach ($metadatas as $metadata) {
				if ($metadata->offsetExists('version') && $metadata->offsetGet('version') === '*') {
					$nodePermissions = $metadata->offsetExists('metadata') && $metadata->offsetGet('metadata')->offsetExists('permissions') ?
						$metadata->offsetGet('metadata')->offsetGet('permissions') : [];

					foreach ($nodePermissions as $nodePermission) {
						// Parse resource & privilege from permission
						[$resource, $privilege] = explode(AuthNode\Constants::PERMISSIONS_DELIMITER, $nodePermission->offsetGet('permission'));

						// Remove white spaces
						$resource = Utils\Strings::trim($resource);
						$privilege = Utils\Strings::trim($privilege);

						$privilege = $privilege === '' ? NS\IAuthorizator::ALL : $privilege;

						if (!$resources->offsetExists($resource)) {
							$resources->offsetSet($resource, new Utils\ArrayHash());
							$resources->offsetGet($resource)->offsetSet('origin', $origin);
							$resources->offsetGet($resource)->offsetSet('privileges', new Utils\ArrayHash());
						}

						$resources->offsetGet($resource)->offsetGet('privileges')->offsetSet($privilege, $nodePermission->offsetGet('title'));
					}
				}
			}
		}

		$actualPrivileges = $actualResources = new SplObjectStorage();

		foreach ($resources as $resourceName => $resourceConfiguration) {
			$findResource = new Queries\FindResourcesQuery();
			$findResource->byName($resourceName);

			$resource = $this->resourceRepository->findOneBy($findResource);

			foreach ($resourceConfiguration->offsetGet('privileges') as $privilegeName => $title) {
				try {
					// Start transaction connection to the database
					$this->getOrmConnection()->beginTransaction();

					if ($resource === null) {
						$create = new Utils\ArrayHash();
						$create->origin = $resourceConfiguration->offsetGet('origin');
						$create->name = $resourceName;
						$create->description = $resourceName;

						$resource = $this->resourcesManager->create($create);
					}

					$findPrivilege = new Queries\FindPrivilegesQuery();
					$findPrivilege->byName($privilegeName);
					$findPrivilege->forResource($resource);

					$privilege = $this->privilegeRepository->findOneBy($findPrivilege);

					if ($privilege === null) {
						$create = new Utils\ArrayHash();
						$create->name = $privilegeName;
						$create->resource = $resource;
						$create->description = $title;

						$privilege = $this->privilegesManager->create($create);

						$io->text(sprintf('<bg=green;options=bold> Created </> <info>%s : %s</info>', $resource->getResourceId(), $privilege->getPrivilegeId()));

					} else {
						$update = new Utils\ArrayHash();
						$update->description = $title;

						$privilege = $this->privilegesManager->update($privilege, $update);

						$io->text(sprintf('<bg=green;options=bold> Updated </> <info>%s : %s</info>', $resource->getResourceId(), $privilege->getPrivilegeId()));
					}

					// Commit all changes into database
					$this->getOrmConnection()->commit();

					$actualResources->attach($resource);
					$actualPrivileges->attach($privilege);

				} catch (Throwable $ex) {
					// Revert all changes when error occur
					$this->getOrmConnection()->rollBack();

					$this->logger->error($ex->getMessage());

					$io->text(sprintf('<error>%s</error>', $this->translator->translate('validation.sync.notFinished', ['error' => $ex->getMessage()])));
				}
			}
		}

		/** @var Entities\Privileges\IPrivilege[] $privileges */
		$privileges = $this->privilegeRepository->findAll();

		foreach ($privileges as $privilege) {
			if (!$actualPrivileges->contains($privilege)) {
				$io->text(sprintf('<bg=red;options=bold> Removed </> <info>%s : %s</info>', $privilege->getResource()->getResourceId(), $privilege->getPrivilegeId()));

				$this->privilegesManager->delete($privilege);
			}
		}

		/** @var Entities\Resources\IResource[] $resources */
		$resources = $this->resourceRepository->findAll();

		foreach ($resources as $resource) {
			if (!$actualPrivileges->contains($resource)) {
				$io->text(sprintf('<bg=red;options=bold> Removed </> <info>%s</info>', $resource->getResourceId()));

				$this->resourcesManager->delete($resource);
			}
		}

		$io->text([
			'',
			'<info>Synchronization complete</info>',
			'',
		]);

		return 0;
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
