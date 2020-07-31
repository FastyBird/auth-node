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

use Contributte\Translation;
use Doctrine\Common;
use Doctrine\DBAL\Connection;
use FastyBird\AuthNode\Exceptions;
use FastyBird\AuthNode\Models;
use FastyBird\AuthNode\Queries;
use FastyBird\NodeAuth;
use Nette\Utils;
use Psr\Log\LoggerInterface;
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

	/** @var Models\Roles\IRoleRepository */
	private $roleRepository;

	/** @var Models\Roles\IRolesManager */
	private $rolesManager;

	/** @var Common\Persistence\ManagerRegistry */
	private $managerRegistry;

	/** @var LoggerInterface */
	private $logger;

	/** @var Translation\PrefixedTranslator */
	private $translator;

	/** @var string */
	private $translationDomain = 'node.commands.initialize';

	public function __construct(
		Models\Roles\IRoleRepository $roleRepository,
		Models\Roles\IRolesManager $rolesManager,
		Translation\Translator $translator,
		Common\Persistence\ManagerRegistry $managerRegistry,
		LoggerInterface $logger,
		?string $name = null
	) {
		$this->roleRepository = $roleRepository;
		$this->rolesManager = $rolesManager;

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
			->setName('fb:auth-node:initialize')
			->addOption('noconfirm', null, Input\InputOption::VALUE_NONE, 'do not ask for any confirmation')
			->setDescription('Initialize node.');
	}

	/**
	 * {@inheritDoc}
	 */
	protected function execute(Input\InputInterface $input, Output\OutputInterface $output)
	{
		$io = new Style\SymfonyStyle($input, $output);

		$io->title('FB auth node - initialization');

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
			$this->getOrmConnection()->rollBack();

			$this->logger->error($ex->getMessage());

			$io->text(sprintf('<error>%s</error>', $this->translator->translate('validation.role.wasNotCreated')));

			return $ex->getCode();
		}

		$io->text(sprintf('<info>%s</info>', $this->translator->translate('success')));

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
