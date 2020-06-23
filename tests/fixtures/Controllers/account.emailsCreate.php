<?php declare(strict_types = 1);

use Fig\Http\Message\StatusCodeInterface;

return [
	'create'          => [
		'/v1/me/emails',
		'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1ODU3NDI0MDAsImV4cCI6MTU4NTc2NDAwMCwianRpIjoiNGY2NzEwYTEtMzhhYS00MjY0LTljMGMtYjQ1Mjg1MTgxMjcwIiwic3ViIjoiNWU3OWVmYmYtYmQwZC01YjdjLTQ2ZWYtYmZiZGVmYmZiZDM0IiwidHlwZSI6ImFjY2VzcyIsInJvbGVzIjpbImFkbWluaXN0cmF0b3IiXX0.Ijw2E1hhDvqzyDpNExUm0vAE0IK08UeZJUcDO5QMTOI',
		file_get_contents(__DIR__ . '/requests/account.emails.create.json'),
		StatusCodeInterface::STATUS_CREATED,
		__DIR__ . '/responses/account.emails.create.json',
	],
	'missingRequired' => [
		'/v1/me/emails',
		'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1ODU3NDI0MDAsImV4cCI6MTU4NTc2NDAwMCwianRpIjoiNGY2NzEwYTEtMzhhYS00MjY0LTljMGMtYjQ1Mjg1MTgxMjcwIiwic3ViIjoiNWU3OWVmYmYtYmQwZC01YjdjLTQ2ZWYtYmZiZGVmYmZiZDM0IiwidHlwZSI6ImFjY2VzcyIsInJvbGVzIjpbImFkbWluaXN0cmF0b3IiXX0.Ijw2E1hhDvqzyDpNExUm0vAE0IK08UeZJUcDO5QMTOI',
		file_get_contents(__DIR__ . '/requests/account.emails.create.missing.required.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/account.emails.create.missingRequired.json',
	],
	'invalidType'     => [
		'/v1/me/emails',
		'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1ODU3NDI0MDAsImV4cCI6MTU4NTc2NDAwMCwianRpIjoiNGY2NzEwYTEtMzhhYS00MjY0LTljMGMtYjQ1Mjg1MTgxMjcwIiwic3ViIjoiNWU3OWVmYmYtYmQwZC01YjdjLTQ2ZWYtYmZiZGVmYmZiZDM0IiwidHlwZSI6ImFjY2VzcyIsInJvbGVzIjpbImFkbWluaXN0cmF0b3IiXX0.Ijw2E1hhDvqzyDpNExUm0vAE0IK08UeZJUcDO5QMTOI',
		file_get_contents(__DIR__ . '/requests/account.emails.create.invalidType.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/account.emails.invalidType.json',
	],
	'unauthorized'    => [
		'/v1/me/emails',
		'Bearer ayJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NzU5MzIxMTgsImV4cCI6MTU3NTk1MzcxOCwianRpIjoiYWI5Y2MwNjktM2QzZS00Mzk3LWFmYjItNjY2ZDIyOTFlZWUwIn0.G507WDqVFp_mIaHE8eyg1LEywTDV1OGgWtYgFwoyCQg',
		file_get_contents(__DIR__ . '/requests/account.emails.create.json'),
		StatusCodeInterface::STATUS_UNAUTHORIZED,
		__DIR__ . '/responses/unauthorized.json',
	],
	'invalidEmail'    => [
		'/v1/me/emails',
		'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1ODU3NDI0MDAsImV4cCI6MTU4NTc2NDAwMCwianRpIjoiNGY2NzEwYTEtMzhhYS00MjY0LTljMGMtYjQ1Mjg1MTgxMjcwIiwic3ViIjoiNWU3OWVmYmYtYmQwZC01YjdjLTQ2ZWYtYmZiZGVmYmZiZDM0IiwidHlwZSI6ImFjY2VzcyIsInJvbGVzIjpbImFkbWluaXN0cmF0b3IiXX0.Ijw2E1hhDvqzyDpNExUm0vAE0IK08UeZJUcDO5QMTOI',
		file_get_contents(__DIR__ . '/requests/account.emails.create.invalidEmail.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/account.emails.invalidEmail.json',
	],
	'usedEmail'       => [
		'/v1/me/emails',
		'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1ODU3NDI0MDAsImV4cCI6MTU4NTc2NDAwMCwianRpIjoiNGY2NzEwYTEtMzhhYS00MjY0LTljMGMtYjQ1Mjg1MTgxMjcwIiwic3ViIjoiNWU3OWVmYmYtYmQwZC01YjdjLTQ2ZWYtYmZiZGVmYmZiZDM0IiwidHlwZSI6ImFjY2VzcyIsInJvbGVzIjpbImFkbWluaXN0cmF0b3IiXX0.Ijw2E1hhDvqzyDpNExUm0vAE0IK08UeZJUcDO5QMTOI',
		file_get_contents(__DIR__ . '/requests/account.emails.create.usedEmail.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/account.emails.create.usedEmail.json',
	],
];
