<?php declare(strict_types = 1);

/**
 * Constants.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     common
 * @since          0.1.0
 *
 * @date           12.06.20
 */

namespace FastyBird\AuthNode;

use FastyBird\AuthNode\Entities as AuthNodeEntities;

/**
 * Service constants
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     common
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class Constants
{

	/**
	 * Node routing
	 */

	public const ROUTE_NAME_ME = 'me';
	public const ROUTE_NAME_ME_RELATIONSHIP = 'me.relationship';
	public const ROUTE_NAME_ME_EMAILS = 'me.emails';
	public const ROUTE_NAME_ME_EMAIL = 'me.email';
	public const ROUTE_NAME_ME_EMAIL_RELATIONSHIP = 'me.email.relationship';
	public const ROUTE_NAME_ME_IDENTITIES = 'me.identities';
	public const ROUTE_NAME_ME_IDENTITY = 'me.identity';
	public const ROUTE_NAME_ME_IDENTITY_RELATIONSHIP = 'me.identity.relationship';

	public const ROUTE_NAME_ACCOUNTS = 'accounts';
	public const ROUTE_NAME_ACCOUNT = 'account';
	public const ROUTE_NAME_ACCOUNT_RELATIONSHIP = 'account.relationship';
	public const ROUTE_NAME_ACCOUNT_EMAILS = 'account.emails';
	public const ROUTE_NAME_ACCOUNT_EMAIL = 'account.email';
	public const ROUTE_NAME_ACCOUNT_EMAIL_RELATIONSHIP = 'account.email.relationship';
	public const ROUTE_NAME_ACCOUNT_IDENTITIES = 'account.identities';
	public const ROUTE_NAME_ACCOUNT_IDENTITY = 'account.identity';
	public const ROUTE_NAME_ACCOUNT_IDENTITY_RELATIONSHIP = 'account.identity.relationship';
	public const ROUTE_NAME_ACCOUNT_CHILDREN = 'account.children';

	public const ROUTE_NAME_SESSION = 'session';
	public const ROUTE_NAME_SESSION_RELATIONSHIP = 'session.relationship';

	public const ROUTE_NAME_ROLE = 'role';
	public const ROUTE_NAME_ROLES = 'roles';
	public const ROUTE_NAME_ROLE_RELATIONSHIP = 'role.relationship';
	public const ROUTE_NAME_ROLE_CHILDREN = 'role.children';

	/**
	 * Account identities
	 */

	public const IDENTITY_UID_MAXIMAL_LENGTH = 50;
	public const IDENTITY_PASSWORD_MINIMAL_LENGTH = 8;

	/**
	 * Message bus routing keys mapping
	 */
	public const RABBIT_MQ_ENTITIES_ROUTING_KEYS_MAPPING = [
		AuthNodeEntities\Accounts\Account::class    => 'fb.bus.node.entity.[ACTION].account',
		AuthNodeEntities\Emails\Email::class        => 'fb.bus.node.entity.[ACTION].email',
		AuthNodeEntities\Identities\Identity::class => 'fb.bus.node.entity.[ACTION].identity',
	];

	public const RABBIT_MQ_ENTITIES_ROUTING_KEY_ACTION_REPLACE_STRING = '[ACTION]';

	/**
	 * Message bus routing key for devices & channels properties messages
	 */

	// Devices
	public const RABBIT_MQ_DEVICES_CREATED_ENTITY_ROUTING_KEY = 'fb.bus.node.entity.created.device';
	public const RABBIT_MQ_DEVICES_DELETED_ENTITY_ROUTING_KEY = 'fb.bus.node.entity.deleted.device';

	/**
	 * Microservices origins
	 */

	public const NODE_DEVICES_ORIGIN = 'com.fastybird.devices-node';

}
