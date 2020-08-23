<?php declare(strict_types = 1);

use FastyBird\AuthNode\Schemas;
use Fig\Http\Message\StatusCodeInterface;

const ADMINISTRATOR_TOKEN = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJqdGkiOiI5YWY1NjI0Mi01ZDg3LTQzNjQtYmIxZS1kOWZjODI4NmIzZmYiLCJpc3MiOiJjb20uZmFzdHliaXJkLmF1dGgtbm9kZSIsImlhdCI6MTU4NTc0MjQwMCwiZXhwIjoxNTg1NzQ5NjAwLCJ1c2VyIjoiNWU3OWVmYmYtYmQwZC01YjdjLTQ2ZWYtYmZiZGVmYmZiZDM0Iiwicm9sZXMiOlsiYWRtaW5pc3RyYXRvciJdfQ.Lb-zUa9DL7swdVSEuPTqaR9FvLgKwuEtrhxiJFWjhU8';
const EXPIRED_TOKEN = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJqdGkiOiI3MjFlMTAyNS04Zjc4LTQzOGQtODIwZi0wZDQ2OWEzNzk1NWQiLCJpc3MiOiJjb20uZmFzdHliaXJkLmF1dGgtbm9kZSIsImlhdCI6MTU3Nzg4MDAwMCwiZXhwIjoxNTc3OTAxNjAwLCJ1c2VyIjoiNTI1ZDZhMDktN2MwNi00NmQyLWFmZmEtNzA5YmIxODM3MDdlIiwicm9sZXMiOlsiYWRtaW5pc3RyYXRvciJdfQ.F9veOiNfcqQVxpbMF7OY5j1AcPLpPQb8dEIZbrBmh24';
const INVALID_TOKEN = 'eyJqdGkiOiI5YWY1NjI0Mi01ZDg3LTQzNjQtYmIxZS1kOWZjODI4NmIzZmYiLCJpc3MiOiJjb20uZmFzdHliaXJkLmF1dGgtbm9kZSIsImlhdCI6MTU4NTc0MjQwMCwiZXhwIjoxNTg1NzQ5NjAwLCJ1c2VyIjoiNWU3OWVmYmYtYmQwZC01YjdjLTQ2ZWYtYmZiZGVmYmZiZDM0Iiwicm9sZXMiOlsiYWRtaW5pc3RyYXRvciJdfQ.Lb-zUa9DL7swdVSEuPTqaR9FvLgKwuEtrhxiJFWjhU8';
const USER_TOKEN = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJqdGkiOiI3YzVkNzdhZC1kOTNlLTRjMmMtOThlNS05ZTFhZmM0NDQ2MTUiLCJpc3MiOiJjb20uZmFzdHliaXJkLmF1dGgtbm9kZSIsImlhdCI6MTU4NTc0MjQwMCwiZXhwIjoxNTg1NzQ5NjAwLCJ1c2VyIjoiZWZiZmJkZWYtYmZiZC02OGVmLWJmYmQtNzcwYjQwZWZiZmJkIiwicm9sZXMiOlsidXNlciJdfQ.cbatWCuGX-K8XbF9MMN7DqxV9hriWmUSGcDGGmnxXX0';

const ADMINISTRATOR_ACCOUNT_ID = '5e79efbf-bd0d-5b7c-46ef-bfbdefbfbd34';
const USER_ACCOUNT_ID = 'efbfbdef-bfbd-68ef-bfbd-770b40efbfbd';
const CHILD_USER_ACCOUNT_ID = 'efbfbdef-bfbd-68ef-bfbd-770b40efbfbd';
const MACHINE_ACCOUNT_ID = '16e5db29-0006-4484-ac38-5cdea5a008f5';
const ADMINISTRATOR_IDENTITY_ID = '77331268-efbf-bd34-49ef-bfbdefbfbd04';
const USER_IDENTITY_ID = 'faf7a863-a49c-4428-a757-1de537773355';
const MACHINE_IDENTITY_ID = '35be5624-0160-4323-83ee-6f59000934b4';
const UNKNOWN_ID = '83985c13-238c-46bd-aacb-2359d5c921a7';

return [
	// Valid responses
	//////////////////
	'readAll'                  => [
		'/v1/accounts/' . USER_ACCOUNT_ID . '/identities',
		'Bearer ' . ADMINISTRATOR_TOKEN,
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/identities/identities.index.json',
	],
	'readAllPaging'            => [
		'/v1/accounts/' . USER_ACCOUNT_ID . '/identities?page[offset]=1&page[limit]=1',
		'Bearer ' . ADMINISTRATOR_TOKEN,
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/identities/identities.index.paging.json',
	],
	'readOneUser'              => [
		'/v1/accounts/' . ADMINISTRATOR_ACCOUNT_ID . '/identities/' . ADMINISTRATOR_IDENTITY_ID,
		'Bearer ' . ADMINISTRATOR_TOKEN,
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/identities/identities.read.user.json',
	],
	'readOneMachine'           => [
		'/v1/accounts/' . MACHINE_ACCOUNT_ID . '/identities/' . MACHINE_IDENTITY_ID,
		'Bearer ' . ADMINISTRATOR_TOKEN,
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/identities/identities.read.machine.json',
	],
	'readRelationshipsAccount' => [
		'/v1/accounts/' . ADMINISTRATOR_ACCOUNT_ID . '/identities/' . ADMINISTRATOR_IDENTITY_ID . '/relationships/' . Schemas\Identities\IdentitySchema::RELATIONSHIPS_ACCOUNT,
		'Bearer ' . ADMINISTRATOR_TOKEN,
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/identities/identities.relationships.account.json',
	],

	// Invalid responses
	////////////////////
	'readOneUnknown'           => [
		'/v1/accounts/' . ADMINISTRATOR_ACCOUNT_ID . '/identities/' . UNKNOWN_ID,
		'Bearer ' . ADMINISTRATOR_TOKEN,
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/generic/notFound.json',
	],
	'readRelationshipsUnknown' => [
		'/v1/accounts/' . ADMINISTRATOR_ACCOUNT_ID . '/identities/' . ADMINISTRATOR_IDENTITY_ID . '/relationships/unknown',
		'Bearer ' . ADMINISTRATOR_TOKEN,
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/generic/relation.unknown.json',
	],
	'readAllNoToken'           => [
		'/v1/accounts/' . ADMINISTRATOR_ACCOUNT_ID . '/identities',
		null,
		StatusCodeInterface::STATUS_FORBIDDEN,
		__DIR__ . '/responses/generic/forbidden.json',
	],
	'readOneNoToken'           => [
		'/v1/accounts/' . ADMINISTRATOR_ACCOUNT_ID . '/identities/' . ADMINISTRATOR_IDENTITY_ID,
		null,
		StatusCodeInterface::STATUS_FORBIDDEN,
		__DIR__ . '/responses/generic/forbidden.json',
	],
	'readAllEmptyToken'        => [
		'/v1/accounts/' . ADMINISTRATOR_ACCOUNT_ID . '/identities',
		'',
		StatusCodeInterface::STATUS_FORBIDDEN,
		__DIR__ . '/responses/generic/forbidden.json',
	],
	'readOneEmptyToken'        => [
		'/v1/accounts/' . ADMINISTRATOR_ACCOUNT_ID . '/identities/' . ADMINISTRATOR_IDENTITY_ID,
		'',
		StatusCodeInterface::STATUS_FORBIDDEN,
		__DIR__ . '/responses/generic/forbidden.json',
	],
	'readAllUserToken'         => [
		'/v1/accounts/' . ADMINISTRATOR_ACCOUNT_ID . '/identities',
		'Bearer ' . USER_TOKEN,
		StatusCodeInterface::STATUS_FORBIDDEN,
		__DIR__ . '/responses/generic/forbidden.json',
	],
	'readOneUserToken'         => [
		'/v1/accounts/' . ADMINISTRATOR_ACCOUNT_ID . '/identities/' . ADMINISTRATOR_IDENTITY_ID,
		'Bearer ' . USER_TOKEN,
		StatusCodeInterface::STATUS_FORBIDDEN,
		__DIR__ . '/responses/generic/forbidden.json',
	],
	'readAllInvalidToken'      => [
		'/v1/accounts/' . ADMINISTRATOR_ACCOUNT_ID . '/identities',
		'Bearer ' . INVALID_TOKEN,
		StatusCodeInterface::STATUS_UNAUTHORIZED,
		__DIR__ . '/responses/generic/unauthorized.json',
	],
	'readOneInvalidToken'      => [
		'/v1/accounts/' . ADMINISTRATOR_ACCOUNT_ID . '/identities/' . ADMINISTRATOR_IDENTITY_ID,
		'Bearer ' . INVALID_TOKEN,
		StatusCodeInterface::STATUS_UNAUTHORIZED,
		__DIR__ . '/responses/generic/unauthorized.json',
	],
	'readAllExpiredToken'      => [
		'/v1/accounts/' . ADMINISTRATOR_ACCOUNT_ID . '/identities',
		'Bearer ' . EXPIRED_TOKEN,
		StatusCodeInterface::STATUS_UNAUTHORIZED,
		__DIR__ . '/responses/generic/unauthorized.json',
	],
	'readOneExpiredToken'      => [
		'/v1/accounts/' . ADMINISTRATOR_ACCOUNT_ID . '/identities/' . ADMINISTRATOR_IDENTITY_ID,
		'Bearer ' . EXPIRED_TOKEN,
		StatusCodeInterface::STATUS_UNAUTHORIZED,
		__DIR__ . '/responses/generic/unauthorized.json',
	],
];
