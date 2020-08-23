<?php declare(strict_types = 1);

use Fig\Http\Message\StatusCodeInterface;

const ADMINISTRATOR_TOKEN = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJqdGkiOiI5YWY1NjI0Mi01ZDg3LTQzNjQtYmIxZS1kOWZjODI4NmIzZmYiLCJpc3MiOiJjb20uZmFzdHliaXJkLmF1dGgtbm9kZSIsImlhdCI6MTU4NTc0MjQwMCwiZXhwIjoxNTg1NzQ5NjAwLCJ1c2VyIjoiNWU3OWVmYmYtYmQwZC01YjdjLTQ2ZWYtYmZiZGVmYmZiZDM0Iiwicm9sZXMiOlsiYWRtaW5pc3RyYXRvciJdfQ.Lb-zUa9DL7swdVSEuPTqaR9FvLgKwuEtrhxiJFWjhU8';
const EXPIRED_TOKEN = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJqdGkiOiI3MjFlMTAyNS04Zjc4LTQzOGQtODIwZi0wZDQ2OWEzNzk1NWQiLCJpc3MiOiJjb20uZmFzdHliaXJkLmF1dGgtbm9kZSIsImlhdCI6MTU3Nzg4MDAwMCwiZXhwIjoxNTc3OTAxNjAwLCJ1c2VyIjoiNTI1ZDZhMDktN2MwNi00NmQyLWFmZmEtNzA5YmIxODM3MDdlIiwicm9sZXMiOlsiYWRtaW5pc3RyYXRvciJdfQ.F9veOiNfcqQVxpbMF7OY5j1AcPLpPQb8dEIZbrBmh24';
const INVALID_TOKEN = 'eyJqdGkiOiI5YWY1NjI0Mi01ZDg3LTQzNjQtYmIxZS1kOWZjODI4NmIzZmYiLCJpc3MiOiJjb20uZmFzdHliaXJkLmF1dGgtbm9kZSIsImlhdCI6MTU4NTc0MjQwMCwiZXhwIjoxNTg1NzQ5NjAwLCJ1c2VyIjoiNWU3OWVmYmYtYmQwZC01YjdjLTQ2ZWYtYmZiZGVmYmZiZDM0Iiwicm9sZXMiOlsiYWRtaW5pc3RyYXRvciJdfQ.Lb-zUa9DL7swdVSEuPTqaR9FvLgKwuEtrhxiJFWjhU8';
const USER_TOKEN = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJqdGkiOiI3YzVkNzdhZC1kOTNlLTRjMmMtOThlNS05ZTFhZmM0NDQ2MTUiLCJpc3MiOiJjb20uZmFzdHliaXJkLmF1dGgtbm9kZSIsImlhdCI6MTU4NTc0MjQwMCwiZXhwIjoxNTg1NzQ5NjAwLCJ1c2VyIjoiZWZiZmJkZWYtYmZiZC02OGVmLWJmYmQtNzcwYjQwZWZiZmJkIiwicm9sZXMiOlsidXNlciJdfQ.cbatWCuGX-K8XbF9MMN7DqxV9hriWmUSGcDGGmnxXX0';

const ADMINISTRATOR_ACCOUNT_ID = '5e79efbf-bd0d-5b7c-46ef-bfbdefbfbd34';
const USER_ACCOUNT_ID = 'efbfbdef-bfbd-68ef-bfbd-770b40efbfbd';

return [
	// Valid responses
	//////////////////
	'create'              => [
		'/v1/accounts/' . ADMINISTRATOR_ACCOUNT_ID . '/emails',
		'Bearer ' . ADMINISTRATOR_TOKEN,
		file_get_contents(__DIR__ . '/requests/emails/emails.create.json'),
		StatusCodeInterface::STATUS_CREATED,
		__DIR__ . '/responses/emails/emails.create.json',
	],

	// Invalid responses
	////////////////////
	'missingRequired'     => [
		'/v1/accounts/' . ADMINISTRATOR_ACCOUNT_ID . '/emails',
		'Bearer ' . ADMINISTRATOR_TOKEN,
		file_get_contents(__DIR__ . '/requests/emails/emails.create.missing.required.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/emails/emails.create.missing.required.json',
	],
	'missingRelation'     => [
		'/v1/accounts/' . ADMINISTRATOR_ACCOUNT_ID . '/emails',
		'Bearer ' . ADMINISTRATOR_TOKEN,
		file_get_contents(__DIR__ . '/requests/emails/emails.create.missing.relation.json'),
		StatusCodeInterface::STATUS_BAD_REQUEST,
		__DIR__ . '/responses/emails/emails.create.missing.relation.json',
	],
	'invalidType'         => [
		'/v1/accounts/' . ADMINISTRATOR_ACCOUNT_ID . '/emails',
		'Bearer ' . ADMINISTRATOR_TOKEN,
		file_get_contents(__DIR__ . '/requests/emails/emails.create.invalid.type.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/generic/invalid.type.json',
	],
	'identifierNotUnique' => [
		'/v1/accounts/' . ADMINISTRATOR_ACCOUNT_ID . '/emails',
		'Bearer ' . ADMINISTRATOR_TOKEN,
		file_get_contents(__DIR__ . '/requests/emails/emails.create.identifier.notUnique.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/generic/identifier.notUnique.json',
	],
	'invalidEmail'    => [
		'/v1/accounts/' . ADMINISTRATOR_ACCOUNT_ID . '/emails',
		'Bearer ' . ADMINISTRATOR_TOKEN,
		file_get_contents(__DIR__ . '/requests/emails/emails.create.invalid.email.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/emails/emails.create.invalid.email.json',
	],
	'usedEmail'             => [
		'/v1/accounts/' . ADMINISTRATOR_ACCOUNT_ID . '/emails',
		'Bearer ' . ADMINISTRATOR_TOKEN,
		file_get_contents(__DIR__ . '/requests/emails/emails.create.usedEmail.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/emails/emails.create.usedEmail.json',
	],
	'noToken'             => [
		'/v1/accounts/' . ADMINISTRATOR_ACCOUNT_ID . '/emails',
		null,
		file_get_contents(__DIR__ . '/requests/emails/emails.create.json'),
		StatusCodeInterface::STATUS_FORBIDDEN,
		__DIR__ . '/responses/generic/forbidden.json',
	],
	'emptyToken'          => [
		'/v1/accounts/' . ADMINISTRATOR_ACCOUNT_ID . '/emails',
		'',
		file_get_contents(__DIR__ . '/requests/emails/emails.create.json'),
		StatusCodeInterface::STATUS_FORBIDDEN,
		__DIR__ . '/responses/generic/forbidden.json',
	],
	'userToken'           => [
		'/v1/accounts/' . ADMINISTRATOR_ACCOUNT_ID . '/emails',
		'Bearer ' . USER_TOKEN,
		file_get_contents(__DIR__ . '/responses/emails/emails.create.json'),
		StatusCodeInterface::STATUS_FORBIDDEN,
		__DIR__ . '/responses/generic/forbidden.json',
	],
	'invalidToken'        => [
		'/v1/accounts/' . ADMINISTRATOR_ACCOUNT_ID . '/emails',
		'Bearer ' . INVALID_TOKEN,
		file_get_contents(__DIR__ . '/requests/emails/emails.create.json'),
		StatusCodeInterface::STATUS_UNAUTHORIZED,
		__DIR__ . '/responses/generic/unauthorized.json',
	],
	'expiredToken'        => [
		'/v1/accounts/' . ADMINISTRATOR_ACCOUNT_ID . '/emails',
		'Bearer ' . EXPIRED_TOKEN,
		file_get_contents(__DIR__ . '/requests/emails/emails.create.json'),
		StatusCodeInterface::STATUS_UNAUTHORIZED,
		__DIR__ . '/responses/generic/unauthorized.json',
	],
];
