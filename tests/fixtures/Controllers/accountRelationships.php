<?php declare(strict_types = 1);

use Fig\Http\Message\StatusCodeInterface;

return [
	'emails'       => [
		'/v1/me/relationships/emails',
		'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1ODU3NDI0MDAsImV4cCI6MTU4NTc2NDAwMCwianRpIjoiNGY2NzEwYTEtMzhhYS00MjY0LTljMGMtYjQ1Mjg1MTgxMjcwIiwic3ViIjoiNWU3OWVmYmYtYmQwZC01YjdjLTQ2ZWYtYmZiZGVmYmZiZDM0IiwidHlwZSI6ImFjY2VzcyIsInJvbGVzIjpbImFkbWluaXN0cmF0b3IiXX0.Ijw2E1hhDvqzyDpNExUm0vAE0IK08UeZJUcDO5QMTOI',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/account.readRelationship.emails.json',
	],
	'identities'   => [
		'/v1/me/relationships/identities',
		'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1ODU3NDI0MDAsImV4cCI6MTU4NTc2NDAwMCwianRpIjoiNGY2NzEwYTEtMzhhYS00MjY0LTljMGMtYjQ1Mjg1MTgxMjcwIiwic3ViIjoiNWU3OWVmYmYtYmQwZC01YjdjLTQ2ZWYtYmZiZGVmYmZiZDM0IiwidHlwZSI6ImFjY2VzcyIsInJvbGVzIjpbImFkbWluaXN0cmF0b3IiXX0.Ijw2E1hhDvqzyDpNExUm0vAE0IK08UeZJUcDO5QMTOI',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/account.readRelationship.identities.json',
	],
	'roles'        => [
		'/v1/me/relationships/roles',
		'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1ODU3NDI0MDAsImV4cCI6MTU4NTc2NDAwMCwianRpIjoiNGY2NzEwYTEtMzhhYS00MjY0LTljMGMtYjQ1Mjg1MTgxMjcwIiwic3ViIjoiNWU3OWVmYmYtYmQwZC01YjdjLTQ2ZWYtYmZiZGVmYmZiZDM0IiwidHlwZSI6ImFjY2VzcyIsInJvbGVzIjpbImFkbWluaXN0cmF0b3IiXX0.Ijw2E1hhDvqzyDpNExUm0vAE0IK08UeZJUcDO5QMTOI',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/account.readRelationship.roles.json',
	],
	'unknown'      => [
		'/v1/me/relationships/unknown',
		'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1ODU3NDI0MDAsImV4cCI6MTU4NTc2NDAwMCwianRpIjoiNGY2NzEwYTEtMzhhYS00MjY0LTljMGMtYjQ1Mjg1MTgxMjcwIiwic3ViIjoiNWU3OWVmYmYtYmQwZC01YjdjLTQ2ZWYtYmZiZGVmYmZiZDM0IiwidHlwZSI6ImFjY2VzcyIsInJvbGVzIjpbImFkbWluaXN0cmF0b3IiXX0.Ijw2E1hhDvqzyDpNExUm0vAE0IK08UeZJUcDO5QMTOI',
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/account.readRelationship.unknown.json',
	],
	'invalidToken' => [
		'/v1/me/relationships/unknown',
		'Bearer ayJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NzU5MzIxMTgsImV4cCI6MTU3NTk1MzcxOCwianRpIjoiYWI5Y2MwNjktM2QzZS00Mzk3LWFmYjItNjY2ZDIyOTFlZWUwIn0.G507WDqVFp_mIaHE8eyg1LEywTDV1OGgWtYgFwoyCQg',
		StatusCodeInterface::STATUS_UNAUTHORIZED,
		__DIR__ . '/responses/unauthorized.json',
	],
];
