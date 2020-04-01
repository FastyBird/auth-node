<?php declare(strict_types = 1);

/**
 * AccountNotFoundException.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AccountsNode!
 * @subpackage     Exceptions
 * @since          0.1.0
 *
 * @date           30.03.20
 */

namespace FastyBird\AccountsNode\Exceptions;

use Nette\Security as NS;

class AccountNotFoundException extends NS\AuthenticationException implements IException
{

}
