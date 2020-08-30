<?php declare(strict_types = 1);

/**
 * IRefreshToken.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Entities
 * @since          0.1.0
 *
 * @date           30.03.20
 */

namespace FastyBird\AuthNode\Entities\Tokens;

use DateTimeInterface;
use FastyBird\NodeAuth\Entities as NodeAuthEntities;
use FastyBird\NodeDatabase\Entities as NodeDatabaseEntities;
use IPub\DoctrineCrud;
use IPub\DoctrineTimestampable;

/**
 * Security refresh token entity interface
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IRefreshToken extends NodeAuthEntities\Tokens\IToken,
	DoctrineCrud\Entities\IIdentifiedEntity,
	NodeDatabaseEntities\IEntityParams,
	DoctrineTimestampable\Entities\IEntityCreated,
	DoctrineTimestampable\Entities\IEntityUpdated
{

	public const TOKEN_EXPIRATION = '+3 days';

	/**
	 * @return IAccessToken
	 */
	public function getAccessToken(): IAccessToken;

	/**
	 * @return DateTimeInterface
	 */
	public function getValidTill(): ?DateTimeInterface;

	/**
	 * @param DateTimeInterface $dateTime
	 *
	 * @return bool
	 */
	public function isValid(DateTimeInterface $dateTime): bool;

}
