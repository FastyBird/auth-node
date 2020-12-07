<?php declare(strict_types = 1);

/**
 * Constants.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     common
 * @since          0.1.0
 *
 * @date           12.06.20
 */

namespace FastyBird\AuthNode;

use FastyBird\AuthModule\Entities as AuthModuleEntities;
use FastyBird\SimpleAuth;

/**
 * Node constants
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     common
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class Constants
{

	/**
	 * Accounts default roles
	 */

	public const USER_ACCOUNT_DEFAULT_ROLES = [
		SimpleAuth\Constants::ROLE_USER,
	];

	public const MACHINE_ACCOUNT_DEFAULT_ROLES = [
		SimpleAuth\Constants::ROLE_USER,
	];

	public const NODE_ACCOUNT_DEFAULT_ROLES = [
		SimpleAuth\Constants::ROLE_USER,
	];

	/**
	 * Account identities
	 */

	public const IDENTITY_UID_MAXIMAL_LENGTH = 50;
	public const IDENTITY_PASSWORD_MINIMAL_LENGTH = 8;

	/**
	 * Message bus routing keys mapping
	 */
	public const RABBIT_MQ_ENTITIES_ROUTING_KEYS_MAPPING = [
		AuthModuleEntities\Accounts\Account::class    => 'fb.bus.node.entity.[ACTION].account',
		AuthModuleEntities\Emails\Email::class        => 'fb.bus.node.entity.[ACTION].email',
		AuthModuleEntities\Identities\Identity::class => 'fb.bus.node.entity.[ACTION].identity',
	];

	public const RABBIT_MQ_ENTITIES_ROUTING_KEY_ACTION_REPLACE_STRING = '[ACTION]';

	/**
	 * Message bus routing key for devices & channels properties messages
	 */

	// Devices
	public const RABBIT_MQ_DEVICES_CREATED_ENTITY_ROUTING_KEY = 'fb.bus.node.entity.created.device';
	public const RABBIT_MQ_DEVICES_DELETED_ENTITY_ROUTING_KEY = 'fb.bus.node.entity.deleted.device';

	/**
	 * Message bus origins
	 */

	public const RABBIT_MQ_DEVICES_ORIGIN = 'com.fastybird.devices-node';

}
