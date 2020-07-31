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
use FastyBird\AuthNode\Types;
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
		Models\Identities\IIdentitiesManager  $identitiesManager,
		Translation\Translator $translator,
		Common\Persistence\ManagerRegistry $managerRegistry,
		LoggerInterface $logger,
		?string $name = null
	) {
		$this->accountsManager = $accountsManager;
		$this->emailRepository = $emailRepository;
		$this->emailsManager = $emailsManager;
		$this->identitiesManager = $identitiesManager;

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
		$this
			->setName('fb:auth-node:accounts:create')
			->addArgument('lastName', Input\InputArgument::OPTIONAL, $this->translator->translate('inputs.lastName.title'))
			->addArgument('firstName', Input\InputArgument::OPTIONAL, $this->translator->translate('inputs.firstName.title'))
			->addArgument('email', Input\InputArgument::OPTIONAL, $this->translator->translate('inputs.email.title'))
			->addOption('noconfirm', null, Input\InputOption::VALUE_NONE, 'do not ask for any confirmation')
			->setDescription('Create account.');
	}

	/**
	 * {@inheritDoc}
	 */
	protected function execute(Input\InputInterface $input, Output\OutputInterface $output)
	{
		$io = new Style\SymfonyStyle($input, $output);

		$io->title('FB auth node - create account');

		if ($input->hasOption('lastName') && $input->getOption('lastName') !== '') {
			$lastName = $input->getOption('lastName');

		} else {
			$lastName = $io->ask($this->translator->translate('inputs.lastName.title'));
		}

		if ($input->hasOption('firstName') && $input->getOption('firstName') !== '') {
			$firstName = $input->getOption('firstName');

		} else {
			$firstName = $io->ask($this->translator->translate('inputs.firstName.title'));
		}

		if ($input->hasOption('email') && $input->getOption('email') !== '') {
			$emailAddress = $input->getOption('email');

		} else {
			$emailAddress = $io->ask($this->translator->translate('inputs.email.title'));
		}

		do {
			if (!Utils\Validators::isEmail($emailAddress)) {
				$io->text(sprintf('<error>%s</error>', $this->translator->translate('validation.email.invalid', ['email' => $emailAddress])));

				$repeat = true;

			} else {
				$email = $this->emailRepository->findOneByAddress($emailAddress);

				$repeat = $email !== null;

				if ($repeat) {
					$io->text(sprintf('<error>%s</error>', $this->translator->translate('validation.email.taken', ['email' => $emailAddress])));
				}
			}

			if ($repeat) {
				$emailAddress = $io->ask($this->translator->translate('inputs.email.title'));
			}

		} while ($repeat);

		try {
			// Start transaction connection to the database
			$this->getOrmConnection()->beginTransaction();

			$create = new Utils\ArrayHash();
			$create->offsetSet('entity', Entities\Accounts\UserAccount::class);
			$create->offsetSet('status', Types\AccountStatusType::get(Types\AccountStatusType::STATE_ACTIVATED));

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

			$io->text(sprintf('<info>%s</info>', $this->translator->translate('success.account', ['name' => $account->getName()])));

		} catch (Throwable $ex) {
			// Revert all changes when error occur
			$this->getOrmConnection()->rollBack();

			$this->logger->error($ex->getMessage());

			$io->text(sprintf('<error>%s</error>', $this->translator->translate('validation.account.wasNotCreated', ['error' => $ex->getMessage()])));

			return $ex->getCode();
		}

		$createIdentity = $io->choice(
			$this->translator->translate('texts.createIdentity'),
			[
				'Yes',
				'No',
			],
			'Yes'
		);

		if ($createIdentity === 'Yes') {
			$password = $io->ask($this->translator->translate('inputs.password.title'));

			if ($account->getEmail() === null) {
				$io->text(sprintf('<info>%s</info>', $this->translator->translate('validation.identity.noEmail')));

				return 0;
			}

			try {
				// Start transaction connection to the database
				$this->getOrmConnection()->beginTransaction();

				// Create new email entity for user
				$create = new Utils\ArrayHash();
				$details->offsetSet('entity', Entities\Identities\UserAccountIdentity::class);
				$create->offsetSet('account', $account);
				$create->offsetSet('uid', $account->getEmail());
				$create->offsetSet('password', $password);
				$create->offsetSet('status', Types\IdentityStatusType::get(Types\IdentityStatusType::STATE_ACTIVE));

				$this->identitiesManager->create($create);

				// Commit all changes into database
				$this->getOrmConnection()->commit();

				$io->text(sprintf('<info>%s</info>', $this->translator->translate('success.identity', ['name' => $account->getName()])));

			} catch (Throwable $ex) {
				// Revert all changes when error occur
				$this->getOrmConnection()->rollBack();

				$this->logger->error($ex->getMessage());

				$io->text(sprintf('<error>%s</error>', $this->translator->translate('validation.identity.wasNotCreated', ['error' => $ex->getMessage()])));

				return $ex->getCode();
			}
		}

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
