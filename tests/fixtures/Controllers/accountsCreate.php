<?php declare(strict_types = 1);

use Fig\Http\Message\StatusCodeInterface;

const VALID_TOKEN = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJqdGkiOiI5YWY1NjI0Mi01ZDg3LTQzNjQtYmIxZS1kOWZjODI4NmIzZmYiLCJpc3MiOiJjb20uZmFzdHliaXJkLmF1dGgtbm9kZSIsImlhdCI6MTU4NTc0MjQwMCwiZXhwIjoxNTg1NzQ5NjAwLCJ1c2VyIjoiNWU3OWVmYmYtYmQwZC01YjdjLTQ2ZWYtYmZiZGVmYmZiZDM0Iiwicm9sZXMiOlsiYWRtaW5pc3RyYXRvciJdfQ.Lb-zUa9DL7swdVSEuPTqaR9FvLgKwuEtrhxiJFWjhU8';
const EXPIRED_TOKEN = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJqdGkiOiI3MjFlMTAyNS04Zjc4LTQzOGQtODIwZi0wZDQ2OWEzNzk1NWQiLCJpc3MiOiJjb20uZmFzdHliaXJkLmF1dGgtbm9kZSIsImlhdCI6MTU3Nzg4MDAwMCwiZXhwIjoxNTc3OTAxNjAwLCJ1c2VyIjoiNTI1ZDZhMDktN2MwNi00NmQyLWFmZmEtNzA5YmIxODM3MDdlIiwicm9sZXMiOlsiYWRtaW5pc3RyYXRvciJdfQ.F9veOiNfcqQVxpbMF7OY5j1AcPLpPQb8dEIZbrBmh24';
const INVALID_TOKEN = 'eyJqdGkiOiI5YWY1NjI0Mi01ZDg3LTQzNjQtYmIxZS1kOWZjODI4NmIzZmYiLCJpc3MiOiJjb20uZmFzdHliaXJkLmF1dGgtbm9kZSIsImlhdCI6MTU4NTc0MjQwMCwiZXhwIjoxNTg1NzQ5NjAwLCJ1c2VyIjoiNWU3OWVmYmYtYmQwZC01YjdjLTQ2ZWYtYmZiZGVmYmZiZDM0Iiwicm9sZXMiOlsiYWRtaW5pc3RyYXRvciJdfQ.Lb-zUa9DL7swdVSEuPTqaR9FvLgKwuEtrhxiJFWjhU8';
const VALID_TOKEN_USER = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJqdGkiOiI3YzVkNzdhZC1kOTNlLTRjMmMtOThlNS05ZTFhZmM0NDQ2MTUiLCJpc3MiOiJjb20uZmFzdHliaXJkLmF1dGgtbm9kZSIsImlhdCI6MTU4NTc0MjQwMCwiZXhwIjoxNTg1NzQ5NjAwLCJ1c2VyIjoiZWZiZmJkZWYtYmZiZC02OGVmLWJmYmQtNzcwYjQwZWZiZmJkIiwicm9sZXMiOlsidXNlciJdfQ.cbatWCuGX-K8XbF9MMN7DqxV9hriWmUSGcDGGmnxXX0';

return [
	// Valid responses
	//////////////////
	'createUser'          => [
		'/v1/accounts',
		'Bearer ' . VALID_TOKEN,
		file_get_contents(__DIR__ . '/requests/accounts.create.user.json'),
		StatusCodeInterface::STATUS_CREATED,
		__DIR__ . '/responses/accounts.create.user.json',
	],
	'createUserWithRoles' => [
		'/v1/accounts',
		'Bearer ' . VALID_TOKEN,
		file_get_contents(__DIR__ . '/requests/accounts.create.userWithRoles.json'),
		StatusCodeInterface::STATUS_CREATED,
		__DIR__ . '/responses/accounts.create.userWithRoles.json',
	],
	'createMachine'       => [
		'/v1/accounts',
		'Bearer ' . VALID_TOKEN,
		file_get_contents(__DIR__ . '/requests/accounts.create.machine.json'),
		StatusCodeInterface::STATUS_CREATED,
		__DIR__ . '/responses/accounts.create.machine.json',
	],

	// Invalid responses
	////////////////////
	'missingRequired'     => [
		'/v1/accounts',
		'Bearer ' . VALID_TOKEN,
		file_get_contents(__DIR__ . '/requests/accounts.create.missing.required.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/accounts.create.missingRequired.json',
	],
	'invalidType'         => [
		'/v1/accounts',
		'Bearer ' . VALID_TOKEN,
		file_get_contents(__DIR__ . '/requests/accounts.create.invalidType.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/generic/invalidType.json',
	],
	'missingParent'       => [
		'/v1/accounts',
		'Bearer ' . VALID_TOKEN,
		file_get_contents(__DIR__ . '/requests/accounts.create.missingParent.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/accounts.create.missingParent.json',
	],
	// As parent is set account with other parent
	'invalidParent'       => [
		'/v1/accounts',
		'Bearer ' . VALID_TOKEN,
		file_get_contents(__DIR__ . '/requests/accounts.create.invalidParent.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/accounts.invalidParent.json',
	],
	// Administrator role is only for parent account
	'invalidRole'         => [
		'/v1/accounts',
		'Bearer ' . VALID_TOKEN,
		file_get_contents(__DIR__ . '/requests/accounts.create.invalidRole.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/accounts.invalidRole.json',
	],
	// User role could not be combined with other roles
	'invalidRoles'        => [
		'/v1/accounts',
		'Bearer ' . VALID_TOKEN,
		file_get_contents(__DIR__ . '/requests/accounts.create.invalidRoles.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/accounts.invalidRole.json',
	],
	'invalidToken'        => [
		'/v1/accounts',
		'Bearer ' . INVALID_TOKEN,
		file_get_contents(__DIR__ . '/requests/accounts.create.user.json'),
		StatusCodeInterface::STATUS_UNAUTHORIZED,
		__DIR__ . '/responses/generic/unauthorized.json',
	],
	'noToken'             => [
		'/v1/accounts',
		null,
		file_get_contents(__DIR__ . '/requests/accounts.create.user.json'),
		StatusCodeInterface::STATUS_FORBIDDEN,
		__DIR__ . '/responses/generic/forbidden.json',
	],
	'expiredToken'        => [
		'/v1/accounts',
		'Bearer ' . EXPIRED_TOKEN,
		file_get_contents(__DIR__ . '/requests/accounts.create.user.json'),
		StatusCodeInterface::STATUS_UNAUTHORIZED,
		__DIR__ . '/responses/generic/unauthorized.json',
	],
	'userToken'           => [
		'/v1/accounts',
		'Bearer ' . VALID_TOKEN_USER,
		file_get_contents(__DIR__ . '/requests/accounts.create.user.json'),
		StatusCodeInterface::STATUS_FORBIDDEN,
		__DIR__ . '/responses/generic/forbidden.json',
	],
];
