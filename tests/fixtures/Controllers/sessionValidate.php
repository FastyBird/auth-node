<?php declare(strict_types = 1);

use Fig\Http\Message\StatusCodeInterface;

return [
	'validate' => [
		'/v1/session/validate',
		file_get_contents(__DIR__ . '/requests/session.validate.json'),
		StatusCodeInterface::STATUS_NO_CONTENT,
		__DIR__ . '/responses/session.validate.json',
	],
	'deleted' => [
		'/v1/session/validate',
		file_get_contents(__DIR__ . '/requests/session.validate.deleted.json'),
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/session.validate.deleted.json',
	],
	'unknown' => [
		'/v1/session/validate',
		file_get_contents(__DIR__ . '/requests/session.validate.unknown.json'),
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/session.validate.unknown.json',
	],
];
