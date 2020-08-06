<?php declare(strict_types = 1);

/**
 * IAccessToken.php
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
use FastyBird\AuthNode\Entities;
use FastyBird\NodeAuth\Entities as NodeAuthEntities;
use FastyBird\NodeDatabase\Entities as NodeDatabaseEntities;
use IPub\DoctrineBlameable;
use IPub\DoctrineCrud;
use IPub\DoctrineTimestampable;

/**
 * Account access token entity interface
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IAccessToken extends NodeAuthEntities\Tokens\IToken,
	DoctrineCrud\Entities\IIdentifiedEntity,
	NodeDatabaseEntities\IEntityParams,
	DoctrineTimestampable\Entities\IEntityCreated,
	DoctrineBlameable\Entities\IEntityCreator,
	DoctrineTimestampable\Entities\IEntityUpdated,
	DoctrineBlameable\Entities\IEntityEditor
{

	public const TOKEN_EXPIRATION = '+6 hours';

	/**
	 * @param IRefreshToken $refreshToken
	 *
	 * @return void
	 */
	public function setRefreshToken(IRefreshToken $refreshToken): void;

	/**
	 * @return IRefreshToken|null
	 */
	public function getRefreshToken(): ?IRefreshToken;

	/**
	 * @return Entities\Identities\IIdentity
	 */
	public function getIdentity(): Entities\Identities\IIdentity;

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
