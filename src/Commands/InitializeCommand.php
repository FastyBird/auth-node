<?php declare(strict_types = 1);

/**
 * InitializeCommand.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Commands
 * @since          0.1.0
 *
 * @date           31.07.20
 */

namespace FastyBird\AuthNode\Commands;

use Doctrine\Common;
use Doctrine\DBAL\Connection;
use FastyBird\AuthNode\Exceptions;
use FastyBird\AuthNode\Models;
use FastyBird\AuthNode\Queries;
use FastyBird\NodeAuth;
use Nette\Utils;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\Console;
use Symfony\Component\Console\Input;
use Symfony\Component\Console\Output;
use Symfony\Component\Console\Style;
use Throwable;

/**
 * Node initialize command
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Commands
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class InitializeCommand extends Console\Command\Command
{

	/** @var Models\Accounts\IAccountRepository */
	private $accountRepository;

	/** @var Models\Roles\IRoleRepository */
	private $roleRepository;

	/** @var Models\Roles\IRolesManager */
	private $rolesManager;

	/** @var Common\Persistence\ManagerRegistry */
	private $managerRegistry;

	/** @var LoggerInterface */
	private $logger;

	public function __construct(
		Models\Accounts\IAccountRepository $accountRepository,
		Models\Roles\IRoleRepository $roleRepository,
		Models\Roles\IRolesManager $rolesManager,
		Common\Persistence\ManagerRegistry $managerRegistry,
		LoggerInterface $logger,
		?string $name = null
	) {
		$this->accountRepository = $accountRepository;
		$this->roleRepository = $roleRepository;
		$this->rolesManager = $rolesManager;

		$this->managerRegistry = $managerRegistry;

		$this->logger = $logger;

		parent::__construct($name);
	}

	/**
	 * {@inheritDoc}
	 */
	protected function configure(): void
	{
		$this
			->setName('fb:initialize')
			->addOption('noconfirm', null, Input\InputOption::VALUE_NONE, 'do not ask for any confirmation')
			->setDescription('Initialize node.');
	}

	/**
	 * {@inheritDoc}
	 */
	protected function execute(Input\InputInterface $input, Output\OutputInterface $output)
	{
		$symfonyApp = $this->getApplication();

		if ($symfonyApp === null) {
			return 1;
		}

		$io = new Style\SymfonyStyle($input, $output);

		$io->title('FB auth node - initialization');

		$io->note('This action will create|update node database structure, create initial data and initialize administrator account.');

		/** @var bool $continue */
		$continue = $io->ask('Would you like to continue?', 'n', function ($answer): bool {
			if (!in_array($answer, ['y', 'Y', 'n', 'N'], true)) {
				throw new RuntimeException('You must type Y or N');
			}

			return in_array($answer, ['y', 'Y'], true);
		});

		if (!$continue) {
			return 0;
		}

		$io->section('Preparing node database');

		$databaseCmd = $symfonyApp->find('orm:schema-tool:update');

		$result = $databaseCmd->run(new Input\ArrayInput([
			'--force' => true,
		]), $output);

		if ($result !== 0) {
			$io->error('Something went wrong, initialization could not be finished.');

			return 1;
		}

		$databaseProxiesCmd = $symfonyApp->find('orm:generate-proxies');

		$result = $databaseProxiesCmd->run(new Input\ArrayInput([
			'--quiet' => true,
		]), $output);

		if ($result !== 0) {
			$io->error('Something went wrong, initialization could not be finished.');

			return 1;
		}

		$io->newLine();

		$io->section('Preparing initial data');

		$allRoles = [
			NodeAuth\Constants::ROLE_ANONYMOUS,
			NodeAuth\Constants::ROLE_VISITOR,
			NodeAuth\Constants::ROLE_USER,
			NodeAuth\Constants::ROLE_MANAGER,
			NodeAuth\Constants::ROLE_ADMINISTRATOR,
		];

		try {
			// Start transaction connection to the database
			$this->getOrmConnection()->beginTransaction();

			$parent = null;

			// Roles initialization
			foreach ($allRoles as $roleName) {
				$findRole = new Queries\FindRolesQuery();
				$findRole->byName($roleName);

				$role = $this->roleRepository->findOneBy($findRole);

				if ($role === null) {
					$create = new Utils\ArrayHash();
					$create->offsetSet('name', $roleName);
					$create->offsetSet('description', $roleName);
					$create->offsetSet('parent', $parent);

					$parent = $this->rolesManager->create($create);
				}
			}

			// Commit all changes into database
			$this->getOrmConnection()->commit();

		} catch (Throwable $ex) {
			// Revert all changes when error occur
			if ($this->getOrmConnection()->isTransactionActive()) {
				$this->getOrmConnection()->rollBack();
			}

			$this->logger->error($ex->getMessage());

			$io->error('Initial data could not be created.');

			return $ex->getCode();
		}

		$io->success('All initial data has been successfully created.');

		$io->newLine();

		$io->section('Checking for administrator account');

		$findRole = new Queries\FindRolesQuery();
		$findRole->byName(NodeAuth\Constants::ROLE_ADMINISTRATOR);

		$administratorRole = $this->roleRepository->findOneBy($findRole);

		if ($administratorRole !== null) {
			$findAccounts = new Queries\FindAccountsQuery();
			$findAccounts->inRole($administratorRole);

			$accounts = $this->accountRepository->findAllBy($findAccounts);

			if (count($accounts) === 0) {
				$accountCmd = $symfonyApp->find('fb:auth-node:accounts:create');

				$result = $accountCmd->run(new Input\ArrayInput([
					'role'       => NodeAuth\Constants::ROLE_ADMINISTRATOR,
					'identity'   => 'y',
					'--injected' => true,
				]), $output);

				if ($result !== 0) {
					$io->error('Something went wrong, initialization could not be finished.');

					return 1;
				}

			} else {
				$io->success('There is existing administrator account.');
			}

		} else {
			$io->error('Something went wrong, administrator role could not be found.');

			return 1;
		}

		$io->newLine(3);

		$io->success('This node has been successfully initialized and can be now started.');

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
