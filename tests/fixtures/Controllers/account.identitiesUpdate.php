<?php declare(strict_types = 1);

use Fig\Http\Message\StatusCodeInterface;

const VALID_TOKEN = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJqdGkiOiI5YWY1NjI0Mi01ZDg3LTQzNjQtYmIxZS1kOWZjODI4NmIzZmYiLCJpc3MiOiJjb20uZmFzdHliaXJkLmF1dGgtbm9kZSIsImlhdCI6MTU4NTc0MjQwMCwiZXhwIjoxNTg1NzQ5NjAwLCJ1c2VyIjoiNWU3OWVmYmYtYmQwZC01YjdjLTQ2ZWYtYmZiZGVmYmZiZDM0Iiwicm9sZXMiOlsiYWRtaW5pc3RyYXRvciJdfQ.Lb-zUa9DL7swdVSEuPTqaR9FvLgKwuEtrhxiJFWjhU8';
const EXPIRED_TOKEN = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJqdGkiOiI3MjFlMTAyNS04Zjc4LTQzOGQtODIwZi0wZDQ2OWEzNzk1NWQiLCJpc3MiOiJjb20uZmFzdHliaXJkLmF1dGgtbm9kZSIsImlhdCI6MTU3Nzg4MDAwMCwiZXhwIjoxNTc3OTAxNjAwLCJ1c2VyIjoiNTI1ZDZhMDktN2MwNi00NmQyLWFmZmEtNzA5YmIxODM3MDdlIiwicm9sZXMiOlsiYWRtaW5pc3RyYXRvciJdfQ.F9veOiNfcqQVxpbMF7OY5j1AcPLpPQb8dEIZbrBmh24';
const INVALID_TOKEN = 'eyJqdGkiOiI5YWY1NjI0Mi01ZDg3LTQzNjQtYmIxZS1kOWZjODI4NmIzZmYiLCJpc3MiOiJjb20uZmFzdHliaXJkLmF1dGgtbm9kZSIsImlhdCI6MTU4NTc0MjQwMCwiZXhwIjoxNTg1NzQ5NjAwLCJ1c2VyIjoiNWU3OWVmYmYtYmQwZC01YjdjLTQ2ZWYtYmZiZGVmYmZiZDM0Iiwicm9sZXMiOlsiYWRtaW5pc3RyYXRvciJdfQ.Lb-zUa9DL7swdVSEuPTqaR9FvLgKwuEtrhxiJFWjhU8';

return [
	// Valid responses
	//////////////////
	'update'          => [
		'/v1/me/identities/77331268-efbf-bd34-49ef-bfbdefbfbd04',
		'Bearer ' . VALID_TOKEN,
		file_get_contents(__DIR__ . '/requests/account.identities.update.json'),
		StatusCodeInterface::STATUS_NO_CONTENT,
		__DIR__ . '/responses/account.identities.update.json',
	],

	// Invalid responses
	////////////////////
	'invalidPassword' => [
		'/v1/me/identities/77331268-efbf-bd34-49ef-bfbdefbfbd04',
		'Bearer ' . VALID_TOKEN,
		file_get_contents(__DIR__ . '/requests/account.identities.update.invalid.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/account.identities.update.invalid.json',
	],
	'missingRequired' => [
		'/v1/me/identities/77331268-efbf-bd34-49ef-bfbdefbfbd04',
		'Bearer ' . VALID_TOKEN,
		file_get_contents(__DIR__ . '/requests/account.identities.update.missing.required.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/account.identities.update.missingRequired.json',
	],
	'invalidType'     => [
		'/v1/me/identities/77331268-efbf-bd34-49ef-bfbdefbfbd04',
		'Bearer ' . VALID_TOKEN,
		file_get_contents(__DIR__ . '/requests/account.identities.update.invalidType.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/account.identities.invalidType.json',
	],
	'invalidToken'    => [
		'/v1/me/identities/77331268-efbf-bd34-49ef-bfbdefbfbd04',
		'Bearer ' . INVALID_TOKEN,
		file_get_contents(__DIR__ . '/requests/account.identities.update.json'),
		StatusCodeInterface::STATUS_UNAUTHORIZED,
		__DIR__ . '/responses/unauthorized.json',
	],
	'noToken'         => [
		'/v1/me/identities/77331268-efbf-bd34-49ef-bfbdefbfbd04',
		null,
		file_get_contents(__DIR__ . '/requests/account.identities.update.json'),
		StatusCodeInterface::STATUS_FORBIDDEN,
		__DIR__ . '/responses/forbidden.json',
	],
	'expiredToken'    => [
		'/v1/me/identities/77331268-efbf-bd34-49ef-bfbdefbfbd04',
		'Bearer ' . EXPIRED_TOKEN,
		file_get_contents(__DIR__ . '/requests/account.identities.update.json'),
		StatusCodeInterface::STATUS_UNAUTHORIZED,
		__DIR__ . '/responses/unauthorized.json',
	],
];
