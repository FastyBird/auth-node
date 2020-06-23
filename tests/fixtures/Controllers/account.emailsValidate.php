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
		'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1ODU3NDI0MDAsImV4cCI6MTU4NTc2NDAwMCwianRpIjoiNGY2NzEwYTEtMzhhYS00MjY0LTljMGMtYjQ1Mjg1MTgxMjcwIiwic3ViIjoiNWU3OWVmYmYtYmQwZC01YjdjLTQ2ZWYtYmZiZGVmYmZiZDM0IiwidHlwZSI6ImFjY2VzcyIsInJvbGVzIjpbImFkbWluaXN0cmF0b3IiXX0.Ijw2E1hhDvqzyDpNExUm0vAE0IK08UeZJUcDO5QMTOI',
		file_get_contents(__DIR__ . '/requests/account.emails.validate.authenticated.json'),
		StatusCodeInterface::STATUS_NO_CONTENT,
		__DIR__ . '/responses/account.emails.validate.authenticated.json',
	],
];
