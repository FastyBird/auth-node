<?php declare(strict_types = 1);

use Fig\Http\Message\StatusCodeInterface;

return [
	'update' => [
		'/v1/session',
		file_get_contents(__DIR__ . '/requests/session.update.json'),
		StatusCodeInterface::STATUS_CREATED,
		__DIR__ . '/responses/session.update.json',
	],
	'missingRequired' => [
		'/v1/session',
		file_get_contents(__DIR__ . '/requests/session.update.missing.required.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/session.update.missingRequired.json',
	],
	'unknown' => [
		'/v1/session',
		file_get_contents(__DIR__ . '/requests/session.update.unknown.json'),
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/session.update.unknown.json',
	],
];
