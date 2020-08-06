<?php declare(strict_types = 1);

use Fig\Http\Message\StatusCodeInterface;

const VALID_TOKEN = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJqdGkiOiI5YWY1NjI0Mi01ZDg3LTQzNjQtYmIxZS1kOWZjODI4NmIzZmYiLCJpc3MiOiJjb20uZmFzdHliaXJkLmF1dGgtbm9kZSIsImlhdCI6MTU4NTc0MjQwMCwiZXhwIjoxNTg1NzQ5NjAwLCJ1c2VyIjoiNWU3OWVmYmYtYmQwZC01YjdjLTQ2ZWYtYmZiZGVmYmZiZDM0Iiwicm9sZXMiOlsiYWRtaW5pc3RyYXRvciJdfQ.Lb-zUa9DL7swdVSEuPTqaR9FvLgKwuEtrhxiJFWjhU8';
const EXPIRED_TOKEN = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJqdGkiOiI3MjFlMTAyNS04Zjc4LTQzOGQtODIwZi0wZDQ2OWEzNzk1NWQiLCJpc3MiOiJjb20uZmFzdHliaXJkLmF1dGgtbm9kZSIsImlhdCI6MTU3Nzg4MDAwMCwiZXhwIjoxNTc3OTAxNjAwLCJ1c2VyIjoiNTI1ZDZhMDktN2MwNi00NmQyLWFmZmEtNzA5YmIxODM3MDdlIiwicm9sZXMiOlsiYWRtaW5pc3RyYXRvciJdfQ.F9veOiNfcqQVxpbMF7OY5j1AcPLpPQb8dEIZbrBmh24';
const INVALID_TOKEN = 'eyJqdGkiOiI5YWY1NjI0Mi01ZDg3LTQzNjQtYmIxZS1kOWZjODI4NmIzZmYiLCJpc3MiOiJjb20uZmFzdHliaXJkLmF1dGgtbm9kZSIsImlhdCI6MTU4NTc0MjQwMCwiZXhwIjoxNTg1NzQ5NjAwLCJ1c2VyIjoiNWU3OWVmYmYtYmQwZC01YjdjLTQ2ZWYtYmZiZGVmYmZiZDM0Iiwicm9sZXMiOlsiYWRtaW5pc3RyYXRvciJdfQ.Lb-zUa9DL7swdVSEuPTqaR9FvLgKwuEtrhxiJFWjhU8';

return [
	// Valid responses
	//////////////////
	'update'       => [
		'/v1/roles/efbfbdef-bfbd-efbf-bd0f-efbfbd5c4f61',
		'Bearer ' . VALID_TOKEN,
		file_get_contents(__DIR__ . '/requests/roles.update.json'),
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/roles.update.json',
	],

	// Invalid responses
	////////////////////
	'invalidType'  => [
		'/v1/roles/efbfbdef-bfbd-efbf-bd0f-efbfbd5c4f61',
		'Bearer ' . VALID_TOKEN,
		file_get_contents(__DIR__ . '/requests/roles.update.invalidType.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/roles.invalidType.json',
	],
	'idMismatch'   => [
		'/v1/roles/efbfbdef-bfbd-efbf-bd0f-efbfbd5c4f61',
		'Bearer ' . VALID_TOKEN,
		file_get_contents(__DIR__ . '/requests/roles.update.idMismatch.json'),
		StatusCodeInterface::STATUS_BAD_REQUEST,
		__DIR__ . '/responses/invalid.identifier.json',
	],
	'notFound'     => [
		'/v1/roles/0046d3d6-c980-494a-8b40-f19e6095e610',
		'Bearer ' . VALID_TOKEN,
		file_get_contents(__DIR__ . '/requests/roles.update.notFound.json'),
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/roles.notFound.json',
	],
	'invalidToken' => [
		'/v1/roles/efbfbdef-bfbd-efbf-bd0f-efbfbd5c4f61',
		'Bearer ' . INVALID_TOKEN,
		file_get_contents(__DIR__ . '/requests/roles.update.json'),
		StatusCodeInterface::STATUS_UNAUTHORIZED,
		__DIR__ . '/responses/unauthorized.json',
	],
	'noToken'      => [
		'/v1/roles/efbfbdef-bfbd-efbf-bd0f-efbfbd5c4f61',
		null,
		file_get_contents(__DIR__ . '/requests/roles.update.json'),
		StatusCodeInterface::STATUS_FORBIDDEN,
		__DIR__ . '/responses/forbidden.json',
	],
	'expiredToken' => [
		'/v1/roles/efbfbdef-bfbd-efbf-bd0f-efbfbd5c4f61',
		'Bearer ' . EXPIRED_TOKEN,
		file_get_contents(__DIR__ . '/requests/roles.update.json'),
		StatusCodeInterface::STATUS_UNAUTHORIZED,
		__DIR__ . '/responses/unauthorized.json',
	],
];
