<?php declare(strict_types = 1);

/**
 * Authenticator.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AccountsNode!
 * @subpackage     Security
 * @since          0.1.0
 *
 * @date           31.03.20
 */

namespace FastyBird\AccountsNode\Security;

use FastyBird\AccountsNode\Entities;
use FastyBird\AccountsNode\Exceptions;
use FastyBird\AccountsNode\Models;
use FastyBird\AccountsNode\Types;
use Nette\Security as NS;

/**
 * Account authentication
 *
 * @package        FastyBird:AccountsNode!
 * @subpackage     Security
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class Authenticator implements NS\IAuthenticator
{

	public const IDENTITY_USERNAME_NOT_FOUND = 110;
	public const IDENTITY_EMAIL_NOT_FOUND = 120;

	public const INVALID_CREDENTIAL_FOR_USERNAME = 101;
	public const INVALID_CREDENTIAL_FOR_EMAIL = 102;

	public const ACCOUNT_PROFILE_BLOCKED = 210;
	public const ACCOUNT_PROFILE_DELETED = 220;

	/** @var Models\Identities\IIdentityRepository */
	private $identityRepository;

	public function __construct(
		Models\Identities\IIdentityRepository $identityRepository
	) {
		$this->identityRepository = $identityRepository;
	}

	/**
	 * Performs an system authentication.
	 *
	 * @param mixed[] $credentials
	 *
	 * @return Entities\Identities\System
	 *
	 * @throws Exceptions\AccountNotFoundException
	 * @throws Exceptions\AuthenticationFailedException
	 */
	public function authenticate(array $credentials): NS\IIdentity
	{
		[$username, $password] = $credentials + [null, null];

		if (strpos($username, '@') === false) {
			/** @var Entities\Identities\System|null $identity */
			$identity = $this->identityRepository->findOneByUid($username, Entities\Identities\System::class);

		} else {
			/** @var Entities\Identities\System|null $identity */
			$identity = $this->identityRepository->findOneByEmail($username, Entities\Identities\System::class);
		}

		if ($identity === null) {
			if (strpos($username, '@') === false) {
				throw new Exceptions\AccountNotFoundException('The username is incorrect', self::IDENTITY_USERNAME_NOT_FOUND);

			} else {
				throw new Exceptions\AccountNotFoundException('The email address is incorrect', self::IDENTITY_EMAIL_NOT_FOUND);
			}
		}

		// Check if password is ok
		if (!$identity->verifyPassword((string) $password)) {
			if (strpos($username, '@') === false) {
				throw new Exceptions\AuthenticationFailedException('The password is incorrect', self::INVALID_CREDENTIAL_FOR_USERNAME);

			} else {
				throw new Exceptions\AuthenticationFailedException('The password is incorrect', self::INVALID_CREDENTIAL_FOR_EMAIL);
			}
		}

		$account = $identity->getAccount();

		if ($account->getStatus()->equalsValue(Types\AccountStatusType::STATE_BLOCKED)) {
			throw new Exceptions\AuthenticationFailedException('Account profile is blocked', self::ACCOUNT_PROFILE_BLOCKED);

		} elseif ($account->getStatus()->equalsValue(Types\AccountStatusType::STATE_DELETED)) {
			throw new Exceptions\AuthenticationFailedException('Account profile is deleted', self::ACCOUNT_PROFILE_DELETED);
		}

		return $identity;
	}

}
