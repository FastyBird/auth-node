<?php declare(strict_types = 1);

use Fig\Http\Message\StatusCodeInterface;

return [
	// Valid responses
	//////////////////
	'request'         => [
		'/v1/password-reset',
		file_get_contents(__DIR__ . '/requests/account/identities/account.identities.passwordRequest.json'),
		StatusCodeInterface::STATUS_NO_CONTENT,
		__DIR__ . '/responses/account/identities/account.identities.passwordRequest.json',
	],

	// Invalid responses
	////////////////////
	'missingRequired' => [
		'/v1/password-reset',
		file_get_contents(__DIR__ . '/requests/account/identities/account.identities.passwordRequest.missing.required.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/account/identities/account.identities.passwordRequest.missing.required.json',
	],
	'invalidType'     => [
		'/v1/password-reset',
		file_get_contents(__DIR__ . '/requests/account/identities/account.identities.passwordRequest.invalidType.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/generic/invalid.type.json',
	],
	'unknown'         => [
		'/v1/password-reset',
		file_get_contents(__DIR__ . '/requests/account/identities/account.identities.passwordRequest.invalid.json'),
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/generic/notFound.json',
	],
	'deleted'         => [
		'/v1/password-reset',
		file_get_contents(__DIR__ . '/requests/account/identities/account.identities.passwordRequest.deleted.json'),
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/generic/notFound.json',
	],
	'blocked'         => [
		'/v1/password-reset',
		file_get_contents(__DIR__ . '/requests/account/identities/account.identities.passwordRequest.blocked.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/account/identities/account.identities.passwordRequest.blocked.json',
	],
	'notActivated'    => [
		'/v1/password-reset',
		file_get_contents(__DIR__ . '/requests/account/identities/account.identities.passwordRequest.notActivated.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/account/identities/account.identities.passwordRequest.notActivated.json',
	],
];
