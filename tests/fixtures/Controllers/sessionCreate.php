<?php declare(strict_types = 1);

use Fig\Http\Message\StatusCodeInterface;

return [
	'create'          => [
		'/v1/session',
		file_get_contents(__DIR__ . '/requests/session.create.json'),
		StatusCodeInterface::STATUS_CREATED,
		__DIR__ . '/responses/session.create.json',
	],
	'missingRequired' => [
		'/v1/session',
		file_get_contents(__DIR__ . '/requests/session.create.missing.required.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/session.create.missingRequired.json',
	],
	'unknown'         => [
		'/v1/session',
		file_get_contents(__DIR__ . '/requests/session.create.unknown.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/session.create.unknown.json',
	],
	'invalid'         => [
		'/v1/session',
		file_get_contents(__DIR__ . '/requests/session.create.invalid.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/session.create.invalid.json',
	],
	'deleted'         => [
		'/v1/session',
		file_get_contents(__DIR__ . '/requests/session.create.deleted.json'),
		StatusCodeInterface::STATUS_FORBIDDEN,
		__DIR__ . '/responses/session.create.deleted.json',
	],
	'blocked'         => [
		'/v1/session',
		file_get_contents(__DIR__ . '/requests/session.create.blocked.json'),
		StatusCodeInterface::STATUS_FORBIDDEN,
		__DIR__ . '/responses/session.create.blocked.json',
	],
	'notActivated'    => [
		'/v1/session',
		file_get_contents(__DIR__ . '/requests/session.create.notActivated.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/session.create.notActivated.json',
	],
	'approvalWaiting' => [
		'/v1/session',
		file_get_contents(__DIR__ . '/requests/session.create.approvalWaiting.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/session.create.approvalWaiting.json',
	],
];
