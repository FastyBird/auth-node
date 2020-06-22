<?php declare(strict_types = 1);

use Fig\Http\Message\StatusCodeInterface;

return [
	'validate' => [
		'/v1/validate-email',
		null,
		file_get_contents(__DIR__ . '/requests/account.emails.validate.json'),
		StatusCodeInterface::STATUS_NO_CONTENT,
		__DIR__ . '/responses/account.emails.validate.json',
	],
	'used' => [
		'/v1/validate-email',
		null,
		file_get_contents(__DIR__ . '/requests/account.emails.validate.used.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/account.emails.validate.used.json',
	],
	'invalidType' => [
		'/v1/validate-email',
		null,
		file_get_contents(__DIR__ . '/requests/account.emails.validate.invalidType.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/account.emails.invalidType.json',
	],
	'invalidEmail' => [
		'/v1/validate-email',
		null,
		file_get_contents(__DIR__ . '/requests/account.emails.validate.invalidEmail.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/account.emails.invalidEmail.json',
	],
	'authenticated' => [
		'/v1/validate-email',
		'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NzU5MzIxMTgsImV4cCI6MTU3NTk1MzcxOCwianRpIjoiYWI5Y2MwNjktM2QzZS00Mzk3LWFmYjItNjY2ZDIyOTFlZWUwIn0.G507WDqVFp_mIaHE8eyg1LEywTDV1OGgWtYgFwoyCQg',
		file_get_contents(__DIR__ . '/requests/account.emails.validate.authenticated.json'),
		StatusCodeInterface::STATUS_NO_CONTENT,
		__DIR__ . '/responses/account.emails.validate.authenticated.json',
	],
];
