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

	/** @var Controllers\AccountSecurityQuestionV1Controller */
	private $accountSecurityQuestionV1Controller;

	/** @var Controllers\AccountIdentitiesV1Controller */
	private $accountIdentitiesV1Controller;

	/** @var Controllers\AccountRolesV1Controller */
	private $accountRolesV1Controller;

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

	public function __construct(
		Controllers\SessionV1Controller $sessionV1Controller,
		Controllers\AccountV1Controller $accountV1Controller,
		Controllers\AccountEmailsV1Controller $accountEmailsV1Controller,
		Controllers\AccountSecurityQuestionV1Controller $accountSecurityQuestionV1Controller,
		Controllers\AccountIdentitiesV1Controller $accountIdentitiesV1Controller,
		Controllers\AccountRolesV1Controller $accountRolesV1Controller,
		Controllers\AccountsV1Controller $accountsV1Controller,
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
		$this->accountSecurityQuestionV1Controller = $accountSecurityQuestionV1Controller;
		$this->accountIdentitiesV1Controller = $accountIdentitiesV1Controller;
		$this->accountRolesV1Controller = $accountRolesV1Controller;

		$this->accountsV1Controller = $accountsV1Controller;
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
				$route->setName('session');

				$group->post('', [$this->sessionV1Controller, 'create']);

				$group->patch('', [$this->sessionV1Controller, 'update']);

				$group->delete('', [$this->sessionV1Controller, 'delete']);

				$group->post('/validate', [$this->sessionV1Controller, 'validate']);

				$route = $group->get('/relationships/{' . self::RELATION_ENTITY . '}', [$this->sessionV1Controller, 'readRelationship']);
				$route->setName('session.relationship');
			});

			$group->group('/me', function (Routing\RouteCollector $group): void {
				$route = $group->get('', [$this->accountV1Controller, 'read']);
				$route->setName('me');

				$group->patch('', [$this->accountV1Controller, 'update']);

				$group->delete('', [$this->accountV1Controller, 'delete']);

				$route = $group->get('/relationships/{' . self::RELATION_ENTITY . '}', [$this->accountV1Controller, 'readRelationship']);
				$route->setName('me.relationship');

				/**
				 * PROFILE EMAILS
				 */
				$group->group('/emails', function (Routing\RouteCollector $group): void {
					$route = $group->get('', [$this->accountEmailsV1Controller, 'index']);
					$route->setName('me.emails');

					$route = $group->get('/{' . self::URL_ITEM_ID . '}', [$this->accountEmailsV1Controller, 'read']);
					$route->setName('me.email');

					$group->post('', [$this->accountEmailsV1Controller, 'create']);

					$group->patch('/{' . self::URL_ITEM_ID . '}', [$this->accountEmailsV1Controller, 'update']);

					$group->delete('/{' . self::URL_ITEM_ID . '}', [$this->accountEmailsV1Controller, 'delete']);

					$route = $group->get('/{' . self::URL_ITEM_ID . '}/relationships/{' . self::RELATION_ENTITY . '}', [$this->accountEmailsV1Controller, 'readRelationship']);
					$route->setName('me.email.relationship');
				});

				/**
				 * PROFILE SECURITY QUESTION
				 */
				$group->group('/security-question', function (Routing\RouteCollector $group): void {
					$route = $group->get('', [$this->accountSecurityQuestionV1Controller, 'read']);
					$route->setName('me.security.question');

					$group->post('', [$this->accountSecurityQuestionV1Controller, 'create']);

					$group->patch('', [$this->accountSecurityQuestionV1Controller, 'update']);

					$group->post('/validate', [$this->accountSecurityQuestionV1Controller, 'validate']);

					$route = $group->get('/relationships/{' . self::RELATION_ENTITY . '}', [$this->accountSecurityQuestionV1Controller, 'readRelationship']);
					$route->setName('me.security.question.relationship');
				});

				/**
				 * PROFILE IDENTITIES
				 */
				$group->group('/identities', function (Routing\RouteCollector $group): void {
					$route = $group->get('', [$this->accountIdentitiesV1Controller, 'index']);
					$route->setName('me.identities');

					$route = $group->get('/{' . self::URL_ITEM_ID . '}', [$this->accountIdentitiesV1Controller, 'read']);
					$route->setName('me.identity');

					$group->patch('/{' . self::URL_ITEM_ID . '}', [$this->accountIdentitiesV1Controller, 'update']);

					$group->post('/{' . self::URL_ITEM_ID . '}/validate', [$this->accountIdentitiesV1Controller, 'validate']);

					$route = $group->get('/{' . self::URL_ITEM_ID . '}/relationships/{' . self::RELATION_ENTITY . '}', [$this->accountIdentitiesV1Controller, 'readRelationship']);
					$route->setName('me.identity.relationship');
				});

				/**
				 * PROFILE ROLES
				 */
				$group->group('/roles', function (Routing\RouteCollector $group): void {
					$route = $group->get('', [$this->accountRolesV1Controller, 'index']);
					$route->setName('me.roles');
				});
			});

			$group->group('/accounts', function (Routing\RouteCollector $group): void {
				$route = $group->get('/{' . self::URL_ITEM_ID . '}', [$this->accountsV1Controller, 'read']);
				$route->setName('account');

				$group->post('', [$this->accountsV1Controller, 'create']);

				$group->patch('/{' . self::URL_ITEM_ID . '}', [$this->accountsV1Controller, 'update']);

				$group->delete('/{' . self::URL_ITEM_ID . '}', [$this->accountsV1Controller, 'delete']);

				$route = $group->get('/{' . self::URL_ITEM_ID . '}/relationships/{' . self::RELATION_ENTITY . '}', [$this->accountsV1Controller, 'readRelationship']);
				$route->setName('account.relationship');
			});

			$group->group('/accounts/{' . self::URL_ACCOUNT_ID . '}', function (Routing\RouteCollector $group): void {
				/**
				 * ACCOUNT EMAILS
				 */
				$group->group('/emails', function (Routing\RouteCollector $group): void {
					$route = $group->get('', [$this->accountEmailsV1Controller, 'index']);
					$route->setName('account.emails');

					$route = $group->get('/{' . self::URL_ITEM_ID . '}', [$this->accountEmailsV1Controller, 'read']);
					$route->setName('account.email');

					$group->post('', [$this->accountEmailsV1Controller, 'create']);

					$group->patch('/{' . self::URL_ITEM_ID . '}', [$this->accountEmailsV1Controller, 'update']);

					$group->delete('/{' . self::URL_ITEM_ID . '}', [$this->accountEmailsV1Controller, 'delete']);

					$route = $group->get('/{' . self::URL_ITEM_ID . '}/relationships/{' . self::RELATION_ENTITY . '}', [$this->accountEmailsV1Controller, 'readRelationship']);
					$route->setName('account.email.relationship');
				});

				/**
				 * ACCOUNT SECURITY QUESTION
				 */
				$group->group('/security-question', function (Routing\RouteCollector $group): void {
					$route = $group->get('', [$this->accountSecurityQuestionV1Controller, 'read']);
					$route->setName('account.security.question');

					$group->post('', [$this->accountSecurityQuestionV1Controller, 'create']);

					$group->patch('', [$this->accountSecurityQuestionV1Controller, 'update']);

					$group->post('/validate', [$this->accountSecurityQuestionV1Controller, 'validate']);

					$route = $group->get('/relationships/{' . self::RELATION_ENTITY . '}', [$this->accountSecurityQuestionV1Controller, 'readRelationship']);
					$route->setName('account.security.question.relationship');
				});

				/**
				 * ACCOUNT IDENTITIES
				 */
				$group->group('/identities', function (Routing\RouteCollector $group): void {
					$route = $group->get('', [$this->accountIdentitiesV1Controller, 'index']);
					$route->setName('account.identities');

					$route = $group->get('/{' . self::URL_ITEM_ID . '}', [$this->accountIdentitiesV1Controller, 'read']);
					$route->setName('account.identity');

					$group->patch('/{' . self::URL_ITEM_ID . '}', [$this->accountIdentitiesV1Controller, 'update']);

					$group->post('/{' . self::URL_ITEM_ID . '}/validate', [$this->accountIdentitiesV1Controller, 'validate']);

					$route = $group->get('/{' . self::URL_ITEM_ID . '}/relationships/{' . self::RELATION_ENTITY . '}', [$this->accountIdentitiesV1Controller, 'readRelationship']);
					$route->setName('account.identity.relationship');
				});

				/**
				 * ACCOUNT ROLES
				 */
				$group->group('/roles', function (Routing\RouteCollector $group): void {
					$route = $group->get('', [$this->accountRolesV1Controller, 'index']);
					$route->setName('account.roles');
				});
			});

			$group->group('/roles', function (Routing\RouteCollector $group): void {
				$route = $group->get('', [$this->rolesV1Controller, 'index']);
				$route->setName('roles');

				$route = $group->get('/{' . self::URL_ITEM_ID . '}', [$this->rolesV1Controller, 'read']);
				$route->setName('role');

				$group->post('', [$this->rolesV1Controller, 'create']);

				$group->patch('/{' . self::URL_ITEM_ID . '}', [$this->rolesV1Controller, 'update']);

				$group->delete('/{' . self::URL_ITEM_ID . '}', [$this->rolesV1Controller, 'delete']);

				$route = $group->get('/{' . self::URL_ITEM_ID . '}/relationships/{' . self::RELATION_ENTITY . '}', [$this->rolesV1Controller, 'readRelationship']);
				$route->setName('role.relationship');

				/**
				 * CHILDREN
				 */
				$route = $group->get('/{' . self::URL_ITEM_ID . '}/children', [$this->roleChildrenV1Controller, 'index']);
				$route->setName('role.children');

				/**
				 * RULES
				 */
				$route = $group->get('/{' . self::URL_ITEM_ID . '}/rules', [$this->roleRulesV1Controller, 'index']);
				$route->setName('role.rules');
			});

			$group->group('/resources', function (Routing\RouteCollector $group): void {
				$route = $group->get('', [$this->resourcesV1Controller, 'index']);
				$route->setName('resources');

				$route = $group->get('/{' . self::URL_ITEM_ID . '}', [$this->resourcesV1Controller, 'read']);
				$route->setName('resource');

				$group->patch('/{' . self::URL_ITEM_ID . '}', [$this->resourcesV1Controller, 'update']);

				$route = $group->get('/{' . self::URL_ITEM_ID . '}/relationships/{' . self::RELATION_ENTITY . '}', [$this->resourcesV1Controller, 'readRelationship']);
				$route->setName('resource.relationship');

				/**
				 * CHILDREN
				 */
				$route = $group->get('/{' . self::URL_ITEM_ID . '}/children', [$this->resourceChildrenV1Controller, 'index']);
				$route->setName('resource.children');

				/**
				 * PRIVILEGES
				 */
				$route = $group->get('/{' . self::URL_ITEM_ID . '}/privileges', [$this->resourcePrivilegesV1Controller, 'index']);
				$route->setName('resource.privileges');
			});

			$group->group('/privileges', function (Routing\RouteCollector $group): void {
				$route = $group->get('', [$this->privilegesV1Controller, 'index']);
				$route->setName('privileges');

				$route = $group->get('/{' . self::URL_ITEM_ID . '}', [$this->privilegesV1Controller, 'read']);
				$route->setName('privilege');

				$group->patch('/{' . self::URL_ITEM_ID . '}', [$this->privilegesV1Controller, 'update']);

				$route = $group->get('/{' . self::URL_ITEM_ID . '}/relationships/{' . self::RELATION_ENTITY . '}', [$this->privilegesV1Controller, 'readRelationship']);
				$route->setName('privilege.relationship');
			});

			$group->group('/rules', function (Routing\RouteCollector $group): void {
				$route = $group->get('', [$this->rulesV1Controller, 'index']);
				$route->setName('rules');

				$route = $group->get('/{' . self::URL_ITEM_ID . '}', [$this->rulesV1Controller, 'read']);
				$route->setName('rule');

				$group->post('', [$this->rulesV1Controller, 'create']);

				$group->patch('/{' . self::URL_ITEM_ID . '}', [$this->rulesV1Controller, 'update']);

				$group->delete('/{' . self::URL_ITEM_ID . '}', [$this->rulesV1Controller, 'delete']);

				$route = $group->get('/{' . self::URL_ITEM_ID . '}/relationships/{' . self::RELATION_ENTITY . '}', [$this->rulesV1Controller, 'readRelationship']);
				$route->setName('rule.relationship');
			});
		})
			->addMiddleware($this->accessControlMiddleware);
	}

}
