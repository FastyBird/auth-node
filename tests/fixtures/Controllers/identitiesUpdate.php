<?php declare(strict_types = 1);

use Fig\Http\Message\StatusCodeInterface;

const VALID_TOKEN = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJqdGkiOiI5YWY1NjI0Mi01ZDg3LTQzNjQtYmIxZS1kOWZjODI4NmIzZmYiLCJpc3MiOiJjb20uZmFzdHliaXJkLmF1dGgtbm9kZSIsImlhdCI6MTU4NTc0MjQwMCwiZXhwIjoxNTg1NzQ5NjAwLCJ1c2VyIjoiNWU3OWVmYmYtYmQwZC01YjdjLTQ2ZWYtYmZiZGVmYmZiZDM0Iiwicm9sZXMiOlsiYWRtaW5pc3RyYXRvciJdfQ.Lb-zUa9DL7swdVSEuPTqaR9FvLgKwuEtrhxiJFWjhU8';
const EXPIRED_TOKEN = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJqdGkiOiI3MjFlMTAyNS04Zjc4LTQzOGQtODIwZi0wZDQ2OWEzNzk1NWQiLCJpc3MiOiJjb20uZmFzdHliaXJkLmF1dGgtbm9kZSIsImlhdCI6MTU3Nzg4MDAwMCwiZXhwIjoxNTc3OTAxNjAwLCJ1c2VyIjoiNTI1ZDZhMDktN2MwNi00NmQyLWFmZmEtNzA5YmIxODM3MDdlIiwicm9sZXMiOlsiYWRtaW5pc3RyYXRvciJdfQ.F9veOiNfcqQVxpbMF7OY5j1AcPLpPQb8dEIZbrBmh24';
const INVALID_TOKEN = 'eyJqdGkiOiI5YWY1NjI0Mi01ZDg3LTQzNjQtYmIxZS1kOWZjODI4NmIzZmYiLCJpc3MiOiJjb20uZmFzdHliaXJkLmF1dGgtbm9kZSIsImlhdCI6MTU4NTc0MjQwMCwiZXhwIjoxNTg1NzQ5NjAwLCJ1c2VyIjoiNWU3OWVmYmYtYmQwZC01YjdjLTQ2ZWYtYmZiZGVmYmZiZDM0Iiwicm9sZXMiOlsiYWRtaW5pc3RyYXRvciJdfQ.Lb-zUa9DL7swdVSEuPTqaR9FvLgKwuEtrhxiJFWjhU8';
const VALID_TOKEN_USER = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJqdGkiOiI3YzVkNzdhZC1kOTNlLTRjMmMtOThlNS05ZTFhZmM0NDQ2MTUiLCJpc3MiOiJjb20uZmFzdHliaXJkLmF1dGgtbm9kZSIsImlhdCI6MTU4NTc0MjQwMCwiZXhwIjoxNTg1NzQ5NjAwLCJ1c2VyIjoiZWZiZmJkZWYtYmZiZC02OGVmLWJmYmQtNzcwYjQwZWZiZmJkIiwicm9sZXMiOlsidXNlciJdfQ.cbatWCuGX-K8XbF9MMN7DqxV9hriWmUSGcDGGmnxXX0';

return [
	// Valid responses
	//////////////////
	'update'       => [
		'/v1/accounts/16e5db29-0006-4484-ac38-5cdea5a008f5/identities/35be5624-0160-4323-83ee-6f59000934b4',
		'Bearer ' . VALID_TOKEN,
		file_get_contents(__DIR__ . '/requests/identities.update.machine.json'),
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/identities.update.machine.json',
	],

	// Invalid responses
	////////////////////
	'unknownEntity'  => [
		'/v1/accounts/17c59dfa-2edd-438e-8c49-faa4e38e5ae5/identities/35be5624-0160-4323-83ee-6f59000934b4',
		'Bearer ' . VALID_TOKEN,
		file_get_contents(__DIR__ . '/requests/identities.update.machine.json'),
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/accounts.notFound.json',
	],
	'invalidType'  => [
		'/v1/accounts/16e5db29-0006-4484-ac38-5cdea5a008f5/identities/35be5624-0160-4323-83ee-6f59000934b4',
		'Bearer ' . VALID_TOKEN,
		file_get_contents(__DIR__ . '/requests/identities.update.invalidType.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/identities.invalidType.json',
	],
	'idMismatch'   => [
		'/v1/accounts/16e5db29-0006-4484-ac38-5cdea5a008f5/identities/35be5624-0160-4323-83ee-6f59000934b4',
		'Bearer ' . VALID_TOKEN,
		file_get_contents(__DIR__ . '/requests/identities.update.idMismatch.json'),
		StatusCodeInterface::STATUS_BAD_REQUEST,
		__DIR__ . '/responses/invalid.identifier.json',
	],
	'invalidToken' => [
		'/v1/accounts/16e5db29-0006-4484-ac38-5cdea5a008f5/identities/35be5624-0160-4323-83ee-6f59000934b4',
		'Bearer ' . INVALID_TOKEN,
		file_get_contents(__DIR__ . '/requests/identities.update.machine.json'),
		StatusCodeInterface::STATUS_UNAUTHORIZED,
		__DIR__ . '/responses/unauthorized.json',
	],
	'noToken'      => [
		'/v1/accounts/16e5db29-0006-4484-ac38-5cdea5a008f5/identities/35be5624-0160-4323-83ee-6f59000934b4',
		null,
		file_get_contents(__DIR__ . '/requests/identities.update.machine.json'),
		StatusCodeInterface::STATUS_FORBIDDEN,
		__DIR__ . '/responses/forbidden.json',
	],
	'expiredToken' => [
		'/v1/accounts/16e5db29-0006-4484-ac38-5cdea5a008f5/identities/35be5624-0160-4323-83ee-6f59000934b4',
		'Bearer ' . EXPIRED_TOKEN,
		file_get_contents(__DIR__ . '/requests/identities.update.machine.json'),
		StatusCodeInterface::STATUS_UNAUTHORIZED,
		__DIR__ . '/responses/unauthorized.json',
	],
	'userToken'    => [
		'/v1/accounts/16e5db29-0006-4484-ac38-5cdea5a008f5/identities/35be5624-0160-4323-83ee-6f59000934b4',
		'Bearer ' . VALID_TOKEN_USER,
		file_get_contents(__DIR__ . '/requests/identities.update.machine.json'),
		StatusCodeInterface::STATUS_FORBIDDEN,
		__DIR__ . '/responses/forbidden.json',
	],
];
