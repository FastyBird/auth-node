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

namespace FastyBird\AuthNode\Commands\Accounts;

use Contributte\Translation;
use Doctrine\Common;
use Doctrine\DBAL\Connection;
use FastyBird\AuthNode\Entities;
use FastyBird\AuthNode\Exceptions;
use FastyBird\AuthNode\Models;
use FastyBird\AuthNode\Queries;
use FastyBird\AuthNode\Types;
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

	/** @var Models\Accounts\IAccountsManager */
	private $accountsManager;

	/** @var Models\Emails\IEmailRepository */
	private $emailRepository;

	/** @var Models\Emails\IEmailsManager */
	private $emailsManager;

	/** @var Models\Identities\IIdentitiesManager */
	private $identitiesManager;

	/** @var Models\Roles\IRoleRepository */
	private $roleRepository;

	/** @var Common\Persistence\ManagerRegistry */
	private $managerRegistry;

	/** @var LoggerInterface */
	private $logger;

	/** @var Translation\PrefixedTranslator */
	private $translator;

	/** @var string */
	private $translationDomain = 'node.commands.accountCreate';

	public function __construct(
		Models\Accounts\IAccountsManager $accountsManager,
		Models\Emails\IEmailRepository $emailRepository,
		Models\Emails\IEmailsManager $emailsManager,
		Models\Identities\IIdentitiesManager $identitiesManager,
		Models\Roles\IRoleRepository $roleRepository,
		Translation\Translator $translator,
		Common\Persistence\ManagerRegistry $managerRegistry,
		LoggerInterface $logger,
		?string $name = null
	) {
		$this->accountsManager = $accountsManager;
		$this->emailRepository = $emailRepository;
		$this->emailsManager = $emailsManager;
		$this->identitiesManager = $identitiesManager;
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
			->setName('fb:auth-node:accounts:create')
			->addArgument('lastName', Input\InputArgument::OPTIONAL, $this->translator->translate('inputs.lastName.title'))
			->addArgument('firstName', Input\InputArgument::OPTIONAL, $this->translator->translate('inputs.firstName.title'))
			->addArgument('email', Input\InputArgument::OPTIONAL, $this->translator->translate('inputs.email.title'))
			->addArgument('role', Input\InputArgument::OPTIONAL, $this->translator->translate('inputs.role.title'))
			->addOption('noconfirm', null, Input\InputOption::VALUE_NONE, 'do not ask for any confirmation')
			->addOption('injected', null, Input\InputOption::VALUE_NONE, 'do not show all outputs')
			->setDescription('Create account.');
	}

	/**
	 * {@inheritDoc}
	 */
	protected function execute(Input\InputInterface $input, Output\OutputInterface $output)
	{
		$io = new Style\SymfonyStyle($input, $output);

		if (!$input->hasOption('injected')) {
			$io->title('FB auth node - create account');
		}

		if (
			$input->hasArgument('lastName')
			&& is_string($input->getArgument('lastName'))
			&& $input->getArgument('lastName') !== ''
		) {
			$lastName = $input->getArgument('lastName');

		} else {
			$lastName = $io->ask($this->translator->translate('inputs.lastName.title'));
		}

		if (
			$input->hasArgument('firstName')
			&& is_string($input->getArgument('firstName'))
			&& $input->getArgument('firstName') !== ''
		) {
			$firstName = $input->getArgument('firstName');

		} else {
			$firstName = $io->ask($this->translator->translate('inputs.firstName.title'));
		}

		if (
			$input->hasArgument('email')
			&& is_string($input->getArgument('email'))
			&& $input->getArgument('email') !== ''
		) {
			$emailAddress = $input->getArgument('email');

		} else {
			$emailAddress = $io->ask($this->translator->translate('inputs.email.title'));
		}

		do {
			if (!Utils\Validators::isEmail($emailAddress)) {
				$io->error($this->translator->translate('validation.email.invalid', ['email' => $emailAddress]));

				$repeat = true;

			} else {
				$email = $this->emailRepository->findOneByAddress($emailAddress);

				$repeat = $email !== null;

				if ($repeat) {
					$io->error($this->translator->translate('validation.email.taken', ['email' => $emailAddress]));
				}
			}

			if ($repeat) {
				$emailAddress = $io->ask($this->translator->translate('inputs.email.title'));
			}

		} while ($repeat);

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

		try {
			// Start transaction connection to the database
			$this->getOrmConnection()->beginTransaction();

			$create = new Utils\ArrayHash();
			$create->offsetSet('entity', Entities\Accounts\UserAccount::class);
			$create->offsetSet('state', Types\AccountStateType::get(Types\AccountStateType::STATE_ACTIVE));
			$create->offsetSet('roles', [$role]);

			$details = new Utils\ArrayHash();
			$details->offsetSet('entity', Entities\Details\Details::class);
			$details->offsetSet('firstName', $firstName);
			$details->offsetSet('lastName', $lastName);

			$create->offsetSet('details', $details);

			/** @var Entities\Accounts\IUserAccount $account */
			$account = $this->accountsManager->create($create);

			// Create new email entity for user
			$create = new Utils\ArrayHash();
			$create->offsetSet('account', $account);
			$create->offsetSet('address', $emailAddress);
			$create->offsetSet('default', true);

			// Create new email entity
			$this->emailsManager->create($create);

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

		$password = $io->askHidden($this->translator->translate('inputs.password.title'));

		if ($account->getEmail() === null) {
			$io->warning($this->translator->translate('validation.identity.noEmail'));

			return 0;
		}

		try {
			// Start transaction connection to the database
			$this->getOrmConnection()->beginTransaction();

			// Create new email entity for user
			$create = new Utils\ArrayHash();
			$create->offsetSet('entity', Entities\Identities\UserAccountIdentity::class);
			$create->offsetSet('account', $account);
			$create->offsetSet('uid', $account->getEmail()->getAddress());
			$create->offsetSet('password', $password);
			$create->offsetSet('state', Types\IdentityStateType::get(Types\IdentityStateType::STATE_ACTIVE));

			$this->identitiesManager->create($create);

			// Commit all changes into database
			$this->getOrmConnection()->commit();

		} catch (Throwable $ex) {
			// Revert all changes when error occur
			if ($this->getOrmConnection()->isTransactionActive()) {
				$this->getOrmConnection()->rollBack();
			}

			$this->logger->error($ex->getMessage());

			$io->error($this->translator->translate('validation.identity.wasNotCreated', ['error' => $ex->getMessage()]));

			return $ex->getCode();
		}

		$io->success($this->translator->translate('success', ['name' => $account->getName()]));

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
