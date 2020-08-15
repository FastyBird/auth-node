<?php declare(strict_types = 1);

/**
 * PropertyNotExistsException.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Exceptions
 * @since          0.1.0
 *
 * @date           15.08.20
 */

namespace FastyBird\AuthNode\Exceptions;

use Exception as PHPException;

class PropertyNotExistsException extends PHPException implements IException
{

}
