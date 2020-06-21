INSERT IGNORE INTO `fb_accounts` (`account_id`, `account_type`, `account_status`, `account_last_visit`, `params`, `created_at`, `updated_at`) VALUES
	(_binary 0x5E79EFBFBD0D5B7C46EFBFBDEFBFBD34, 'user', 'activated', '2019-11-07 22:30:56', '{"datetime": {"zone": "Europe/Prague", "format": {"date": "DD.MM.YYYY", "time": "HH:mm"}, "week_start": 1}}', '2017-01-03 11:30:00', '2017-01-03 11:30:00'),
	(_binary 0xEFBFBDEFBFBD68EFBFBD770B40EFBFBD, 'user', 'activated', '2019-05-29 07:38:24', '{"datetime": {"zone": "Europe/Prague", "format": {"date": "DD.MM.YYYY", "time": "HH:mm"}, "week_start": 1}}', '2017-01-04 12:30:00', '2017-01-04 12:30:00'),
	(_binary 0xFAE8D7817E2C43189C8543BA637D14C5, 'user', 'approvalWaiting', '2019-05-29 07:38:24', '{"datetime": {"zone": "Europe/Prague", "format": {"date": "DD.MM.YYYY", "time": "HH:mm"}, "week_start": 1}}', '2017-01-04 12:30:00', '2017-01-04 12:30:00'),
	(_binary 0xFD23CCB48D874EB394DA638AB4E10AE3, 'user', 'notActivated', '2019-05-29 07:38:24', '{"datetime": {"zone": "Europe/Prague", "format": {"date": "DD.MM.YYYY", "time": "HH:mm"}, "week_start": 1}}', '2017-01-04 12:30:00', '2017-01-04 12:30:00'),
	(_binary 0xFDBE2CE23B1841F1AAABC3C56D286EB4, 'user', 'deleted', '2019-05-29 07:38:24', '{"datetime": {"zone": "Europe/Prague", "format": {"date": "DD.MM.YYYY", "time": "HH:mm"}, "week_start": 1}}', '2017-01-04 12:30:00', '2017-01-04 12:30:00'),
	(_binary 0xFE1152868CFD41BFACEB6CA95BAF6FE9, 'user', 'blocked', '2019-05-29 07:38:24', '{"datetime": {"zone": "Europe/Prague", "format": {"date": "DD.MM.YYYY", "time": "HH:mm"}, "week_start": 1}}', '2017-01-04 12:30:00', '2017-01-04 12:30:00');

INSERT IGNORE INTO `fb_accounts_users` (`account_id`, `parent_id`, `account_request_hash`) VALUES
	(_binary 0x5E79EFBFBD0D5B7C46EFBFBDEFBFBD34, NULL, 'NGZqMmVxdnhubjJpIyMxNTc0NDUwNDAz'),
	(_binary 0xEFBFBDEFBFBD68EFBFBD770B40EFBFBD, NULL, 'YjRqZXFoZGw1Z3ZzIyMxNTc0MjA3NDQ1'),
	(_binary 0xFAE8D7817E2C43189C8543BA637D14C5, NULL, 'YjRqZXFoZGw1Z3ZzIyMxNTc0MjA3NDQ1'),
	(_binary 0xFD23CCB48D874EB394DA638AB4E10AE3, NULL, 'YjRqZXFoZGw1Z3ZzIyMxNTc0MjA3NDQ1'),
	(_binary 0xFDBE2CE23B1841F1AAABC3C56D286EB4, NULL, 'YjRqZXFoZGw1Z3ZzIyMxNTc0MjA3NDQ1'),
	(_binary 0xFE1152868CFD41BFACEB6CA95BAF6FE9, NULL, 'YjRqZXFoZGw1Z3ZzIyMxNTc0MjA3NDQ1');

INSERT IGNORE INTO `fb_accounts_details` (`detail_id`, `account_id`, `detail_first_name`, `detail_last_name`, `detail_middle_name`, `created_at`, `updated_at`) VALUES
	(_binary 0xEFBFBDCFAA74EFBFBD4CEFBFBDEFBFBD, _binary 0x5E79EFBFBD0D5B7C46EFBFBDEFBFBD34, 'John', 'Doe', NULL, '2017-01-03 11:30:00', '2017-01-03 11:30:00'),
	(_binary 0xEFBFBDEFBFBDEFBFBD4011EFBFBD4254, _binary 0xEFBFBDEFBFBD68EFBFBD770B40EFBFBD, 'Jane', 'Doe', NULL, '2017-01-04 12:30:00', '2017-01-04 12:30:00'),
	(_binary 0xF3CCE15AF9564C7EA4B3AC31A0017AC9, _binary 0xFDBE2CE23B1841F1AAABC3C56D286EB4, 'Peter', 'Parker', 'Deleted', '2017-01-04 12:30:00', '2017-01-04 12:30:00'),
	(_binary 0xF48FC1C96CAD483E8F847EB001378DC9, _binary 0xFE1152868CFD41BFACEB6CA95BAF6FE9, 'Peter', 'Pan', 'Blocked', '2017-01-04 12:30:00', '2017-01-04 12:30:00'),
	(_binary 0xF599AF7982F24C7B8F9B1594F1EE5E8A, _binary 0xFD23CCB48D874EB394DA638AB4E10AE3, 'Bruce', 'Wane', 'Unactivated', '2017-01-04 12:30:00', '2017-01-04 12:30:00'),
	(_binary 0xF62132F799BE4ED4AD23190A883AF788, _binary 0xFAE8D7817E2C43189C8543BA637D14C5, 'Clark', 'Kent', 'Waiting', '2017-01-04 12:30:00', '2017-01-04 12:30:00');

INSERT IGNORE INTO `fb_emails` (`email_id`, `account_id`, `email_address`, `email_default`, `email_verified`, `email_verification_hash`, `email_verification_created`, `email_verification_completed`, `email_visibility`, `created_at`, `updated_at`) VALUES
	(_binary 0x0B46D3D6C980494A8B40F19E6095E610, _binary 0x5E79EFBFBD0D5B7C46EFBFBDEFBFBD34, 'john.doe@fastybird.com', 1, 1, NULL, NULL, '2019-09-22 20:29:16', 'public', '2019-09-22 20:29:16', '2019-09-22 20:29:16'),
	(_binary 0x32EBE3C30238482EAB796B1D9EE2147C, _binary 0x5E79EFBFBD0D5B7C46EFBFBDEFBFBD34, 'john.doe@fastybird.ovh', 0, 1, NULL, NULL, '2017-09-07 18:24:35', 'public', '2017-09-07 18:24:35', '2018-02-07 17:38:20'),
	(_binary 0x73EFBFBDEFBFBD3644EFBFBDEFBFBD7A, _binary 0xEFBFBDEFBFBD68EFBFBD770B40EFBFBD, 'jane.doe@fastybird.com', 1, 1, NULL, NULL, '2017-01-25 22:31:19', 'public', '2017-01-25 22:31:19', '2019-04-21 19:49:32'),
	(_binary 0x7D60DD96EFBFBD534E2BEFBFBDEFBFBD, _binary 0xFE1152868CFD41BFACEB6CA95BAF6FE9, 'peter.pan@fastybird.com', 1, 1, NULL, NULL, '2017-05-03 11:27:34', 'public', '2017-05-03 11:27:34', '2018-02-07 17:38:20'),
	(_binary 0xEB54A59E5C7A41E5BFAC4FB6EC4D6AD9, _binary 0xFDBE2CE23B1841F1AAABC3C56D286EB4, 'peter.parker@fastybird.com', 1, 1, NULL, NULL, '2017-05-03 11:27:34', 'public', '2017-05-03 11:27:34', '2018-02-07 17:38:20'),
	(_binary 0xBAEFEC20192741CEA7E0AE74BD641B9C, _binary 0xFD23CCB48D874EB394DA638AB4E10AE3, 'bruce.wane@fastybird.com', 1, 1, NULL, NULL, '2017-09-06 22:56:44', 'public', '2017-09-06 22:56:44', '2019-04-21 19:49:32'),
	(_binary 0xED987404F14C40B4915015B6590DEB8C, _binary 0xFAE8D7817E2C43189C8543BA637D14C5, 'clark.kent@fastybird.com', 1, 1, NULL, NULL, '2019-09-22 21:09:56', 'public', '2019-09-22 21:09:56', '2019-09-22 21:09:56');

INSERT IGNORE INTO `fb_identities` (`identity_id`, `account_id`, `identity_uid`, `identity_status`, `created_at`, `updated_at`, `identity_type`, `identity_token`, `params`) VALUES
	(_binary 0x77331268EFBFBD3449EFBFBDEFBFBD04, _binary 0x5E79EFBFBD0D5B7C46EFBFBDEFBFBD34, 'john.doe@fastybird.com', 'active', '2017-05-03 11:27:34', '2019-05-29 07:14:00', 'user', '11b9174dbbc8f3f5ce3b3d270a3c73eedbe33832489aaa70fe0f370566bcd2f12a1a15041b318b57fd6ce6bb82471d9e2d7bc753064491bf418f9f9c3de21fcf', '{"salt": "89bwo"}'),
	(_binary 0xF2AB51A80B6F4ADBB51E1C648D41F24E, _binary 0xFDBE2CE23B1841F1AAABC3C56D286EB4, 'peter.parker@fastybird.com', 'active', '2017-01-25 12:42:26', '2019-12-10 20:19:01', 'user', '11b9174dbbc8f3f5ce3b3d270a3c73eedbe33832489aaa70fe0f370566bcd2f12a1a15041b318b57fd6ce6bb82471d9e2d7bc753064491bf418f9f9c3de21fcf', '{"salt": "89bwo"}'),
	(_binary 0xFAF7A863A49C4428A7571DE537773355, _binary 0xEFBFBDEFBFBD68EFBFBD770B40EFBFBD, 'jane.doe@fastybird.com', 'active', '2017-05-03 11:27:34', '2019-05-29 07:14:00', 'user', '11b9174dbbc8f3f5ce3b3d270a3c73eedbe33832489aaa70fe0f370566bcd2f12a1a15041b318b57fd6ce6bb82471d9e2d7bc753064491bf418f9f9c3de21fcf', '{"salt": "89bwo"}'),
	(_binary 0xFD3385A5D8D744FBB7F1151C62A4A7B4, _binary 0xFE1152868CFD41BFACEB6CA95BAF6FE9, 'peter.pan@fastybird.com', 'active', '2017-05-03 11:27:34', '2019-05-29 07:14:00', 'user', '11b9174dbbc8f3f5ce3b3d270a3c73eedbe33832489aaa70fe0f370566bcd2f12a1a15041b318b57fd6ce6bb82471d9e2d7bc753064491bf418f9f9c3de21fcf', '{"salt": "89bwo"}'),
	(_binary 0xFE1152868CFD41BFACEB6CA95BAF6FE9, _binary 0xFD23CCB48D874EB394DA638AB4E10AE3, 'bruce.wane@fastybird.com', 'active', '2017-05-03 11:27:34', '2019-05-29 07:14:00', 'user', '11b9174dbbc8f3f5ce3b3d270a3c73eedbe33832489aaa70fe0f370566bcd2f12a1a15041b318b57fd6ce6bb82471d9e2d7bc753064491bf418f9f9c3de21fcf', '{"salt": "89bwo"}'),
	(_binary 0xFF99F3C470B943F99EF6C9B33B43ABCB, _binary 0xFAE8D7817E2C43189C8543BA637D14C5, 'clark.kent@fastybird.com', 'active', '2017-05-03 11:27:34', '2019-05-29 07:14:00', 'user', '11b9174dbbc8f3f5ce3b3d270a3c73eedbe33832489aaa70fe0f370566bcd2f12a1a15041b318b57fd6ce6bb82471d9e2d7bc753064491bf418f9f9c3de21fcf', '{"salt": "89bwo"}');

INSERT IGNORE INTO `fb_security_questions` (`question_id`, `account_id`, `question_question`, `question_answer`, `question_custom`, `created_at`, `updated_at`) VALUES
	(_binary 0x4774B7C38B2140C7B8D7E4242A962B71, _binary 0x5E79EFBFBD0D5B7C46EFBFBDEFBFBD34, 'firstPetName', 'super secret pet name', 0, '2017-09-25 20:45:34', '2017-10-29 12:26:40'),
	(_binary 0xC3F6947C173642B0BFD473B9070B4491, _binary 0xFDBE2CE23B1841F1AAABC3C56D286EB4, 'firstPetName', 'my first pet', 0, '2019-04-22 07:03:20', '2019-04-22 07:03:20');

INSERT IGNORE INTO `fb_security_tokens` (`token_id`, `parent_id`, `token_token`, `token_valid_till`, `token_status`, `params`, `created_at`, `updated_at`, `token_type`) VALUES
	(_binary 0x00892288A6F8485F9F05D47C8890D52A, NULL, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NzU5MzIxMTgsImV4cCI6MTU3NTk1MzcxOCwianRpIjoiYWI5Y2MwNjktM2QzZS00Mzk3LWFmYjItNjY2ZDIyOTFlZWUwIn0.G507WDqVFp_mIaHE8eyg1LEywTDV1OGgWtYgFwoyCQg', NULL, 'active', NULL, '2019-12-09 22:55:18', '2019-12-09 22:55:18', 'access_token'),
	(_binary 0x00E79DA3E4BE4F289C49082E724BBADE, _binary 0x00892288A6F8485F9F05D47C8890D52A, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NjU4OTc2ODMsImV4cCI6MTU2NjE1Njg4MywianRpIjoiNTA1NTIyY2MtMzg1Ni00OGIzLWI0MzUtMDk3MDQ4MWY1MWMyIn0.f3xN8CY5x5cM8htYp8v4hqEFqBpGu-zNOCSUcAOdiwY', NULL, 'active', NULL, '2019-08-15 19:34:43', '2019-08-15 19:34:43', 'refresh_token'),
	(_binary 0xC0E7081AE0B64698A23447FAA6B687F8, NULL, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NjU5NDI5ODAsImV4cCI6MTU2NTk2NDU4MCwianRpIjoiNjg4NTE4ZTktMGE2Ni00ZGVmLTg1ODItMWY5YzNjOGI1NGE4In0.ry-j4RnM9j1HrV6ktI_ATXVibV7FSe3yq52vN7e83jk', NULL, 'active', NULL, '2019-08-16 08:09:40', '2019-08-16 08:09:40', 'access_token'),
	(_binary 0xC0EF0AAF9EDE4B54A2B48E9D9005865A, _binary 0xED79B4AD291347BBAAEB2753F21C31B8, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NjU0Njc2NDgsImV4cCI6MTU2NTcyNjg0OCwianRpIjoiYjg5ZDM2NmYtMGNhMy00YTdhLTgxZjktM2IyZjIwNTM5NTNiIn0.SnS_Wn07gSszXrSb3Kr_Dqwbh82h3QFVGCvZpdAWjZg', NULL, 'active', NULL, '2019-08-10 20:07:28', '2019-08-10 20:07:28', 'refresh_token');

INSERT IGNORE INTO `fb_security_tokens_access` (`token_id`, `identity_id`) VALUES
	(_binary 0x00892288A6F8485F9F05D47C8890D52A, _binary 0x77331268EFBFBD3449EFBFBDEFBFBD04),
	(_binary 0xC0E7081AE0B64698A23447FAA6B687F8, _binary 0xFAF7A863A49C4428A7571DE537773355);

INSERT IGNORE INTO `fb_security_tokens_refresh` (`token_id`) VALUES
	(_binary 0x00E79DA3E4BE4F289C49082E724BBADE),
	(_binary 0xC0EF0AAF9EDE4B54A2B48E9D9005865A);

INSERT IGNORE INTO `fb_acl_roles` (`role_id`, `parent_id`, `role_name`, `role_description`, `created_at`, `updated_at`) VALUES
	(_binary 0xEFBFBD040158EFBFBDEFBFBD4DEFBFBD, NULL, 'guest', 'Guest', '2020-06-03 12:00:00', '2020-06-03 12:00:00'),
	(_binary 0xEFBFBDEFBFBDEFBFBD0FEFBFBD5C4F61, _binary 0xEFBFBD040158EFBFBDEFBFBD4DEFBFBD, 'authenticated', 'Authenticated', '2020-06-03 12:00:00', '2020-06-03 12:00:00'),
	(_binary 0x337A0518664B40EFBFBDEFBFBD7914EF, _binary 0xEFBFBDEFBFBDEFBFBD0FEFBFBD5C4F61, 'administrator', 'Administrator', '2020-06-03 12:00:00', '2020-06-03 12:00:00'),
	(_binary 0x89F4A14F7F78421699B8584AB9229F1C, _binary 0xEFBFBDEFBFBDEFBFBD0FEFBFBD5C4F61, 'user-defined', 'User defined', '2020-06-03 12:00:00', '2020-06-03 12:00:00');

INSERT IGNORE INTO `fb_accounts_roles` (`role_id`, `account_id`) VALUES
	(_binary 0x337A0518664B40EFBFBDEFBFBD7914EF, _binary 0x5E79EFBFBD0D5B7C46EFBFBDEFBFBD34),
	(_binary 0xEFBFBDEFBFBDEFBFBD0FEFBFBD5C4F61, _binary 0xEFBFBDEFBFBD68EFBFBD770B40EFBFBD);

INSERT IGNORE INTO `fb_acl_resources` (`resource_id`, `parent_id`, `resource_name`, `resource_description`, `created_at`, `updated_at`) VALUES
	(_binary 0x58EFBFBD2FEFBFBD78EFBFBD4FEFBFBD, NULL, 'fb/auth-node', 'Resource desc', '2020-06-03 12:00:00', '2020-06-03 12:00:00'),
	(_binary 0xEFBFBDEFBFBD510E37744C64EFBFBDEF, NULL, 'fb/other-node', 'Resource desc', '2020-06-03 12:00:00', '2020-06-03 12:00:00'),
	(_binary 0xC3F6947C173642B0BFD473B9070B4491, _binary 0xEFBFBDEFBFBD510E37744C64EFBFBDEF, 'node entity', 'Specific entity resource', '2020-06-03 12:00:00', '2020-06-03 12:00:00'),
	(_binary 0x00892288A6F8485F9F05D47C8890D52A, NULL, 'manage-access-control', 'Access roles management', '2020-06-03 12:00:00', '2020-06-03 12:00:00');

INSERT IGNORE INTO `fb_acl_privileges` (`privilege_id`, `resource_id`, `privilege_name`, `privilege_description`, `created_at`, `updated_at`) VALUES
	(_binary 0x22EFBFBDEFBFBDEFBFBDEFBFBDEFBFBD, _binary 0x58EFBFBD2FEFBFBD78EFBFBD4FEFBFBD, 'access', 'Access to node', '2020-06-03 12:00:00', '2020-06-03 12:00:00'),
	(_binary 0x3DEFBFBD2E1AEFBFBDEFBFBD49EFBFBD, _binary 0xEFBFBDEFBFBD510E37744C64EFBFBDEF, 'manage emails', 'Manage emails. Warning: Give to trusted roles only; this permission has security implications.', '2020-06-03 12:00:00', '2020-06-03 12:00:00'),
	(_binary 0x77331268EFBFBD3449EFBFBDEFBFBD04, _binary 0xEFBFBDEFBFBD510E37744C64EFBFBDEF, 'read', 'Read emails. Warning: Give to trusted roles only; this permission has security implications.', '2020-06-03 12:00:00', '2020-06-03 12:00:00');

INSERT IGNORE INTO `fb_acl_rules` (`rule_id`, `role_id`, `privilege_id`, `access`, `created_at`, `updated_at`) VALUES
	(_binary 0x317CEFBFBD377E1743EFBFBDC99AEFBF, _binary 0xEFBFBDEFBFBDEFBFBD0FEFBFBD5C4F61, _binary 0x22EFBFBDEFBFBDEFBFBDEFBFBDEFBFBD, 1, '2020-06-03 12:00:00', '2020-06-03 12:00:00'),
	(_binary 0x365DEFBFBD09EFBFBD7E4F29EFBFBDEF, _binary 0xEFBFBDEFBFBDEFBFBD0FEFBFBD5C4F61, _binary 0x3DEFBFBD2E1AEFBFBDEFBFBD49EFBFBD, 1, '2020-06-03 12:00:00', '2020-06-03 12:00:00');

INSERT IGNORE INTO `fb_vernemq_acl` (`id`, `account_id`, `mountpoint`, `client_id`, `username`, `password`, `publish_acl`, `subscribe_acl`) VALUES
	(_binary 0x5E79EFBFBD0D5B7C46EFBFBDEFBFBD34, _binary 0xEFBFBDEFBFBD68EFBFBD770B40EFBFBD, '', '', 'jane.doe@fastybird.com', 'passwd', '[]', '[]');
