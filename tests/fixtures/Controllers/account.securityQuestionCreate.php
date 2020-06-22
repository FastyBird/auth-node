<?php declare(strict_types = 1);

use Fig\Http\Message\StatusCodeInterface;

return [
	'create'           => [
		'/v1/me/security-question',
		'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NjU5NDI5ODAsImV4cCI6MTU2NTk2NDU4MCwianRpIjoiNjg4NTE4ZTktMGE2Ni00ZGVmLTg1ODItMWY5YzNjOGI1NGE4In0.ry-j4RnM9j1HrV6ktI_ATXVibV7FSe3yq52vN7e83jk',
		file_get_contents(__DIR__ . '/requests/account.securityQuestion.create.json'),
		StatusCodeInterface::STATUS_CREATED,
		__DIR__ . '/responses/account.securityQuestion.create.json',
	],
	'missingRequired'  => [
		'/v1/me/security-question',
		'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NjU5NDI5ODAsImV4cCI6MTU2NTk2NDU4MCwianRpIjoiNjg4NTE4ZTktMGE2Ni00ZGVmLTg1ODItMWY5YzNjOGI1NGE4In0.ry-j4RnM9j1HrV6ktI_ATXVibV7FSe3yq52vN7e83jk',
		file_get_contents(__DIR__ . '/requests/account.securityQuestion.create.missing.required.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/account.securityQuestion.create.missingRequired.json',
	],
	'invalidType'      => [
		'/v1/me/security-question',
		'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NjU5NDI5ODAsImV4cCI6MTU2NTk2NDU4MCwianRpIjoiNjg4NTE4ZTktMGE2Ni00ZGVmLTg1ODItMWY5YzNjOGI1NGE4In0.ry-j4RnM9j1HrV6ktI_ATXVibV7FSe3yq52vN7e83jk',
		file_get_contents(__DIR__ . '/requests/account.securityQuestion.create.invalidType.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/account.securityQuestion.invalidType.json',
	],
	'unauthorized'     => [
		'/v1/me/security-question',
		'Bearer ayJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NzU5MzIxMTgsImV4cCI6MTU3NTk1MzcxOCwianRpIjoiYWI5Y2MwNjktM2QzZS00Mzk3LWFmYjItNjY2ZDIyOTFlZWUwIn0.G507WDqVFp_mIaHE8eyg1LEywTDV1OGgWtYgFwoyCQg',
		file_get_contents(__DIR__ . '/requests/account.securityQuestion.create.json'),
		StatusCodeInterface::STATUS_UNAUTHORIZED,
		__DIR__ . '/responses/unauthorized.json',
	],
	'existingQuestion' => [
		'/v1/me/security-question',
		'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NzU5MzIxMTgsImV4cCI6MTU3NTk1MzcxOCwianRpIjoiYWI5Y2MwNjktM2QzZS00Mzk3LWFmYjItNjY2ZDIyOTFlZWUwIn0.G507WDqVFp_mIaHE8eyg1LEywTDV1OGgWtYgFwoyCQg',
		file_get_contents(__DIR__ . '/requests/account.securityQuestion.create.existingQuestion.json'),
		StatusCodeInterface::STATUS_BAD_REQUEST,
		__DIR__ . '/responses/account.securityQuestion.create.existingQuestion.json',
	],
];
