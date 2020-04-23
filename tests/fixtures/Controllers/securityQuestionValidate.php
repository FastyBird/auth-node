<?php declare(strict_types = 1);

use Fig\Http\Message\StatusCodeInterface;

return [
	'validate' => [
		'/v1/account/5e79efbf-bd0d-5b7c-46ef-bfbdefbfbd34/security-question/validate',
		'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NzU5MzIxMTgsImV4cCI6MTU3NTk1MzcxOCwianRpIjoiYWI5Y2MwNjktM2QzZS00Mzk3LWFmYjItNjY2ZDIyOTFlZWUwIn0.G507WDqVFp_mIaHE8eyg1LEywTDV1OGgWtYgFwoyCQg',
		file_get_contents(__DIR__ . '/requests/securityQuestion.validate.json'),
		StatusCodeInterface::STATUS_NO_CONTENT,
		__DIR__ . '/responses/securityQuestion.validate.json',
	],
	'invalid' => [
		'/v1/account/5e79efbf-bd0d-5b7c-46ef-bfbdefbfbd34/security-question/validate',
		'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NzU5MzIxMTgsImV4cCI6MTU3NTk1MzcxOCwianRpIjoiYWI5Y2MwNjktM2QzZS00Mzk3LWFmYjItNjY2ZDIyOTFlZWUwIn0.G507WDqVFp_mIaHE8eyg1LEywTDV1OGgWtYgFwoyCQg',
		file_get_contents(__DIR__ . '/requests/securityQuestion.validate.invalid.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/securityQuestion.validate.invalid.json',
	],
	'invalidType' => [
		'/v1/account/5e79efbf-bd0d-5b7c-46ef-bfbdefbfbd34/security-question/validate',
		'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NzU5MzIxMTgsImV4cCI6MTU3NTk1MzcxOCwianRpIjoiYWI5Y2MwNjktM2QzZS00Mzk3LWFmYjItNjY2ZDIyOTFlZWUwIn0.G507WDqVFp_mIaHE8eyg1LEywTDV1OGgWtYgFwoyCQg',
		file_get_contents(__DIR__ . '/requests/securityQuestion.validate.invalidType.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/securityQuestion.invalidType.json',
	],
	'unauthorized' => [
		'/v1/account/5e79efbf-bd0d-5b7c-46ef-bfbdefbfbd34/security-question/validate',
		'Bearer ayJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NzU5MzIxMTgsImV4cCI6MTU3NTk1MzcxOCwianRpIjoiYWI5Y2MwNjktM2QzZS00Mzk3LWFmYjItNjY2ZDIyOTFlZWUwIn0.G507WDqVFp_mIaHE8eyg1LEywTDV1OGgWtYgFwoyCQg',
		file_get_contents(__DIR__ . '/requests/securityQuestion.validate.json'),
		StatusCodeInterface::STATUS_UNAUTHORIZED,
		__DIR__ . '/responses/unauthorized.json',
	],
	'missingQuestion' => [
		'/v1/account/efbfbdef-bfbd-68ef-bfbd-770b40efbfbd/security-question/validate',
		'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NjU5NDI5ODAsImV4cCI6MTU2NTk2NDU4MCwianRpIjoiNjg4NTE4ZTktMGE2Ni00ZGVmLTg1ODItMWY5YzNjOGI1NGE4In0.ry-j4RnM9j1HrV6ktI_ATXVibV7FSe3yq52vN7e83jk',
		file_get_contents(__DIR__ . '/requests/securityQuestion.validate.missingQuestion.json'),
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/securityQuestion.validate.missingQuestion.json',
	],
];
