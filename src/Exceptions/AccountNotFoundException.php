<?php declare(strict_types = 1);

/**
 * AccountNotFoundException.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Exceptions
 * @since          0.1.0
 *
 * @date           30.03.20
 */

namespace FastyBird\AuthNode\Exceptions;

use FastyBird\NodeAuth\Exceptions as NodeAuthExceptions;

class AccountNotFoundException extends NodeAuthExceptions\AuthenticationException implements IException
{

}
