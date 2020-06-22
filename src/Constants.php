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

	// Permissions string delimiter
	public const PERMISSIONS_DELIMITER = ':';

	// Identity constants
	public const IDENTITY_UID_MAXIMAL_LENGTH = 50;
	public const IDENTITY_PASSWORD_MINIMAL_LENGTH = 8;

	// Node ACL
	public const ACL_RESOURCE_MANAGE_ACCESS = 'manage-access-control';
	public const ACL_RESOURCE_MANAGE_ACCOUNTS = 'manage-accounts';
	public const ACL_RESOURCE_MANAGE_EMAILS = 'manage-emails';
	public const ACL_RESOURCE_MANAGE_IDENTITIES = 'manage-identities';

	public const ACL_PRIVILEGE_READ = 'read';
	public const ACL_PRIVILEGE_CREATE = 'create';
	public const ACL_PRIVILEGE_UPDATE = 'update';
	public const ACL_PRIVILEGE_DELETE = 'delete';

	// Routing
	public const ROUTE_NAME_ME = 'me';
	public const ROUTE_NAME_ME_RELATIONSHIPS = 'me.relationship';
	public const ROUTE_NAME_ME_EMAILS = 'me.emails';
	public const ROUTE_NAME_ME_EMAIL = 'me.email';
	public const ROUTE_NAME_ME_EMAIL_RELATIONSHIPS = 'me.email.relationship';
	public const ROUTE_NAME_ME_SECURITY_QUESTION = 'me.security.question';
	public const ROUTE_NAME_ME_SECURITY_QUESTION_RELATIONSHIPS = 'me.security.question.relationship';
	public const ROUTE_NAME_ME_IDENTITIES = 'me.identities';
	public const ROUTE_NAME_ME_IDENTITY = 'me.identity';
	public const ROUTE_NAME_ME_IDENTITY_RELATIONSHIPS = 'me.identity.relationship';
	public const ROUTE_NAME_ME_ROLES = 'me.roles';

	public const ROUTE_NAME_ACCOUNT = 'account';
	public const ROUTE_NAME_ACCOUNT_RELATIONSHIPS = 'account.relationship';
	public const ROUTE_NAME_ACCOUNT_EMAILS = 'account.emails';
	public const ROUTE_NAME_ACCOUNT_EMAIL = 'account.email';
	public const ROUTE_NAME_ACCOUNT_EMAIL_RELATIONSHIPS = 'account.email.relationship';
	public const ROUTE_NAME_ACCOUNT_SECURITY_QUESTION = 'account.security.question';
	public const ROUTE_NAME_ACCOUNT_SECURITY_QUESTION_RELATIONSHIPS = 'account.security.question.relationship';
	public const ROUTE_NAME_ACCOUNT_IDENTITIES = 'account.identities';
	public const ROUTE_NAME_ACCOUNT_IDENTITY = 'account.identity';
	public const ROUTE_NAME_ACCOUNT_IDENTITY_RELATIONSHIPS = 'account.identity.relationship';
	public const ROUTE_NAME_ACCOUNT_ROLES = 'account.roles';

}
