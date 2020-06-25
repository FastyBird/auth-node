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
	 * Node ACL
	 */

	// Permissions string delimiter
	public const PERMISSIONS_DELIMITER = ':';

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

	public const ROUTE_NAME_ACCOUNT = 'account';
	public const ROUTE_NAME_ACCOUNT_RELATIONSHIP = 'account.relationship';
	public const ROUTE_NAME_ACCOUNT_EMAILS = 'account.emails';
	public const ROUTE_NAME_ACCOUNT_EMAIL = 'account.email';
	public const ROUTE_NAME_ACCOUNT_EMAIL_RELATIONSHIP = 'account.email.relationship';
	public const ROUTE_NAME_ACCOUNT_IDENTITIES = 'account.identities';
	public const ROUTE_NAME_ACCOUNT_IDENTITY = 'account.identity';
	public const ROUTE_NAME_ACCOUNT_IDENTITY_RELATIONSHIP = 'account.identity.relationship';

	public const ROUTE_NAME_SESSION = 'session';
	public const ROUTE_NAME_SESSION_RELATIONSHIP = 'session.relationship';

	public const ROUTE_NAME_ROLE = 'role';
	public const ROUTE_NAME_ROLES = 'roles';
	public const ROUTE_NAME_ROLE_RELATIONSHIP = 'role.relationship';
	public const ROUTE_NAME_ROLE_CHILDREN = 'role.children';
	public const ROUTE_NAME_ROLE_RULES = 'role.rules';

	public const ROUTE_NAME_RESOURCE = 'resource';
	public const ROUTE_NAME_RESOURCES = 'resources';
	public const ROUTE_NAME_RESOURCE_RELATIONSHIP = 'resource.relationship';
	public const ROUTE_NAME_RESOURCE_CHILDREN = 'resource.children';
	public const ROUTE_NAME_RESOURCE_PRIVILEGES = 'resource.privileges';

	public const ROUTE_NAME_PRIVILEGE = 'privilege';
	public const ROUTE_NAME_PRIVILEGES = 'privileges';
	public const ROUTE_NAME_PRIVILEGE_RELATIONSHIP = 'privilege.relationship';

	public const ROUTE_NAME_RULE = 'rule';
	public const ROUTE_NAME_RULES = 'rules';
	public const ROUTE_NAME_RULE_RELATIONSHIP = 'rule.relationship';

	/**
	 * Account identities
	 */

	public const IDENTITY_UID_MAXIMAL_LENGTH = 50;
	public const IDENTITY_PASSWORD_MINIMAL_LENGTH = 8;

}
