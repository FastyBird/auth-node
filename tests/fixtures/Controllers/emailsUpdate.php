<?php declare(strict_types = 1);

use Fig\Http\Message\StatusCodeInterface;

return [
	'update' => [
		'/v1/accounts/5e79efbf-bd0d-5b7c-46ef-bfbdefbfbd34/emails/0b46d3d6-c980-494a-8b40-f19e6095e610',
		'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NzU5MzIxMTgsImV4cCI6MTU3NTk1MzcxOCwianRpIjoiYWI5Y2MwNjktM2QzZS00Mzk3LWFmYjItNjY2ZDIyOTFlZWUwIn0.G507WDqVFp_mIaHE8eyg1LEywTDV1OGgWtYgFwoyCQg',
		file_get_contents(__DIR__ . '/requests/emails.update.json'),
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/emails.update.json',
	],
	'invalidType' => [
		'/v1/accounts/5e79efbf-bd0d-5b7c-46ef-bfbdefbfbd34/emails/0b46d3d6-c980-494a-8b40-f19e6095e610',
		'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NzU5MzIxMTgsImV4cCI6MTU3NTk1MzcxOCwianRpIjoiYWI5Y2MwNjktM2QzZS00Mzk3LWFmYjItNjY2ZDIyOTFlZWUwIn0.G507WDqVFp_mIaHE8eyg1LEywTDV1OGgWtYgFwoyCQg',
		file_get_contents(__DIR__ . '/requests/emails.update.invalidType.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/emails.invalidType.json',
	],
	'unauthorized' => [
		'/v1/accounts/5e79efbf-bd0d-5b7c-46ef-bfbdefbfbd34/emails/0b46d3d6-c980-494a-8b40-f19e6095e610',
		'Bearer ayJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NzU5MzIxMTgsImV4cCI6MTU3NTk1MzcxOCwianRpIjoiYWI5Y2MwNjktM2QzZS00Mzk3LWFmYjItNjY2ZDIyOTFlZWUwIn0.G507WDqVFp_mIaHE8eyg1LEywTDV1OGgWtYgFwoyCQg',
		file_get_contents(__DIR__ . '/requests/emails.update.json'),
		StatusCodeInterface::STATUS_UNAUTHORIZED,
		__DIR__ . '/responses/unauthorized.json',
	],
];
