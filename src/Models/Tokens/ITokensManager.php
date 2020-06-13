<?php declare(strict_types = 1);

/**
 * ITokensManager.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Models
 * @since          0.1.0
 *
 * @date           31.03.20
 */

namespace FastyBird\AuthNode\Models\Tokens;

use FastyBird\AuthNode\Entities;
use FastyBird\AuthNode\Models;
use Nette\Utils;

/**
 * Access tokens entities manager interface
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface ITokensManager
{

	/**
	 * @param Utils\ArrayHash $values
	 *
	 * @return Entities\Tokens\IToken
	 */
	public function create(
		Utils\ArrayHash $values
	): Entities\Tokens\IToken;

	/**
	 * @param Entities\Tokens\IToken $entity
	 * @param Utils\ArrayHash $values
	 *
	 * @return Entities\Tokens\IToken
	 */
	public function update(
		Entities\Tokens\IToken $entity,
		Utils\ArrayHash $values
	): Entities\Tokens\IToken;

	/**
	 * @param Entities\Tokens\IToken $entity
	 *
	 * @return bool
	 */
	public function delete(
		Entities\Tokens\IToken $entity
	): bool;

}
