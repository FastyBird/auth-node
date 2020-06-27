<?php declare(strict_types = 1);

/**
 * VernemqCommand.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Commands
 * @since          0.1.0
 *
 * @date           26.06.20
 */

namespace FastyBird\AuthNode\Commands\Synchronisation;

use Contributte\Translation;
use Doctrine\Common;
use Doctrine\DBAL\Connection;
use FastyBird\AuthNode\Entities;
use FastyBird\AuthNode\Exceptions;
use FastyBird\AuthNode\Models;
use FastyBird\AuthNode\Queries;
use Nette\Utils;
use Psr\Log\LoggerInterface;
use stdClass;
use Symfony\Component\Console;
use Symfony\Component\Console\Input;
use Symfony\Component\Console\Output;
use Symfony\Component\Console\Style;
use Throwable;

/**
 * Synchronize Verne MQ accounts
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Commands
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class VernemqCommand extends Console\Command\Command
{

	/** @var Models\Accounts\IAccountRepository */
	private $accountRepository;

	/** @var Models\Vernemq\IAccountRepository */
	private $verneAccountRepository;

	/** @var Models\Vernemq\IAccountsManager */
	private $verneAccountsManager;

	/** @var Common\Persistence\ManagerRegistry */
	private $managerRegistry;

	/** @var LoggerInterface */
	private $logger;

	/** @var Translation\PrefixedTranslator */
	private $translator;

	/** @var string */
	private $translationDomain = 'node.commands.sync';

	public function __construct(
		Models\Accounts\IAccountRepository $accountRepository,
		Models\Vernemq\IAccountRepository $verneAccountRepository,
		Models\Vernemq\IAccountsManager $verneAccountsManager,
		Translation\Translator $translator,
		Common\Persistence\ManagerRegistry $managerRegistry,
		LoggerInterface $logger,
		?string $name = null
	) {
		// Modules models
		$this->accountRepository = $accountRepository;
		$this->verneAccountRepository = $verneAccountRepository;
		$this->verneAccountsManager = $verneAccountsManager;

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
			->setName('fb:auth-node:sync:vernemq')
			->addOption('noconfirm', null, Input\InputOption::VALUE_NONE, 'do not ask for any confirmation')
			->setDescription('Synchronize Verne MQ accounts.')
			->setHelp('This command synchronize all accounts with Verne MQ');
	}

	/**
	 * {@inheritDoc}
	 */
	protected function execute(Input\InputInterface $input, Output\OutputInterface $output)
	{
		$io = new Style\SymfonyStyle($input, $output);

		$io->title('FB auth node - Permissions synchronization');

		$findAccount = new Queries\FindAccountsQuery();

		$accounts = $this->accountRepository->findAllBy($findAccount);

		foreach ($accounts as $account) {
			if (
				$account instanceof Entities\Accounts\IMachineAccount
				|| $account instanceof Entities\Accounts\INodeAccount
			) {
				$identities = $account->getIdentities();

				$identity = reset($identities);

				if (
					$identity instanceof Entities\Identities\IMachineAccountIdentity
					|| $identity instanceof Entities\Identities\INodeAccountIdentity
				) {
					$findAccount = new Queries\FindVerneMqAccountsQuery();
					$findAccount->forAccount($account);

					$verneMqAccount = $this->verneAccountRepository->findOneBy($findAccount);

					if ($verneMqAccount === null) {
						$publishRule = new stdClass();
						$publishRule->pattern = '/fb/' . $identity->getUid() . '/#';

						$subscribeRule = new stdClass();
						$subscribeRule->pattern = '/fb/' . $identity->getUid() . '/#';

						$create = Utils\ArrayHash::from([
							'username'     => $identity->getUid(),
							'password'     => $identity->getPassword(),
							'account'      => $account,
							'publishAcl'   => [$publishRule],
							'subscribeAcl' => [$subscribeRule],
						]);

						try {
							$verneMqAccount = $this->verneAccountsManager->create($create);

							$io->text(sprintf('<bg=green;options=bold> Created </> <info>%s</info>', $verneMqAccount->getUsername()));

						} catch (Throwable $ex) {
							// Revert all changes when error occur
							$this->getOrmConnection()->rollBack();

							$this->logger->error($ex->getMessage());

							$io->text(sprintf('<error>%s</error>', $this->translator->translate('validation.sync.notFinished', ['error' => $ex->getMessage()])));
						}

					} else {
						$update = Utils\ArrayHash::from([
							'username' => $identity->getUid(),
							'password' => $identity->getPassword(),
						]);

						try {
							$this->verneAccountsManager->update($verneMqAccount, $update);

							$io->text(sprintf('<bg=green;options=bold> Updated </> <info>%s</info>', $verneMqAccount->getUsername()));

						} catch (Throwable $ex) {
							// Revert all changes when error occur
							$this->getOrmConnection()->rollBack();

							$this->logger->error($ex->getMessage());

							$io->text(sprintf('<error>%s</error>', $this->translator->translate('validation.notFinished', ['error' => $ex->getMessage()])));
						}
					}
				}
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
