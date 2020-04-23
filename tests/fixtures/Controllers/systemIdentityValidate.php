<?php declare(strict_types = 1);

use Fig\Http\Message\StatusCodeInterface;

return [
	'validate' => [
		'/v1/account/5e79efbf-bd0d-5b7c-46ef-bfbdefbfbd34/identities/77331268-efbf-bd34-49ef-bfbdefbfbd04/validate',
		'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NzU5MzIxMTgsImV4cCI6MTU3NTk1MzcxOCwianRpIjoiYWI5Y2MwNjktM2QzZS00Mzk3LWFmYjItNjY2ZDIyOTFlZWUwIn0.G507WDqVFp_mIaHE8eyg1LEywTDV1OGgWtYgFwoyCQg',
		file_get_contents(__DIR__ . '/requests/systemIdentity.validate.json'),
		StatusCodeInterface::STATUS_NO_CONTENT,
		__DIR__ . '/responses/systemIdentity.validate.json',
	],
	'invalid' => [
		'/v1/account/5e79efbf-bd0d-5b7c-46ef-bfbdefbfbd34/identities/77331268-efbf-bd34-49ef-bfbdefbfbd04/validate',
		'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NzU5MzIxMTgsImV4cCI6MTU3NTk1MzcxOCwianRpIjoiYWI5Y2MwNjktM2QzZS00Mzk3LWFmYjItNjY2ZDIyOTFlZWUwIn0.G507WDqVFp_mIaHE8eyg1LEywTDV1OGgWtYgFwoyCQg',
		file_get_contents(__DIR__ . '/requests/systemIdentity.validate.invalid.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/systemIdentity.validate.invalid.json',
	],
	'invalidType' => [
		'/v1/account/5e79efbf-bd0d-5b7c-46ef-bfbdefbfbd34/identities/77331268-efbf-bd34-49ef-bfbdefbfbd04/validate',
		'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NzU5MzIxMTgsImV4cCI6MTU3NTk1MzcxOCwianRpIjoiYWI5Y2MwNjktM2QzZS00Mzk3LWFmYjItNjY2ZDIyOTFlZWUwIn0.G507WDqVFp_mIaHE8eyg1LEywTDV1OGgWtYgFwoyCQg',
		file_get_contents(__DIR__ . '/requests/systemIdentity.validate.invalidType.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/systemIdentity.invalidType.json',
	],
	'unauthorized' => [
		'/v1/account/5e79efbf-bd0d-5b7c-46ef-bfbdefbfbd34/identities/77331268-efbf-bd34-49ef-bfbdefbfbd04/validate',
		'Bearer ayJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NzU5MzIxMTgsImV4cCI6MTU3NTk1MzcxOCwianRpIjoiYWI5Y2MwNjktM2QzZS00Mzk3LWFmYjItNjY2ZDIyOTFlZWUwIn0.G507WDqVFp_mIaHE8eyg1LEywTDV1OGgWtYgFwoyCQg',
		file_get_contents(__DIR__ . '/requests/systemIdentity.validate.json'),
		StatusCodeInterface::STATUS_UNAUTHORIZED,
		__DIR__ . '/responses/unauthorized.json',
	],
];
