<?php declare(strict_types = 1);

/**
 * NodeAccount.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Entities
 * @since          0.1.0
 *
 * @date           26.06.20
 */

namespace FastyBird\AuthNode\Entities\Accounts;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="fb_accounts_nodes",
 *     options={
 *       "collate"="utf8mb4_general_ci",
 *       "charset"="utf8mb4",
 *       "comment"="Node accounts"
 *     }
 * )
 */
class NodeAccount extends Account implements INodeAccount
{

	/**
	 * {@inheritDoc}
	 */
	public function toArray(): array
	{
		return array_merge(parent::toArray(), [
			'type' => 'node',
		]);
	}

}
