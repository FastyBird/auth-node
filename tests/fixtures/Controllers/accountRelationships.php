<?php declare(strict_types = 1);

use Fig\Http\Message\StatusCodeInterface;

return [
	'emails' => [
		'/v1/accounts/5e79efbf-bd0d-5b7c-46ef-bfbdefbfbd34/relationships/emails',
		'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NzU5MzIxMTgsImV4cCI6MTU3NTk1MzcxOCwianRpIjoiYWI5Y2MwNjktM2QzZS00Mzk3LWFmYjItNjY2ZDIyOTFlZWUwIn0.G507WDqVFp_mIaHE8eyg1LEywTDV1OGgWtYgFwoyCQg',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/account.readRelationship.emails.json',
	],
	'security-question' => [
		'/v1/accounts/5e79efbf-bd0d-5b7c-46ef-bfbdefbfbd34/relationships/security-question',
		'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NzU5MzIxMTgsImV4cCI6MTU3NTk1MzcxOCwianRpIjoiYWI5Y2MwNjktM2QzZS00Mzk3LWFmYjItNjY2ZDIyOTFlZWUwIn0.G507WDqVFp_mIaHE8eyg1LEywTDV1OGgWtYgFwoyCQg',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/account.readRelationship.securityQuestion.json',
	],
	'identities' => [
		'/v1/accounts/5e79efbf-bd0d-5b7c-46ef-bfbdefbfbd34/relationships/identities',
		'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NzU5MzIxMTgsImV4cCI6MTU3NTk1MzcxOCwianRpIjoiYWI5Y2MwNjktM2QzZS00Mzk3LWFmYjItNjY2ZDIyOTFlZWUwIn0.G507WDqVFp_mIaHE8eyg1LEywTDV1OGgWtYgFwoyCQg',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/account.readRelationship.identities.json',
	],
	'roles' => [
		'/v1/accounts/5e79efbf-bd0d-5b7c-46ef-bfbdefbfbd34/relationships/roles',
		'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NzU5MzIxMTgsImV4cCI6MTU3NTk1MzcxOCwianRpIjoiYWI5Y2MwNjktM2QzZS00Mzk3LWFmYjItNjY2ZDIyOTFlZWUwIn0.G507WDqVFp_mIaHE8eyg1LEywTDV1OGgWtYgFwoyCQg',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/account.readRelationship.roles.json',
	],
	'unknown' => [
		'/v1/accounts/5e79efbf-bd0d-5b7c-46ef-bfbdefbfbd34/relationships/unknown',
		'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NzU5MzIxMTgsImV4cCI6MTU3NTk1MzcxOCwianRpIjoiYWI5Y2MwNjktM2QzZS00Mzk3LWFmYjItNjY2ZDIyOTFlZWUwIn0.G507WDqVFp_mIaHE8eyg1LEywTDV1OGgWtYgFwoyCQg',
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/account.readRelationship.unknown.json',
	],
	'invalidToken' => [
		'/v1/accounts/5e79efbf-bd0d-5b7c-46ef-bfbdefbfbd34/relationships/unknown',
		'Bearer ayJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NzU5MzIxMTgsImV4cCI6MTU3NTk1MzcxOCwianRpIjoiYWI5Y2MwNjktM2QzZS00Mzk3LWFmYjItNjY2ZDIyOTFlZWUwIn0.G507WDqVFp_mIaHE8eyg1LEywTDV1OGgWtYgFwoyCQg',
		StatusCodeInterface::STATUS_UNAUTHORIZED,
		__DIR__ . '/responses/unauthorized.json',
	],
];
