<?php declare(strict_types = 1);

/**
 * AccountRolesV1Controller.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Controllers
 * @since          0.1.0
 *
 * @date           22.06.20
 */

namespace FastyBird\AuthNode\Controllers;

use FastyBird\AuthNode\Controllers;
use FastyBird\AuthNode\Models;
use FastyBird\NodeJsonApi\Exceptions as NodeJsonApiExceptions;
use FastyBird\NodeWebServer\Http as NodeWebServerHttp;
use Psr\Http\Message;

/**
 * Account roles API controller
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Controllers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class AccountRolesV1Controller extends BaseV1Controller
{

	use Controllers\Finders\TAccountFinder;

	/** @var Models\Accounts\IAccountRepository */
	protected $accountRepository;

	/** @var string */
	protected $translationDomain = 'node.roles';

	public function __construct(
		Models\Accounts\IAccountRepository $accountRepository
	) {
		$this->accountRepository = $accountRepository;
	}

	/**
	 * @param Message\ServerRequestInterface $request
	 * @param NodeWebServerHttp\Response $response
	 *
	 * @return NodeWebServerHttp\Response
	 *
	 * @throws NodeJsonApiExceptions\IJsonApiException
	 *
	 * @Secured
	 * @Secured\User(loggedIn)
	 */
	public function index(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		$account = $this->findAccount($request);

		return $response
			->withEntity(NodeWebServerHttp\ScalarEntity::from($account->getRoles()));
	}

}
