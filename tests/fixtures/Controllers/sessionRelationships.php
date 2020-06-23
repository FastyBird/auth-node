<?php declare(strict_types = 1);

use Fig\Http\Message\StatusCodeInterface;

return [
	'read' => [
		'/v1/session/relationships/account',
		'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1ODU3NDI0MDAsImV4cCI6MTU4NTc2NDAwMCwianRpIjoiNGY2NzEwYTEtMzhhYS00MjY0LTljMGMtYjQ1Mjg1MTgxMjcwIiwic3ViIjoiNWU3OWVmYmYtYmQwZC01YjdjLTQ2ZWYtYmZiZGVmYmZiZDM0IiwidHlwZSI6ImFjY2VzcyIsInJvbGVzIjpbImFkbWluaXN0cmF0b3IiXX0.Ijw2E1hhDvqzyDpNExUm0vAE0IK08UeZJUcDO5QMTOI',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/session.readRelationship.account.json',
	],
	'unknown' => [
		'/v1/session/relationships/unknown',
		'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1ODU3NDI0MDAsImV4cCI6MTU4NTc2NDAwMCwianRpIjoiNGY2NzEwYTEtMzhhYS00MjY0LTljMGMtYjQ1Mjg1MTgxMjcwIiwic3ViIjoiNWU3OWVmYmYtYmQwZC01YjdjLTQ2ZWYtYmZiZGVmYmZiZDM0IiwidHlwZSI6ImFjY2VzcyIsInJvbGVzIjpbImFkbWluaXN0cmF0b3IiXX0.Ijw2E1hhDvqzyDpNExUm0vAE0IK08UeZJUcDO5QMTOI',
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/session.readRelationship.unknown.json',
	],
	'invalidToken' => [
		'/v1/session/relationships/unknown',
		'Bearer ayJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NzU5MzIxMTgsImV4cCI6MTU3NTk1MzcxOCwianRpIjoiYWI5Y2MwNjktM2QzZS00Mzk3LWFmYjItNjY2ZDIyOTFlZWUwIn0.G507WDqVFp_mIaHE8eyg1LEywTDV1OGgWtYgFwoyCQg',
		StatusCodeInterface::STATUS_UNAUTHORIZED,
		__DIR__ . '/responses/unauthorized.json',
	],
];
