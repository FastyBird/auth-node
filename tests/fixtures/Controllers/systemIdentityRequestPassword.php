<?php declare(strict_types = 1);

use Fig\Http\Message\StatusCodeInterface;

return [
	'request' => [
		'/v1/password-reset',
		file_get_contents(__DIR__ . '/requests/systemIdentity.passwordRequest.json'),
		StatusCodeInterface::STATUS_NO_CONTENT,
		__DIR__ . '/responses/systemIdentity.passwordRequest.json',
	],
	'missingRequired' => [
		'/v1/password-reset',
		file_get_contents(__DIR__ . '/requests/systemIdentity.passwordRequest.missing.required.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/systemIdentity.passwordRequest.missingRequired.json',
	],
	'invalidType' => [
		'/v1/password-reset',
		file_get_contents(__DIR__ . '/requests/systemIdentity.passwordRequest.invalidType.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/systemIdentity.invalidType.json',
	],
	'invalid' => [
		'/v1/password-reset',
		file_get_contents(__DIR__ . '/requests/systemIdentity.passwordRequest.invalid.json'),
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/systemIdentity.passwordRequest.invalid.json',
	],
	'deleted' => [
		'/v1/password-reset',
		file_get_contents(__DIR__ . '/requests/systemIdentity.passwordRequest.deleted.json'),
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/systemIdentity.passwordRequest.deleted.json',
	],
	'blocked' => [
		'/v1/password-reset',
		file_get_contents(__DIR__ . '/requests/systemIdentity.passwordRequest.blocked.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/systemIdentity.passwordRequest.blocked.json',
	],
	'notActivated' => [
		'/v1/password-reset',
		file_get_contents(__DIR__ . '/requests/systemIdentity.passwordRequest.notActivated.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/systemIdentity.passwordRequest.notActivated.json',
	],
];
