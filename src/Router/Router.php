<?php declare(strict_types = 1);

/**
 * RouterFactory.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Router
 * @since          0.1.0
 *
 * @date           31.03.20
 */

namespace FastyBird\AuthNode\Router;

use FastyBird\AuthNode;
use FastyBird\AuthNode\Controllers;
use FastyBird\AuthNode\Middleware;
use IPub\SlimRouter\Routing;
use Psr\Http\Message\ResponseFactoryInterface;

/**
 * Node router configuration
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Router
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class Router extends Routing\Router
{

	public const URL_ITEM_ID = 'id';

	public const URL_ACCOUNT_ID = 'account';

	public const RELATION_ENTITY = 'relationEntity';

	/** @var Controllers\SessionV1Controller */
	private $sessionV1Controller;

	/** @var Controllers\AccountV1Controller */
	private $accountV1Controller;

	/** @var Controllers\AccountEmailsV1Controller */
	private $accountEmailsV1Controller;

	/** @var Controllers\AccountIdentitiesV1Controller */
	private $accountIdentitiesV1Controller;

	/** @var Controllers\RolesV1Controller */
	private $rolesV1Controller;

	/** @var Controllers\RoleChildrenV1Controller */
	private $roleChildrenV1Controller;

	/** @var Controllers\RoleRulesV1Controller */
	private $roleRulesV1Controller;

	/** @var Controllers\ResourcesV1Controller */
	private $resourcesV1Controller;

	/** @var Controllers\ResourceChildrenV1Controller */
	private $resourceChildrenV1Controller;

	/** @var Controllers\ResourcePrivilegesV1Controller */
	private $resourcePrivilegesV1Controller;

	/** @var Controllers\PrivilegesV1Controller */
	private $privilegesV1Controller;

	/** @var Controllers\RulesV1Controller */
	private $rulesV1Controller;

	/** @var Middleware\AccessMiddleware */
	private $accessControlMiddleware;

	/** @var Controllers\AccountsV1Controller */
	private $accountsV1Controller;

	/** @var Controllers\EmailsV1Controller */
	private $emailsV1Controller;

	/** @var Controllers\IdentitiesV1Controller */
	private $identitiesV1Controller;

	public function __construct(
		Controllers\SessionV1Controller $sessionV1Controller,
		Controllers\AccountV1Controller $accountV1Controller,
		Controllers\AccountEmailsV1Controller $accountEmailsV1Controller,
		Controllers\AccountIdentitiesV1Controller $accountIdentitiesV1Controller,
		Controllers\AccountsV1Controller $accountsV1Controller,
		Controllers\EmailsV1Controller $emailsV1Controller,
		Controllers\IdentitiesV1Controller $identitiesV1Controller,
		Controllers\RolesV1Controller $rolesV1Controller,
		Controllers\RoleChildrenV1Controller $roleChildrenV1Controller,
		Controllers\RoleRulesV1Controller $roleRulesV1Controller,
		Controllers\ResourcesV1Controller $resourcesV1Controller,
		Controllers\ResourceChildrenV1Controller $resourceChildrenV1Controller,
		Controllers\ResourcePrivilegesV1Controller $resourcePrivilegesV1Controller,
		Controllers\PrivilegesV1Controller $privilegesV1Controller,
		Controllers\RulesV1Controller $rulesV1Controller,
		Middleware\AccessMiddleware $accessControlMiddleware,
		?ResponseFactoryInterface $responseFactory = null
	) {
		parent::__construct($responseFactory, null);

		$this->sessionV1Controller = $sessionV1Controller;

		$this->accountV1Controller = $accountV1Controller;
		$this->accountEmailsV1Controller = $accountEmailsV1Controller;
		$this->accountIdentitiesV1Controller = $accountIdentitiesV1Controller;

		$this->accountsV1Controller = $accountsV1Controller;
		$this->emailsV1Controller = $emailsV1Controller;
		$this->identitiesV1Controller = $identitiesV1Controller;
		$this->rolesV1Controller = $rolesV1Controller;
		$this->roleChildrenV1Controller = $roleChildrenV1Controller;
		$this->roleRulesV1Controller = $roleRulesV1Controller;
		$this->resourcesV1Controller = $resourcesV1Controller;
		$this->resourceChildrenV1Controller = $resourceChildrenV1Controller;
		$this->resourcePrivilegesV1Controller = $resourcePrivilegesV1Controller;
		$this->privilegesV1Controller = $privilegesV1Controller;
		$this->rulesV1Controller = $rulesV1Controller;

		$this->accessControlMiddleware = $accessControlMiddleware;
	}

	/**
	 * @return void
	 */
	public function registerRoutes(): void
	{
		$this->group('/v1', function (Routing\RouteCollector $group): void {
			$group->post('/register', [$this->accountV1Controller, 'create']);

			$group->post('/password-reset', [$this->accountIdentitiesV1Controller, 'requestPassword']);

			$group->post('/validate-email', [$this->accountEmailsV1Controller, 'validate']);

			$group->group('/session', function (Routing\RouteCollector $group): void {
				$route = $group->get('', [$this->sessionV1Controller, 'read']);
				$route->setName(AuthNode\Constants::ROUTE_NAME_SESSION);

				$group->post('', [$this->sessionV1Controller, 'create']);

				$group->patch('', [$this->sessionV1Controller, 'update']);

				$group->delete('', [$this->sessionV1Controller, 'delete']);

				$route = $group->get('/relationships/{' . self::RELATION_ENTITY . '}', [$this->sessionV1Controller, 'readRelationship']);
				$route->setName(AuthNode\Constants::ROUTE_NAME_SESSION_RELATIONSHIP);
			});

			$group->group('/me', function (Routing\RouteCollector $group): void {
				$route = $group->get('', [$this->accountV1Controller, 'read']);
				$route->setName(AuthNode\Constants::ROUTE_NAME_ME);

				$group->patch('', [$this->accountV1Controller, 'update']);

				$group->delete('', [$this->accountV1Controller, 'delete']);

				$route = $group->get('/relationships/{' . self::RELATION_ENTITY . '}', [$this->accountV1Controller, 'readRelationship']);
				$route->setName(AuthNode\Constants::ROUTE_NAME_ME_RELATIONSHIP);

				/**
				 * PROFILE EMAILS
				 */
				$group->group('/emails', function (Routing\RouteCollector $group): void {
					$route = $group->get('', [$this->accountEmailsV1Controller, 'index']);
					$route->setName(AuthNode\Constants::ROUTE_NAME_ME_EMAILS);

					$route = $group->get('/{' . self::URL_ITEM_ID . '}', [$this->accountEmailsV1Controller, 'read']);
					$route->setName(AuthNode\Constants::ROUTE_NAME_ME_EMAIL);

					$group->post('', [$this->accountEmailsV1Controller, 'create']);

					$group->patch('/{' . self::URL_ITEM_ID . '}', [$this->accountEmailsV1Controller, 'update']);

					$group->delete('/{' . self::URL_ITEM_ID . '}', [$this->accountEmailsV1Controller, 'delete']);

					$route = $group->get('/{' . self::URL_ITEM_ID . '}/relationships/{' . self::RELATION_ENTITY . '}', [$this->accountEmailsV1Controller, 'readRelationship']);
					$route->setName(AuthNode\Constants::ROUTE_NAME_ME_EMAIL_RELATIONSHIP);
				});

				/**
				 * PROFILE IDENTITIES
				 */
				$group->group('/identities', function (Routing\RouteCollector $group): void {
					$route = $group->get('', [$this->accountIdentitiesV1Controller, 'index']);
					$route->setName(AuthNode\Constants::ROUTE_NAME_ME_IDENTITIES);

					$route = $group->get('/{' . self::URL_ITEM_ID . '}', [$this->accountIdentitiesV1Controller, 'read']);
					$route->setName(AuthNode\Constants::ROUTE_NAME_ME_IDENTITY);

					$group->patch('/{' . self::URL_ITEM_ID . '}', [$this->accountIdentitiesV1Controller, 'update']);

					$route = $group->get('/{' . self::URL_ITEM_ID . '}/relationships/{' . self::RELATION_ENTITY . '}', [$this->accountIdentitiesV1Controller, 'readRelationship']);
					$route->setName(AuthNode\Constants::ROUTE_NAME_ME_IDENTITY_RELATIONSHIP);
				});
			});

			$group->group('/accounts', function (Routing\RouteCollector $group): void {
				$route = $group->get('/{' . self::URL_ITEM_ID . '}', [$this->accountsV1Controller, 'read']);
				$route->setName(AuthNode\Constants::ROUTE_NAME_ACCOUNT);

				$group->post('', [$this->accountsV1Controller, 'create']);

				$group->patch('/{' . self::URL_ITEM_ID . '}', [$this->accountsV1Controller, 'update']);

				$group->delete('/{' . self::URL_ITEM_ID . '}', [$this->accountsV1Controller, 'delete']);

				$route = $group->get('/{' . self::URL_ITEM_ID . '}/relationships/{' . self::RELATION_ENTITY . '}', [$this->accountsV1Controller, 'readRelationship']);
				$route->setName(AuthNode\Constants::ROUTE_NAME_ACCOUNT_RELATIONSHIP);
			});

			$group->group('/accounts/{' . self::URL_ACCOUNT_ID . '}', function (Routing\RouteCollector $group): void {
				/**
				 * ACCOUNT EMAILS
				 */
				$group->group('/emails', function (Routing\RouteCollector $group): void {
					$route = $group->get('', [$this->emailsV1Controller, 'index']);
					$route->setName(AuthNode\Constants::ROUTE_NAME_ACCOUNT_EMAILS);

					$route = $group->get('/{' . self::URL_ITEM_ID . '}', [$this->emailsV1Controller, 'read']);
					$route->setName(AuthNode\Constants::ROUTE_NAME_ACCOUNT_EMAIL);

					$group->post('', [$this->emailsV1Controller, 'create']);

					$group->patch('/{' . self::URL_ITEM_ID . '}', [$this->emailsV1Controller, 'update']);

					$group->delete('/{' . self::URL_ITEM_ID . '}', [$this->emailsV1Controller, 'delete']);

					$route = $group->get('/{' . self::URL_ITEM_ID . '}/relationships/{' . self::RELATION_ENTITY . '}', [$this->emailsV1Controller, 'readRelationship']);
					$route->setName(AuthNode\Constants::ROUTE_NAME_ACCOUNT_EMAIL_RELATIONSHIP);
				});

				/**
				 * ACCOUNT IDENTITIES
				 */
				$group->group('/identities', function (Routing\RouteCollector $group): void {
					$route = $group->get('', [$this->identitiesV1Controller, 'index']);
					$route->setName(AuthNode\Constants::ROUTE_NAME_ACCOUNT_IDENTITIES);

					$route = $group->get('/{' . self::URL_ITEM_ID . '}', [$this->identitiesV1Controller, 'read']);
					$route->setName(AuthNode\Constants::ROUTE_NAME_ACCOUNT_IDENTITY);

					$group->patch('/{' . self::URL_ITEM_ID . '}', [$this->identitiesV1Controller, 'update']);

					$route = $group->get('/{' . self::URL_ITEM_ID . '}/relationships/{' . self::RELATION_ENTITY . '}', [$this->identitiesV1Controller, 'readRelationship']);
					$route->setName(AuthNode\Constants::ROUTE_NAME_ACCOUNT_IDENTITY_RELATIONSHIP);
				});
			});

			$group->group('/roles', function (Routing\RouteCollector $group): void {
				$route = $group->get('', [$this->rolesV1Controller, 'index']);
				$route->setName(AuthNode\Constants::ROUTE_NAME_ROLES);

				$route = $group->get('/{' . self::URL_ITEM_ID . '}', [$this->rolesV1Controller, 'read']);
				$route->setName(AuthNode\Constants::ROUTE_NAME_ROLE);

				$group->post('', [$this->rolesV1Controller, 'create']);

				$group->patch('/{' . self::URL_ITEM_ID . '}', [$this->rolesV1Controller, 'update']);

				$group->delete('/{' . self::URL_ITEM_ID . '}', [$this->rolesV1Controller, 'delete']);

				$route = $group->get('/{' . self::URL_ITEM_ID . '}/relationships/{' . self::RELATION_ENTITY . '}', [$this->rolesV1Controller, 'readRelationship']);
				$route->setName(AuthNode\Constants::ROUTE_NAME_ROLE_RELATIONSHIP);

				/**
				 * CHILDREN
				 */
				$route = $group->get('/{' . self::URL_ITEM_ID . '}/children', [$this->roleChildrenV1Controller, 'index']);
				$route->setName(AuthNode\Constants::ROUTE_NAME_ROLE_CHILDREN);

				/**
				 * RULES
				 */
				$route = $group->get('/{' . self::URL_ITEM_ID . '}/rules', [$this->roleRulesV1Controller, 'index']);
				$route->setName(AuthNode\Constants::ROUTE_NAME_ROLE_RULES);
			});

			$group->group('/resources', function (Routing\RouteCollector $group): void {
				$route = $group->get('', [$this->resourcesV1Controller, 'index']);
				$route->setName(AuthNode\Constants::ROUTE_NAME_RESOURCES);

				$route = $group->get('/{' . self::URL_ITEM_ID . '}', [$this->resourcesV1Controller, 'read']);
				$route->setName(AuthNode\Constants::ROUTE_NAME_RESOURCE);

				$group->patch('/{' . self::URL_ITEM_ID . '}', [$this->resourcesV1Controller, 'update']);

				$route = $group->get('/{' . self::URL_ITEM_ID . '}/relationships/{' . self::RELATION_ENTITY . '}', [$this->resourcesV1Controller, 'readRelationship']);
				$route->setName(AuthNode\Constants::ROUTE_NAME_RESOURCE_RELATIONSHIP);

				/**
				 * CHILDREN
				 */
				$route = $group->get('/{' . self::URL_ITEM_ID . '}/children', [$this->resourceChildrenV1Controller, 'index']);
				$route->setName(AuthNode\Constants::ROUTE_NAME_RESOURCE_CHILDREN);

				/**
				 * PRIVILEGES
				 */
				$route = $group->get('/{' . self::URL_ITEM_ID . '}/privileges', [$this->resourcePrivilegesV1Controller, 'index']);
				$route->setName(AuthNode\Constants::ROUTE_NAME_RESOURCE_PRIVILEGES);
			});

			$group->group('/privileges', function (Routing\RouteCollector $group): void {
				$route = $group->get('', [$this->privilegesV1Controller, 'index']);
				$route->setName(AuthNode\Constants::ROUTE_NAME_PRIVILEGES);

				$route = $group->get('/{' . self::URL_ITEM_ID . '}', [$this->privilegesV1Controller, 'read']);
				$route->setName(AuthNode\Constants::ROUTE_NAME_PRIVILEGE);

				$group->patch('/{' . self::URL_ITEM_ID . '}', [$this->privilegesV1Controller, 'update']);

				$route = $group->get('/{' . self::URL_ITEM_ID . '}/relationships/{' . self::RELATION_ENTITY . '}', [$this->privilegesV1Controller, 'readRelationship']);
				$route->setName(AuthNode\Constants::ROUTE_NAME_PRIVILEGE_RELATIONSHIP);
			});

			$group->group('/rules', function (Routing\RouteCollector $group): void {
				$route = $group->get('', [$this->rulesV1Controller, 'index']);
				$route->setName(AuthNode\Constants::ROUTE_NAME_RULES);

				$route = $group->get('/{' . self::URL_ITEM_ID . '}', [$this->rulesV1Controller, 'read']);
				$route->setName(AuthNode\Constants::ROUTE_NAME_RULE);

				$group->post('', [$this->rulesV1Controller, 'create']);

				$group->patch('/{' . self::URL_ITEM_ID . '}', [$this->rulesV1Controller, 'update']);

				$group->delete('/{' . self::URL_ITEM_ID . '}', [$this->rulesV1Controller, 'delete']);

				$route = $group->get('/{' . self::URL_ITEM_ID . '}/relationships/{' . self::RELATION_ENTITY . '}', [$this->rulesV1Controller, 'readRelationship']);
				$route->setName(AuthNode\Constants::ROUTE_NAME_RULE_RELATIONSHIP);
			});
		})
			->addMiddleware($this->accessControlMiddleware);
	}

}
