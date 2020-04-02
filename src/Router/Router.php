<?php declare(strict_types = 1);

/**
 * RouterFactory.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AccountsNode!
 * @subpackage     Router
 * @since          0.1.0
 *
 * @date           31.03.20
 */

namespace FastyBird\AccountsNode\Router;

use FastyBird\AccountsNode\Controllers;
use IPub\SlimRouter\Routing;
use Psr\Http\Message\ResponseFactoryInterface;

/**
 * Node router configuration
 *
 * @package        FastyBird:AccountsNode!
 * @subpackage     Router
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class Router extends Routing\Router
{

	public const URL_ITEM_ID = 'id';

	public const RELATION_ENTITY = 'relationEntity';

	/** @var Controllers\SessionV1Controller */
	private $sessionV1Controller;

	/** @var Controllers\AccountV1Controller */
	private $accountV1Controller;

	/** @var Controllers\EmailsV1Controller */
	private $emailsV1Controller;

	/** @var Controllers\SecurityQuestionV1Controller */
	private $securityQuestionV1Controller;

	/** @var Controllers\SystemIdentityV1Controller */
	private $systemIdentityV1Controller;

	/** @var Controllers\RolesV1Controller */
	private $rolesV1Controller;

	public function __construct(
		Controllers\SessionV1Controller $sessionV1Controller,
		Controllers\AccountV1Controller $accountV1Controller,
		Controllers\EmailsV1Controller $emailsV1Controller,
		Controllers\SecurityQuestionV1Controller $securityQuestionV1Controller,
		Controllers\SystemIdentityV1Controller $systemIdentityV1Controller,
		Controllers\RolesV1Controller $rolesV1Controller,
		?ResponseFactoryInterface $responseFactory = null
	) {
		parent::__construct($responseFactory, null);

		$this->sessionV1Controller = $sessionV1Controller;
		$this->accountV1Controller = $accountV1Controller;
		$this->emailsV1Controller = $emailsV1Controller;
		$this->securityQuestionV1Controller = $securityQuestionV1Controller;
		$this->systemIdentityV1Controller = $systemIdentityV1Controller;
		$this->rolesV1Controller = $rolesV1Controller;
	}

	/**
	 * @return void
	 */
	public function registerRoutes(): void
	{
		$this->group('/v1', function (Routing\RouteCollector $group): void {
			$group->post('/register', [$this->accountV1Controller, 'create']);

			$group->post('/password-reset', [$this->systemIdentityV1Controller, 'requestPassword']);

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

			$group->group('/account', function (Routing\RouteCollector $group): void {
				$route = $group->get('', [$this->accountV1Controller, 'read']);
				$route->setName('account');

				$group->patch('', [$this->accountV1Controller, 'update']);

				$group->delete('', [$this->accountV1Controller, 'delete']);

				$route = $group->get('/relationships/{' . self::RELATION_ENTITY . '}', [$this->accountV1Controller, 'readRelationship']);
				$route->setName('account.relationship');

				$group->group('/emails', function (Routing\RouteCollector $group): void {
					$route = $group->get('', [$this->emailsV1Controller, 'index']);
					$route->setName('account.emails');

					$route = $group->get('/{' . self::URL_ITEM_ID . '}', [$this->emailsV1Controller, 'read']);
					$route->setName('account.email');

					$group->post('', [$this->emailsV1Controller, 'create']);

					$group->patch('/{' . self::URL_ITEM_ID . '}', [$this->emailsV1Controller, 'update']);

					$group->delete('/{' . self::URL_ITEM_ID . '}', [$this->emailsV1Controller, 'delete']);

					$route = $group->get('/{' . self::URL_ITEM_ID . '}/relationships/{' . self::RELATION_ENTITY . '}', [$this->emailsV1Controller, 'readRelationship']);
					$route->setName('account.emails.relationship');
				});

				$group->group('/security-question', function (Routing\RouteCollector $group): void {
					$route = $group->get('', [$this->securityQuestionV1Controller, 'read']);
					$route->setName('account.security.question');

					$group->post('', [$this->securityQuestionV1Controller, 'create']);

					$group->patch('', [$this->securityQuestionV1Controller, 'update']);

					$group->post('/validate', [$this->securityQuestionV1Controller, 'validate']);

					$route = $group->get('/relationships/{' . self::RELATION_ENTITY . '}', [$this->securityQuestionV1Controller, 'readRelationship']);
					$route->setName('account.security.question.relationship');
				});

				$group->group('/identity', function (Routing\RouteCollector $group): void {
					$group->patch('', [$this->systemIdentityV1Controller, 'update']);

					$group->post('/validate', [$this->systemIdentityV1Controller, 'validate']);
				});

				$group->group('/roles', function (Routing\RouteCollector $group): void {
					$route = $group->get('', [$this->rolesV1Controller, 'index']);
					$route->setName('account.roles');

					$route = $group->get('/{' . self::URL_ITEM_ID . '}', [$this->rolesV1Controller, 'read']);
					$route->setName('account.role');

					$route = $group->get('/{' . self::URL_ITEM_ID . '}/relationships/{' . self::RELATION_ENTITY . '}', [$this->rolesV1Controller, 'readRelationship']);
					$route->setName('account.roles.relationship');
				});
			});
		});
	}

}
