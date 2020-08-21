<?php declare(strict_types = 1);

use Fig\Http\Message\StatusCodeInterface;

const ADMINISTRATOR_TOKEN = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJqdGkiOiI5YWY1NjI0Mi01ZDg3LTQzNjQtYmIxZS1kOWZjODI4NmIzZmYiLCJpc3MiOiJjb20uZmFzdHliaXJkLmF1dGgtbm9kZSIsImlhdCI6MTU4NTc0MjQwMCwiZXhwIjoxNTg1NzQ5NjAwLCJ1c2VyIjoiNWU3OWVmYmYtYmQwZC01YjdjLTQ2ZWYtYmZiZGVmYmZiZDM0Iiwicm9sZXMiOlsiYWRtaW5pc3RyYXRvciJdfQ.Lb-zUa9DL7swdVSEuPTqaR9FvLgKwuEtrhxiJFWjhU8';
const EXPIRED_TOKEN = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJqdGkiOiI3MjFlMTAyNS04Zjc4LTQzOGQtODIwZi0wZDQ2OWEzNzk1NWQiLCJpc3MiOiJjb20uZmFzdHliaXJkLmF1dGgtbm9kZSIsImlhdCI6MTU3Nzg4MDAwMCwiZXhwIjoxNTc3OTAxNjAwLCJ1c2VyIjoiNTI1ZDZhMDktN2MwNi00NmQyLWFmZmEtNzA5YmIxODM3MDdlIiwicm9sZXMiOlsiYWRtaW5pc3RyYXRvciJdfQ.F9veOiNfcqQVxpbMF7OY5j1AcPLpPQb8dEIZbrBmh24';
const INVALID_TOKEN = 'eyJqdGkiOiI5YWY1NjI0Mi01ZDg3LTQzNjQtYmIxZS1kOWZjODI4NmIzZmYiLCJpc3MiOiJjb20uZmFzdHliaXJkLmF1dGgtbm9kZSIsImlhdCI6MTU4NTc0MjQwMCwiZXhwIjoxNTg1NzQ5NjAwLCJ1c2VyIjoiNWU3OWVmYmYtYmQwZC01YjdjLTQ2ZWYtYmZiZGVmYmZiZDM0Iiwicm9sZXMiOlsiYWRtaW5pc3RyYXRvciJdfQ.Lb-zUa9DL7swdVSEuPTqaR9FvLgKwuEtrhxiJFWjhU8';

return [
	// Valid responses
	//////////////////
	'create'                 => [
		'/v1/session',
		null,
		file_get_contents(__DIR__ . '/requests/session/session.create.json'),
		StatusCodeInterface::STATUS_CREATED,
		__DIR__ . '/responses/session/session.create.json',
	],
	'createWithEmptyToken'   => [
		'/v1/session',
		'',
		file_get_contents(__DIR__ . '/requests/session/session.create.json'),
		StatusCodeInterface::STATUS_CREATED,
		__DIR__ . '/responses/session/session.create.json',
	],

	// Invalid responses
	////////////////////
	'createWithToken'        => [
		'/v1/session',
		'Bearer ' . ADMINISTRATOR_TOKEN,
		file_get_contents(__DIR__ . '/requests/session/session.create.json'),
		StatusCodeInterface::STATUS_FORBIDDEN,
		__DIR__ . '/responses/generic/forbidden.json',
	],
	'createWithExpiredToken' => [
		'/v1/session',
		'Bearer ' . EXPIRED_TOKEN,
		file_get_contents(__DIR__ . '/requests/session/session.create.json'),
		StatusCodeInterface::STATUS_UNAUTHORIZED,
		__DIR__ . '/responses/generic/unauthorized.json',
	],
	'createWithInvalidToken' => [
		'/v1/session',
		'Bearer ' . INVALID_TOKEN,
		file_get_contents(__DIR__ . '/requests/session/session.create.json'),
		StatusCodeInterface::STATUS_UNAUTHORIZED,
		__DIR__ . '/responses/generic/unauthorized.json',
	],
	'missingRequired'        => [
		'/v1/session',
		null,
		file_get_contents(__DIR__ . '/requests/session/session.create.missing.required.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/session/session.create.missing.required.json',
	],
	'unknown'                => [
		'/v1/session',
		null,
		file_get_contents(__DIR__ . '/requests/session/session.create.unknown.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/session/session.create.unknown.json',
	],
	'invalid'                => [
		'/v1/session',
		null,
		file_get_contents(__DIR__ . '/requests/session/session.create.invalid.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/session/session.create.invalid.json',
	],
	'deleted'                => [
		'/v1/session',
		null,
		file_get_contents(__DIR__ . '/requests/session/session.create.deleted.json'),
		StatusCodeInterface::STATUS_FORBIDDEN,
		__DIR__ . '/responses/session/session.create.deleted.json',
	],
	'blocked'                => [
		'/v1/session',
		null,
		file_get_contents(__DIR__ . '/requests/session/session.create.blocked.json'),
		StatusCodeInterface::STATUS_FORBIDDEN,
		__DIR__ . '/responses/session/session.create.blocked.json',
	],
	'notActivated'           => [
		'/v1/session',
		null,
		file_get_contents(__DIR__ . '/requests/session/session.create.notActivated.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/session/session.create.notActivated.json',
	],
	'approvalWaiting'        => [
		'/v1/session',
		null,
		file_get_contents(__DIR__ . '/requests/session/session.create.approvalWaiting.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/session/session.create.approvalWaiting.json',
	],
];
