<?php declare(strict_types = 1);

use Fig\Http\Message\RequestMethodInterface;
use Fig\Http\Message\StatusCodeInterface;

return [
	'readAllowed' => [
		'/v1/roles',
		RequestMethodInterface::METHOD_GET,
		'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1ODU3NDI0MDAsImV4cCI6MTU4NTc2NDAwMCwianRpIjoiOTU2YTdlMjYtZThjMC00YmRiLTg5NGEtZGQyMWIxNGU0YTk0Iiwic3ViIjoiZWZiZmJkZWYtYmZiZC02OGVmLWJmYmQtNzcwYjQwZWZiZmJkIiwidHlwZSI6ImFjY2VzcyIsInJvbGVzIjpbImF1dGhlbnRpY2F0ZWQiXX0.E2GOmUx2ohUC6r-JKj0EKN4oD-RVNWgPznP-vYYEP1c',
		'',
		StatusCodeInterface::STATUS_FORBIDDEN,
		__DIR__ . '/responses/roles.index.json',
	],
	'updateForbidden' => [
		'/v1/roles/efbfbdef-bfbd-efbf-bd0f-efbfbd5c4f61',
		RequestMethodInterface::METHOD_PATCH,
		'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1ODU3NDI0MDAsImV4cCI6MTU4NTc2NDAwMCwianRpIjoiOTU2YTdlMjYtZThjMC00YmRiLTg5NGEtZGQyMWIxNGU0YTk0Iiwic3ViIjoiZWZiZmJkZWYtYmZiZC02OGVmLWJmYmQtNzcwYjQwZWZiZmJkIiwidHlwZSI6ImFjY2VzcyIsInJvbGVzIjpbImF1dGhlbnRpY2F0ZWQiXX0.E2GOmUx2ohUC6r-JKj0EKN4oD-RVNWgPznP-vYYEP1c',
		file_get_contents(__DIR__ . '/requests/roles.update.json'),
		StatusCodeInterface::STATUS_FORBIDDEN,
		__DIR__ . '/responses/roles.update.json',
	],
];
