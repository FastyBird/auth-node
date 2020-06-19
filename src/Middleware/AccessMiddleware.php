<?php declare(strict_types = 1);

/**
 * AccountMiddleware.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:AuthNode!
 * @subpackage     Middleware
 * @since          0.1.0
 *
 * @date           01.04.20
 */

namespace FastyBird\AuthNode\Middleware;

use Contributte\Translation;
use FastyBird\AuthNode;
use FastyBird\AuthNode\Exceptions;
use FastyBird\AuthNode\Security;
use FastyBird\NodeJsonApi\Exceptions as NodeJsonApiExceptions;
use Fig\Http\Message\StatusCodeInterface;
use IPub\SlimRouter;
use Nette\Security as NS;
use Nette\Utils;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ReflectionClass;
use ReflectionException;
use Reflector;

/**
 * Access check middleware
 *
 * @package        FastyBird:AuthNode!
 * @subpackage     Middleware
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class AccessMiddleware implements MiddlewareInterface
{

	/** @var Security\User */
	private $user;

	/** @var Translation\Translator */
	private $translator;

	public function __construct(
		Security\User $user,
		Translation\Translator $translator
	) {
		$this->user = $user;

		$this->translator = $translator;
	}

	/**
	 * @param ServerRequestInterface $request
	 * @param RequestHandlerInterface $handler
	 *
	 * @return ResponseInterface
	 *
	 * @throws NodeJsonApiExceptions\IJsonApiException
	 * @throws ReflectionException
	 */
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		$route = $request->getAttribute(SlimRouter\Routing\Router::ROUTE);

		if ($route instanceof SlimRouter\Routing\IRoute) {
			$routeCallable = $route->getCallable();

			if (
				is_array($routeCallable)
				&& count($routeCallable) === 2
				&& is_object($routeCallable[0])
				&& is_string($routeCallable[1])
				&& class_exists(get_class($routeCallable[0]))
			) {
				if (!$this->checkAccess(get_class($routeCallable[0]), $routeCallable[1])) {
					throw new NodeJsonApiExceptions\JsonApiErrorException(
						StatusCodeInterface::STATUS_FORBIDDEN,
						$this->translator->translate('//node.base.messages.forbidden.heading'),
						$this->translator->translate('//node.base.messages.forbidden.message')
					);
				}
			}
		}

		return $handler->handle($request);
	}


	/**
	 * @param string $controllerClass
	 * @param string $controllerMethod
	 *
	 * @return bool
	 *
	 * @throws ReflectionException
	 */
	private function checkAccess(
		string $controllerClass,
		string $controllerMethod
	): bool {
		if (class_exists($controllerClass)) {
			$reflection = new ReflectionClass($controllerClass);

			foreach ([$reflection, $reflection->getMethod($controllerMethod)] as $element) {
				if (!$this->isAllowed($element)) {
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * @param Reflector $element
	 *
	 * @return bool
	 */
	public function isAllowed(Reflector $element): bool
	{
		// Check annotations only if element have to be secured
		if ($this->parseAnnotation($element, 'Secured') !== null) {
			return $this->checkUser($element)
				&& $this->checkResources($element)
				&& $this->checkPrivileges($element)
				&& $this->checkPermission($element)
				&& $this->checkRoles($element);

		} else {
			return true;
		}
	}

	/**
	 * @param Reflector $element
	 *
	 * @return bool
	 *
	 * @throws Exceptions\InvalidArgumentException
	 */
	private function checkUser(Reflector $element): bool
	{
		// Check if element has @Secured\User annotation
		if ($this->parseAnnotation($element, 'Secured\User') !== null) {
			// Get user annotation
			$result = $this->parseAnnotation($element, 'Secured\User');

			if (count($result) > 0) {
				$user = end($result);

				// Annotation is single string
				if (in_array($user, ['loggedIn', 'guest'], true)) {
					// User have to be logged in and is not
					if ($user === 'loggedIn' && $this->user->isLoggedIn() === false) {
						return false;

						// User have to be logged out and is logged in
					} elseif ($user === 'guest' && $this->user->isLoggedIn() === true) {
						return false;
					}

					// Annotation have wrong definition
				} else {
					throw new Exceptions\InvalidArgumentException('In @Security\User annotation is allowed only one from two strings: \'loggedIn\' & \'guest\'');
				}
			}

			return true;
		}

		return true;
	}

	/**
	 * @param Reflector $element
	 *
	 * @return bool
	 *
	 * @throws Exceptions\InvalidStateException
	 */
	private function checkResources(Reflector $element): bool
	{
		// Check if element has @Security\Resource annotation & @Secured\Privilege annotation
		if ($this->parseAnnotation($element, 'Secured\Resource') !== null) {
			// Get resources annotation
			$resources = $this->parseAnnotation($element, 'Secured\Resource');

			if (count($resources) !== 1) {
				throw new Exceptions\InvalidStateException('Invalid resources count in @Security\Resource annotation!');
			}

			// Get privileges annotation
			$privileges = $this->parseAnnotation($element, 'Secured\Privilege');

			foreach ($resources as $resource) {
				if ($privileges !== null && $privileges !== []) {
					foreach ($privileges as $privilege) {
						if ($this->user->isAllowed($resource, $privilege)) {
							return true;
						}
					}

				} else {
					if ($this->user->isAllowed($resource)) {
						return true;
					}
				}
			}

			return false;
		}

		return true;
	}

	/**
	 * @param Reflector $element
	 *
	 * @return bool
	 *
	 * @throws Exceptions\InvalidStateException
	 */
	private function checkPrivileges(Reflector $element): bool
	{
		// Check if element has @Secured\Privilege annotation & hasn't @Secured\Resource annotation
		if (
			$this->parseAnnotation($element, 'Secured\Resource') !== null
			&& $this->parseAnnotation($element, 'Secured\Privilege') !== null
		) {
			$privileges = $this->parseAnnotation($element, 'Secured\Privilege');

			if (count($privileges) !== 1) {
				throw new Exceptions\InvalidStateException('Invalid privileges count in @Security\Privilege annotation!');
			}

			foreach ($privileges as $privilege) {
				// Check if privilege name is defined
				if (is_bool($privilege) || $privilege === null) {
					continue;
				}

				if ($this->user->isAllowed(NS\IAuthorizator::ALL, $privilege)) {
					return true;
				}
			}

			return false;
		}

		return true;
	}

	/**
	 * @param Reflector $element
	 *
	 * @return bool
	 */
	private function checkPermission(Reflector $element): bool
	{
		// Check if element has @Secured\Permission annotation
		if ($this->parseAnnotation($element, 'Secured\Permission') !== null) {
			$permissions = $this->parseAnnotation($element, 'Secured\Permission');

			foreach ($permissions as $permission) {
				// Check if parameters are defined
				if (is_bool($permission) || $permission === null) {
					continue;
				}

				// Parse resource & privilege from permission
				[$resource, $privilege] = explode(AuthNode\Constants::PERMISSIONS_DELIMITER, $permission);

				// Remove white spaces
				$resource = Utils\Strings::trim($resource);
				$privilege = Utils\Strings::trim($privilege);

				if ($this->user->isAllowed($resource, $privilege)) {
					return true;
				}
			}

			return false;
		}

		return true;
	}

	/**
	 * @param Reflector $element
	 *
	 * @return bool
	 */
	private function checkRoles(Reflector $element): bool
	{
		// Check if element has @Secured\Role annotation
		if ($this->parseAnnotation($element, 'Secured\Role') !== null) {
			$roles = $this->parseAnnotation($element, 'Secured\Role');

			foreach ($roles as $role) {
				// Check if role name is defined
				if (is_bool($role) || $role === null) {
					continue;
				}

				if ($this->user->isInRole($role)) {
					return true;
				}
			}

			return false;
		}

		return true;
	}

	/**
	 * @param Reflector $ref
	 * @param string $name
	 *
	 * @return mixed[]|null
	 */
	private function parseAnnotation(Reflector $ref, string $name): ?array
	{
		$callable = [$ref, 'getDocComment'];

		if (
			!is_callable($callable)
			|| preg_match_all(
				'#[\s*]@' . preg_quote($name, '#') . '(?:\(\s*([^)]*)\s*\)|\s|$)#',
				(string) call_user_func($callable),
				$m
			) === false
		) {
			return null;
		}

		static $tokens = ['true' => true, 'false' => false, 'null' => null];

		$res = [];

		foreach ($m[1] as $s) {
			$items = preg_split('#\s*,\s*#', $s, -1, PREG_SPLIT_NO_EMPTY);

			foreach ($items !== false ? $items : ['true'] as $item) {
				$tmp = strtolower($item);

				if (!array_key_exists($tmp, $tokens) && $item !== '') {
					$res[] = $item;
				}
			}
		}

		return $res;
	}

}
