<?php declare(strict_types = 1);

use Fig\Http\Message\StatusCodeInterface;

return [
	'readForbidden' => [
		'/v1/roles',
		'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NjU5NDI5ODAsImV4cCI6MTU2NTk2NDU4MCwianRpIjoiNjg4NTE4ZTktMGE2Ni00ZGVmLTg1ODItMWY5YzNjOGI1NGE4In0.ry-j4RnM9j1HrV6ktI_ATXVibV7FSe3yq52vN7e83jk',
		StatusCodeInterface::STATUS_FORBIDDEN,
		__DIR__ . '/responses/roles.index.json',
	],
];