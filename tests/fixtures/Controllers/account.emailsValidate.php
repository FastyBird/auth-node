<?php declare(strict_types = 1);

use Fig\Http\Message\StatusCodeInterface;

return [
	'validate'      => [
		'/v1/validate-email',
		null,
		file_get_contents(__DIR__ . '/requests/account.emails.validate.json'),
		StatusCodeInterface::STATUS_NO_CONTENT,
		__DIR__ . '/responses/account.emails.validate.json',
	],
	'used'          => [
		'/v1/validate-email',
		null,
		file_get_contents(__DIR__ . '/requests/account.emails.validate.used.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/account.emails.validate.used.json',
	],
	'invalidType'   => [
		'/v1/validate-email',
		null,
		file_get_contents(__DIR__ . '/requests/account.emails.validate.invalidType.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/account.emails.invalidType.json',
	],
	'invalidEmail'  => [
		'/v1/validate-email',
		null,
		file_get_contents(__DIR__ . '/requests/account.emails.validate.invalidEmail.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/account.emails.invalidEmail.json',
	],
	'authenticated' => [
		'/v1/validate-email',
		'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJqdGkiOiI5YWY1NjI0Mi01ZDg3LTQzNjQtYmIxZS1kOWZjODI4NmIzZmYiLCJpc3MiOiJjb20uZmFzdHliaXJkLmF1dGgtbm9kZSIsImlhdCI6MTU4NTc0MjQwMCwiZXhwIjoxNTg1NzQ5NjAwLCJ1c2VyIjoiNWU3OWVmYmYtYmQwZC01YjdjLTQ2ZWYtYmZiZGVmYmZiZDM0Iiwicm9sZXMiOlsiYWRtaW5pc3RyYXRvciJdfQ.Lb-zUa9DL7swdVSEuPTqaR9FvLgKwuEtrhxiJFWjhU8',
		file_get_contents(__DIR__ . '/requests/account.emails.validate.authenticated.json'),
		StatusCodeInterface::STATUS_NO_CONTENT,
		__DIR__ . '/responses/account.emails.validate.authenticated.json',
	],
];
