<?php declare(strict_types = 1);

/**
 * CreateCommand.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Commands
 * @since          0.1.0
 *
 * @date           31.03.20
 */

namespace FastyBird\AuthNode\Commands\Vernemq;

use Contributte\Translation;
use Doctrine\Common;
use Doctrine\DBAL\Connection;
use FastyBird\AuthNode\Entities;
use FastyBird\AuthNode\Exceptions;
use FastyBird\AuthNode\Models;
use FastyBird\AuthNode\Queries;
use FastyBird\NodeAuth;
use Monolog;
use Nette\Utils;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console;
use Symfony\Component\Console\Input;
use Symfony\Component\Console\Output;
use Symfony\Component\Console\Style;
use Throwable;

/**
 * Account creation command
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Commands
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class CreateCommand extends Console\Command\Command
{

	/** @var Models\Vernemq\IAccountsManager */
	private $accountsManager;

	/** @var Common\Persistence\ManagerRegistry */
	private $managerRegistry;

	/** @var Models\Roles\IRoleRepository */
	private $roleRepository;

	/** @var LoggerInterface */
	private $logger;

	/** @var Translation\PrefixedTranslator */
	private $translator;

	/** @var string */
	private $translationDomain = 'node.commands.vernemqCreate';

	public function __construct(
		Models\Vernemq\IAccountsManager $accountsManager,
		Models\Roles\IRoleRepository $roleRepository,
		Translation\Translator $translator,
		Common\Persistence\ManagerRegistry $managerRegistry,
		LoggerInterface $logger,
		?string $name = null
	) {
		$this->accountsManager = $accountsManager;
		$this->roleRepository = $roleRepository;

		$this->managerRegistry = $managerRegistry;

		$this->logger = $logger;

		$this->translator = new Translation\PrefixedTranslator($translator, $this->translationDomain);

		parent::__construct($name);

		// Override loggers to not log debug events into console
		if ($logger instanceof Monolog\Logger) {
			foreach ($logger->getHandlers() as $handler) {
				if ($handler instanceof Monolog\Handler\StreamHandler) {
					$handler->setLevel(Monolog\Logger::WARNING);
				}
			}
		}
	}

	/**
	 * {@inheritDoc}
	 */
	protected function configure(): void
	{
		$this
			->setName('fb:auth-node:create:vernemq')
			->addArgument('username', Input\InputArgument::OPTIONAL, $this->translator->translate('inputs.username.title'))
			->addArgument('password', Input\InputArgument::OPTIONAL, $this->translator->translate('inputs.password.title'))
			->addArgument('role', Input\InputArgument::OPTIONAL, $this->translator->translate('inputs.role.title'))
			->addOption('noconfirm', null, Input\InputOption::VALUE_NONE, 'do not ask for any confirmation')
			->addOption('injected', null, Input\InputOption::VALUE_NONE, 'do not show all outputs')
			->setDescription('Create vernemq account.');
	}

	/**
	 * {@inheritDoc}
	 */
	protected function execute(Input\InputInterface $input, Output\OutputInterface $output)
	{
		$io = new Style\SymfonyStyle($input, $output);

		if (!$input->hasOption('injected')) {
			$io->title('FB auth node - create Verne MQ account');
		}

		if (
			$input->hasArgument('username')
			&& is_string($input->getArgument('username'))
			&& $input->getArgument('username') !== ''
		) {
			$username = $input->getArgument('username');

		} else {
			$username = $io->ask($this->translator->translate('inputs.username.title'));
		}

		if (
			$input->hasArgument('password')
			&& is_string($input->getArgument('password'))
			&& $input->getArgument('password') !== ''
		) {
			$password = $input->getArgument('password');

		} else {
			$password = $io->ask($this->translator->translate('inputs.password.title'));
		}

		$repeat = true;

		if ($input->hasArgument('role') && in_array($input->getArgument('role'), [NodeAuth\Constants::ROLE_USER, NodeAuth\Constants::ROLE_MANAGER, NodeAuth\Constants::ROLE_ADMINISTRATOR], true)) {
			$findRole = new Queries\FindRolesQuery();
			$findRole->byName($input->getArgument('role'));

			$role = $this->roleRepository->findOneBy($findRole);

			if ($role === null) {
				$io->error('Entered unknown role name.');

				return 1;
			}

		} else {
			do {
				$roleName = $io->choice(
					$this->translator->translate('inputs.role.title'),
					[
						'U' => $this->translator->translate('inputs.role.values.user'),
						'M' => $this->translator->translate('inputs.role.values.manager'),
						'A' => $this->translator->translate('inputs.role.values.administrator'),
					],
					'U'
				);

				switch ($roleName) {
					case 'U':
						$roleName = NodeAuth\Constants::ROLE_USER;
						break;

					case 'M':
						$roleName = NodeAuth\Constants::ROLE_MANAGER;
						break;

					case 'A':
						$roleName = NodeAuth\Constants::ROLE_ADMINISTRATOR;
						break;
				}

				$findRole = new Queries\FindRolesQuery();
				$findRole->byName($roleName);

				$role = $this->roleRepository->findOneBy($findRole);

				if ($role !== null) {
					$repeat = false;
				}

			} while ($repeat);
		}

		if ($role === null) {
			$io->error('Role could\'t be loaded.');

			return 1;
		}

		if ($role->getName() === NodeAuth\Constants::ROLE_USER) {
			$publishAcls = [];

			$subscribeAcls = [
				'/fb/#',
			];

		} else {
			$publishAcls = [
				'/fb/#',
			];

			$subscribeAcls = [
				'/fb/#',
				'$SYS/broker/log/#',
			];
		}

		try {
			// Start transaction connection to the database
			$this->getOrmConnection()->beginTransaction();

			$create = Utils\ArrayHash::from([
				'entity'       => Entities\Vernemq\Account::class,
				'username'     => $username,
				'password'     => $password,
				'publishAcl'   => $publishAcls,
				'subscribeAcl' => $subscribeAcls,
			]);

			$account = $this->accountsManager->create($create);

			// Commit all changes into database
			$this->getOrmConnection()->commit();

		} catch (Throwable $ex) {
			// Revert all changes when error occur
			if ($this->getOrmConnection()->isTransactionActive()) {
				$this->getOrmConnection()->rollBack();
			}

			$this->logger->error($ex->getMessage());

			$io->error($this->translator->translate('validation.account.wasNotCreated', ['error' => $ex->getMessage()]));

			return $ex->getCode();
		}

		$io->success($this->translator->translate('success', ['name' => $account->getUsername()]));

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
