<?php declare(strict_types = 1);

use Fig\Http\Message\StatusCodeInterface;

const VALID_TOKEN = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJqdGkiOiI5YWY1NjI0Mi01ZDg3LTQzNjQtYmIxZS1kOWZjODI4NmIzZmYiLCJpc3MiOiJjb20uZmFzdHliaXJkLmF1dGgtbm9kZSIsImlhdCI6MTU4NTc0MjQwMCwiZXhwIjoxNTg1NzQ5NjAwLCJ1c2VyIjoiNWU3OWVmYmYtYmQwZC01YjdjLTQ2ZWYtYmZiZGVmYmZiZDM0Iiwicm9sZXMiOlsiYWRtaW5pc3RyYXRvciJdfQ.Lb-zUa9DL7swdVSEuPTqaR9FvLgKwuEtrhxiJFWjhU8';
const EXPIRED_TOKEN = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJqdGkiOiI3MjFlMTAyNS04Zjc4LTQzOGQtODIwZi0wZDQ2OWEzNzk1NWQiLCJpc3MiOiJjb20uZmFzdHliaXJkLmF1dGgtbm9kZSIsImlhdCI6MTU3Nzg4MDAwMCwiZXhwIjoxNTc3OTAxNjAwLCJ1c2VyIjoiNTI1ZDZhMDktN2MwNi00NmQyLWFmZmEtNzA5YmIxODM3MDdlIiwicm9sZXMiOlsiYWRtaW5pc3RyYXRvciJdfQ.F9veOiNfcqQVxpbMF7OY5j1AcPLpPQb8dEIZbrBmh24';
const INVALID_TOKEN = 'eyJqdGkiOiI5YWY1NjI0Mi01ZDg3LTQzNjQtYmIxZS1kOWZjODI4NmIzZmYiLCJpc3MiOiJjb20uZmFzdHliaXJkLmF1dGgtbm9kZSIsImlhdCI6MTU4NTc0MjQwMCwiZXhwIjoxNTg1NzQ5NjAwLCJ1c2VyIjoiNWU3OWVmYmYtYmQwZC01YjdjLTQ2ZWYtYmZiZGVmYmZiZDM0Iiwicm9sZXMiOlsiYWRtaW5pc3RyYXRvciJdfQ.Lb-zUa9DL7swdVSEuPTqaR9FvLgKwuEtrhxiJFWjhU8';
const VALID_TOKEN_USER = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJqdGkiOiI3YzVkNzdhZC1kOTNlLTRjMmMtOThlNS05ZTFhZmM0NDQ2MTUiLCJpc3MiOiJjb20uZmFzdHliaXJkLmF1dGgtbm9kZSIsImlhdCI6MTU4NTc0MjQwMCwiZXhwIjoxNTg1NzQ5NjAwLCJ1c2VyIjoiZWZiZmJkZWYtYmZiZC02OGVmLWJmYmQtNzcwYjQwZWZiZmJkIiwicm9sZXMiOlsidXNlciJdfQ.cbatWCuGX-K8XbF9MMN7DqxV9hriWmUSGcDGGmnxXX0';

return [
	// Valid responses
	//////////////////
	'updateUser'    => [
		'/v1/accounts/5e79efbf-bd0d-5b7c-46ef-bfbdefbfbd34',
		'Bearer ' . VALID_TOKEN,
		file_get_contents(__DIR__ . '/requests/accounts.update.user.json'),
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/accounts.update.user.json',
	],

	// Invalid responses
	////////////////////
	'invalidType'   => [
		'/v1/accounts/5e79efbf-bd0d-5b7c-46ef-bfbdefbfbd34',
		'Bearer ' . VALID_TOKEN,
		file_get_contents(__DIR__ . '/requests/accounts.update.invalidType.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/accounts.invalidType.json',
	],
	'idMismatch'    => [
		'/v1/accounts/5e79efbf-bd0d-5b7c-46ef-bfbdefbfbd34',
		'Bearer ' . VALID_TOKEN,
		file_get_contents(__DIR__ . '/requests/accounts.update.idMismatch.json'),
		StatusCodeInterface::STATUS_BAD_REQUEST,
		__DIR__ . '/responses/invalid.identifier.json',
	],
	'notFound'      => [
		'/v1/accounts/17c59dfa-2edd-438e-8c49-faa4e38e5ae5',
		'Bearer ' . VALID_TOKEN,
		file_get_contents(__DIR__ . '/requests/accounts.update.user.json'),
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/accounts.notFound.json',
	],
	'invalidToken'  => [
		'/v1/accounts/5e79efbf-bd0d-5b7c-46ef-bfbdefbfbd34',
		'Bearer ' . INVALID_TOKEN,
		file_get_contents(__DIR__ . '/requests/accounts.update.user.json'),
		StatusCodeInterface::STATUS_UNAUTHORIZED,
		__DIR__ . '/responses/unauthorized.json',
	],
	'noToken'       => [
		'/v1/accounts/5e79efbf-bd0d-5b7c-46ef-bfbdefbfbd34',
		null,
		file_get_contents(__DIR__ . '/requests/accounts.update.user.json'),
		StatusCodeInterface::STATUS_FORBIDDEN,
		__DIR__ . '/responses/forbidden.json',
	],
	'expiredToken'  => [
		'/v1/accounts/5e79efbf-bd0d-5b7c-46ef-bfbdefbfbd34',
		'Bearer ' . EXPIRED_TOKEN,
		file_get_contents(__DIR__ . '/requests/accounts.update.user.json'),
		StatusCodeInterface::STATUS_UNAUTHORIZED,
		__DIR__ . '/responses/unauthorized.json',
	],
	'userToken'       => [
		'/v1/accounts/5e79efbf-bd0d-5b7c-46ef-bfbdefbfbd34',
		'Bearer ' . VALID_TOKEN_USER,
		file_get_contents(__DIR__ . '/requests/accounts.update.user.json'),
		StatusCodeInterface::STATUS_FORBIDDEN,
		__DIR__ . '/responses/forbidden.json',
	],
];