<?php declare(strict_types = 1);

/**
 * GenerateTokenCommand.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Commands
 * @since          0.1.0
 *
 * @date           22.07.20
 */

namespace FastyBird\AuthNode\Commands\Tests;

use DateTimeInterface;
use FastyBird\AuthNode\Entities;
use FastyBird\AuthNode\Models;
use FastyBird\AuthNode\Queries;
use FastyBird\NodeAuth\Security as NodeAuthSecurity;
use Nette\Utils;
use Symfony\Component\Console;
use Symfony\Component\Console\Input;
use Symfony\Component\Console\Output;
use Symfony\Component\Console\Style;

/**
 * Generate token command
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Commands
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class GenerateTokenCommand extends Console\Command\Command
{

	private const TYPE_ACCESS = 'access';
	private const TYPE_REFRESH = 'refresh';

	/** @var Models\Identities\IIdentityRepository */
	private $identityRepository;

	/** @var NodeAuthSecurity\TokenBuilder */
	private $tokenBuilder;

	public function __construct(
		Models\Identities\IIdentityRepository $identityRepository,
		NodeAuthSecurity\TokenBuilder $tokenBuilder,
		?string $name = null
	) {
		$this->identityRepository = $identityRepository;
		$this->tokenBuilder = $tokenBuilder;

		parent::__construct($name);
	}

	/**
	 * {@inheritDoc}
	 */
	protected function configure(): void
	{
		$this
			->setName('fb:auth-node:tests:token:generate')
			->addArgument('type', Input\InputArgument::OPTIONAL, 'Token type')
			->addArgument('uid', Input\InputArgument::OPTIONAL, 'Account identity identifier')
			->addArgument('validTill', Input\InputArgument::OPTIONAL, 'Token expiration')
			->addOption('noconfirm', null, Input\InputOption::VALUE_NONE, 'do not ask for any confirmation')
			->setDescription('Create account.');
	}

	/**
	 * {@inheritDoc}
	 */
	protected function execute(Input\InputInterface $input, Output\OutputInterface $output)
	{
		$io = new Style\SymfonyStyle($input, $output);

		$io->title('FB auth node - generate token');

		if ($input->hasOption('type') && $input->getOption('type') !== '') {
			$type = $input->getOption('type');

		} else {
			$type = $io->choice(
				'Select token type to generate',
				[
					self::TYPE_ACCESS,
					self::TYPE_REFRESH,
				],
				self::TYPE_ACCESS
			);
		}

		if (!in_array($type, [self::TYPE_ACCESS, self::TYPE_REFRESH], true)) {
			$io->text(sprintf('<error>%s</error>', 'Provided token type is not valid.'));

			return 1;
		}

		if ($input->hasOption('uid') && $input->getOption('uid') !== '') {
			$uid = $input->getOption('uid');

		} else {
			$uid = $io->ask('Provide account identity identifier');
		}

		$findIdentity = new Queries\FindIdentitiesQuery();
		$findIdentity->byUid($uid);

		$identity = $this->identityRepository->findOneBy($findIdentity);

		if ($identity === null) {
			$io->text(sprintf('<error>%s</error>', 'Provided uid was not found.'));

			return 1;
		}

		$account = $identity->getAccount();

		if ($input->hasOption('validTill') && $input->getOption('validTill') !== '') {
			$validTill = $input->getOption('validTill');

		} else {
			$validTill = $io->ask('When should token expire? (Y-m-d\TH:i:s)');

			if ($validTill === false) {
				$io->text(sprintf('<error>%s</error>', 'Provided date is not valid.'));

				return 1;
			}
		}

		if ($validTill === '') {
			$validTill = null;

		} else {
			$validTill = Utils\DateTime::createFromFormat(DATE_ATOM, $validTill);
		}

		if (!$validTill instanceof DateTimeInterface) {
			$validTill = null;
		}

		$token = $this->tokenBuilder->build(
			$account->getPlainId(),
			array_map(function (Entities\Roles\IRole $role): string {
				return $role->getRoleId();
			}, $account->getRoles()),
			$validTill
		);

		$io->text(sprintf('<info>%s</info>', 'Token was generated: ' . (string) $token));

		return 0;
	}

}
