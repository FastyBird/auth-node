<?php declare(strict_types = 1);

use Fig\Http\Message\StatusCodeInterface;

return [
	// Valid responses
	//////////////////
	'request'         => [
		'/v1/password-reset',
		file_get_contents(__DIR__ . '/requests/account.identities.passwordRequest.json'),
		StatusCodeInterface::STATUS_NO_CONTENT,
		__DIR__ . '/responses/account.identities.passwordRequest.json',
	],

	// Invalid responses
	////////////////////
	'missingRequired' => [
		'/v1/password-reset',
		file_get_contents(__DIR__ . '/requests/account.identities.passwordRequest.missing.required.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/account.identities.passwordRequest.missingRequired.json',
	],
	'invalidType'     => [
		'/v1/password-reset',
		file_get_contents(__DIR__ . '/requests/account.identities.passwordRequest.invalidType.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/account.identities.invalidType.json',
	],
	'invalid'         => [
		'/v1/password-reset',
		file_get_contents(__DIR__ . '/requests/account.identities.passwordRequest.invalid.json'),
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/account.identities.passwordRequest.invalid.json',
	],
	'deleted'         => [
		'/v1/password-reset',
		file_get_contents(__DIR__ . '/requests/account.identities.passwordRequest.deleted.json'),
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/account.identities.passwordRequest.deleted.json',
	],
	'blocked'         => [
		'/v1/password-reset',
		file_get_contents(__DIR__ . '/requests/account.identities.passwordRequest.blocked.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/account.identities.passwordRequest.blocked.json',
	],
	'notActivated'    => [
		'/v1/password-reset',
		file_get_contents(__DIR__ . '/requests/account.identities.passwordRequest.notActivated.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/account.identities.passwordRequest.notActivated.json',
	],
];
