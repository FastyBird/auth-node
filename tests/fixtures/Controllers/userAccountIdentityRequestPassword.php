<?php declare(strict_types = 1);

use Fig\Http\Message\StatusCodeInterface;

return [
	'request' => [
		'/v1/password-reset',
		file_get_contents(__DIR__ . '/requests/userAccountIdentity.passwordRequest.json'),
		StatusCodeInterface::STATUS_NO_CONTENT,
		__DIR__ . '/responses/userAccountIdentity.passwordRequest.json',
	],
	'missingRequired' => [
		'/v1/password-reset',
		file_get_contents(__DIR__ . '/requests/userAccountIdentity.passwordRequest.missing.required.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/userAccountIdentity.passwordRequest.missingRequired.json',
	],
	'invalidType' => [
		'/v1/password-reset',
		file_get_contents(__DIR__ . '/requests/userAccountIdentity.passwordRequest.invalidType.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/userAccountIdentity.invalidType.json',
	],
	'invalid' => [
		'/v1/password-reset',
		file_get_contents(__DIR__ . '/requests/userAccountIdentity.passwordRequest.invalid.json'),
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/userAccountIdentity.passwordRequest.invalid.json',
	],
	'deleted' => [
		'/v1/password-reset',
		file_get_contents(__DIR__ . '/requests/userAccountIdentity.passwordRequest.deleted.json'),
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/userAccountIdentity.passwordRequest.deleted.json',
	],
	'blocked' => [
		'/v1/password-reset',
		file_get_contents(__DIR__ . '/requests/userAccountIdentity.passwordRequest.blocked.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/userAccountIdentity.passwordRequest.blocked.json',
	],
	'notActivated' => [
		'/v1/password-reset',
		file_get_contents(__DIR__ . '/requests/userAccountIdentity.passwordRequest.notActivated.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/userAccountIdentity.passwordRequest.notActivated.json',
	],
];
