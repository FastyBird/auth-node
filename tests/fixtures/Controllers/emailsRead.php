<?php declare(strict_types = 1);

use Fig\Http\Message\StatusCodeInterface;

return [
	'readAll'                  => [
		'/v1/account/5e79efbf-bd0d-5b7c-46ef-bfbdefbfbd34/emails',
		'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NzU5MzIxMTgsImV4cCI6MTU3NTk1MzcxOCwianRpIjoiYWI5Y2MwNjktM2QzZS00Mzk3LWFmYjItNjY2ZDIyOTFlZWUwIn0.G507WDqVFp_mIaHE8eyg1LEywTDV1OGgWtYgFwoyCQg',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/emails.index.json',
	],
	'readAllPaging'            => [
		'/v1/account/5e79efbf-bd0d-5b7c-46ef-bfbdefbfbd34/emails?page[offset]=1&page[limit]=1',
		'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NzU5MzIxMTgsImV4cCI6MTU3NTk1MzcxOCwianRpIjoiYWI5Y2MwNjktM2QzZS00Mzk3LWFmYjItNjY2ZDIyOTFlZWUwIn0.G507WDqVFp_mIaHE8eyg1LEywTDV1OGgWtYgFwoyCQg',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/emails.index.paging.json',
	],
	'readOne'                  => [
		'/v1/account/5e79efbf-bd0d-5b7c-46ef-bfbdefbfbd34/emails/0b46d3d6-c980-494a-8b40-f19e6095e610',
		'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NzU5MzIxMTgsImV4cCI6MTU3NTk1MzcxOCwianRpIjoiYWI5Y2MwNjktM2QzZS00Mzk3LWFmYjItNjY2ZDIyOTFlZWUwIn0.G507WDqVFp_mIaHE8eyg1LEywTDV1OGgWtYgFwoyCQg',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/emails.read.json',
	],
	'readOneUnknown'           => [
		'/v1/account/5e79efbf-bd0d-5b7c-46ef-bfbdefbfbd34/emails/17c59dfa-2edd-438e-8c49-faa4e38e5ae5',
		'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NzU5MzIxMTgsImV4cCI6MTU3NTk1MzcxOCwianRpIjoiYWI5Y2MwNjktM2QzZS00Mzk3LWFmYjItNjY2ZDIyOTFlZWUwIn0.G507WDqVFp_mIaHE8eyg1LEywTDV1OGgWtYgFwoyCQg',
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/emails.notFound.json',
	],
	'readRelationshipsAccount' => [
		'/v1/account/5e79efbf-bd0d-5b7c-46ef-bfbdefbfbd34/emails/0b46d3d6-c980-494a-8b40-f19e6095e610/relationships/account',
		'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NzU5MzIxMTgsImV4cCI6MTU3NTk1MzcxOCwianRpIjoiYWI5Y2MwNjktM2QzZS00Mzk3LWFmYjItNjY2ZDIyOTFlZWUwIn0.G507WDqVFp_mIaHE8eyg1LEywTDV1OGgWtYgFwoyCQg',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/emails.readRelationships.account.json',
	],
	'readRelationshipsUnknown' => [
		'/v1/account/5e79efbf-bd0d-5b7c-46ef-bfbdefbfbd34/emails/0b46d3d6-c980-494a-8b40-f19e6095e610/relationships/unknown',
		'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NzU5MzIxMTgsImV4cCI6MTU3NTk1MzcxOCwianRpIjoiYWI5Y2MwNjktM2QzZS00Mzk3LWFmYjItNjY2ZDIyOTFlZWUwIn0.G507WDqVFp_mIaHE8eyg1LEywTDV1OGgWtYgFwoyCQg',
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/relation.unknown.json',
	],
];
