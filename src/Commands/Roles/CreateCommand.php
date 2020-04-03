<?php declare(strict_types = 1);

/**
 * CreateCommand.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AccountsNode!
 * @subpackage     Commands
 * @since          0.1.0
 *
 * @date           31.03.20
 */

namespace FastyBird\AccountsNode\Commands\Roles;

use Contributte\Translation;
use FastyBird\AccountsNode\Models;
use Nette\Utils;
use Symfony\Component\Console;
use Symfony\Component\Console\Input;
use Symfony\Component\Console\Output;
use Symfony\Component\Console\Style;
use Throwable;

/**
 * ACL role creation command
 *
 * @package        FastyBird:AccountsNode!
 * @subpackage     Commands
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class CreateCommand extends Console\Command\Command
{

	/** @var Models\Roles\IRoleRepository */
	private $roleRepository;

	/** @var Models\Roles\IRolesManager */
	private $rolesManager;

	/** @var Translation\PrefixedTranslator */
	private $translator;

	/** @var string */
	private $translationDomain = 'node.commands.roleCreate';

	public function __construct(
		Models\Roles\IRoleRepository $roleRepository,
		Models\Roles\IRolesManager $rolesManager,
		Translation\Translator $translator,
		?string $name = null
	) {
		// Modules models
		$this->roleRepository = $roleRepository;
		$this->rolesManager = $rolesManager;

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
			->setName('fb:accounts-node:roles:create')
			->addArgument('key', Input\InputArgument::OPTIONAL, $this->translator->translate('key.title'))
			->addArgument('name', Input\InputArgument::OPTIONAL, $this->translator->translate('name.title'))
			->addArgument('priority', Input\InputArgument::OPTIONAL, $this->translator->translate('priority.title'))
			->addOption('noconfirm', null, Input\InputOption::VALUE_NONE, 'do not ask for any confirmation')
			->setDescription('Create role.');
	}

	/**
	 * {@inheritDoc}
	 */
	protected function execute(Input\InputInterface $input, Output\OutputInterface $output)
	{
		$io = new Style\SymfonyStyle($input, $output);

		$io->title('FB accounts node - create role');

		if ($input->hasOption('key') && $input->getOption('key')) {
			$keyName = $input->getOption('key');

		} else {
			$keyName = $io->ask($this->translator->translate('key.title'));
		}

		do {
			$role = $this->roleRepository->findOneByKeyName($keyName);

			$repeat = $role !== null;

			if ($repeat) {
				$io->text(sprintf('<error>%s</error>', $this->translator->translate('validation.role.exists', ['role' => $keyName])));

				$keyName = $io->ask($this->translator->translate('key.title'));
			}

		} while ($repeat);

		if ($input->hasOption('name') && $input->getOption('name')) {
			$name = $input->getOption('name');

		} else {
			$name = $io->ask($this->translator->translate('name.title'));
		}

		try {
			$create = new Utils\ArrayHash();
			$create->keyName = $keyName;
			$create->name = $name;
			$create->priority = $input->getArgument('priority') ?: 0;

			$role = $this->rolesManager->create($create);

			$io->text(sprintf('<info>%s</info>', $this->translator->translate('success', ['name' => $role->getName(), 'key' => $role->getRoleId()])));

		} catch (Throwable $ex) {
			$io->text(sprintf('<error>%s</error>', $this->translator->translate('validation.role.wasNotCreated', ['error' => $ex->getMessage()])));
		}

		return 0;
	}

}
