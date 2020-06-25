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

INSERT IGNORE INTO `fb_security_tokens` (`token_id`, `parent_id`, `token_token`, `token_valid_till`, `token_status`, `params`, `created_at`, `updated_at`, `token_type`) VALUES
    (_binary 0x4F6710A138AA42649C0CB45285181270, NULL, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1ODU3NDI0MDAsImV4cCI6MTU4NTc2NDAwMCwianRpIjoiNGY2NzEwYTEtMzhhYS00MjY0LTljMGMtYjQ1Mjg1MTgxMjcwIiwic3ViIjoiNWU3OWVmYmYtYmQwZC01YjdjLTQ2ZWYtYmZiZGVmYmZiZDM0IiwidHlwZSI6ImFjY2VzcyIsInJvbGVzIjpbImFkbWluaXN0cmF0b3IiXX0.Ijw2E1hhDvqzyDpNExUm0vAE0IK08UeZJUcDO5QMTOI', '2020-04-01 18:00:00', 'active', '[]', '2020-06-23 19:32:18', '2020-06-23 19:32:18', 'access_token'),
    (_binary 0x35BE56240160432383EE6F59000934B4, _binary 0x4F6710A138AA42649C0CB45285181270, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1ODU3NDI0MDAsImV4cCI6MTU4NjAwMTYwMCwianRpIjoiMzViZTU2MjQtMDE2MC00MzIzLTgzZWUtNmY1OTAwMDkzNGI0Iiwic3ViIjoiNWU3OWVmYmYtYmQwZC01YjdjLTQ2ZWYtYmZiZGVmYmZiZDM0IiwidHlwZSI6InJlZnJlc2gifQ.JFlQH71H4FzdO8stTC8AuMNq1YDoCgXY7Ni0pyNX7NY', '2020-04-04 12:00:00', 'active', '[]', '2020-06-23 19:32:18', '2020-06-23 19:32:18', 'refresh_token'),
    (_binary 0x956A7E26E8C04BDB894ADD21B14E4A94, NULL, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1ODU3NDI0MDAsImV4cCI6MTU4NTc2NDAwMCwianRpIjoiOTU2YTdlMjYtZThjMC00YmRiLTg5NGEtZGQyMWIxNGU0YTk0Iiwic3ViIjoiZWZiZmJkZWYtYmZiZC02OGVmLWJmYmQtNzcwYjQwZWZiZmJkIiwidHlwZSI6ImFjY2VzcyIsInJvbGVzIjpbImF1dGhlbnRpY2F0ZWQiXX0.E2GOmUx2ohUC6r-JKj0EKN4oD-RVNWgPznP-vYYEP1c', '2020-04-01 18:00:00', 'active', '[]', '2020-06-23 19:34:41', '2020-06-23 19:34:41', 'access_token'),
    (_binary 0x850E0E2716294136B18DD4628557A724, _binary 0x956A7E26E8C04BDB894ADD21B14E4A94, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1ODU3NDI0MDAsImV4cCI6MTU4NjAwMTYwMCwianRpIjoiODUwZTBlMjctMTYyOS00MTM2LWIxOGQtZDQ2Mjg1NTdhNzI0Iiwic3ViIjoiZWZiZmJkZWYtYmZiZC02OGVmLWJmYmQtNzcwYjQwZWZiZmJkIiwidHlwZSI6InJlZnJlc2gifQ.G-nrGqBdMFWivJOqpdVnjI-MxlJ-Jo8f-BVDesUVNZk', '2020-04-04 12:00:00', 'active', '[]', '2020-06-23 19:34:42', '2020-06-23 19:34:42', 'refresh_token');

INSERT IGNORE INTO `fb_security_tokens_access` (`token_id`, `identity_id`) VALUES
    (_binary 0x4F6710A138AA42649C0CB45285181270, _binary 0x77331268EFBFBD3449EFBFBDEFBFBD04),
    (_binary 0x956A7E26E8C04BDB894ADD21B14E4A94, _binary 0xFAF7A863A49C4428A7571DE537773355);

INSERT IGNORE INTO `fb_security_tokens_refresh` (`token_id`) VALUES
    (_binary 0x35BE56240160432383EE6F59000934B4),
    (_binary 0x850E0E2716294136B18DD4628557A724);

INSERT IGNORE INTO `fb_acl_roles` (`role_id`, `parent_id`, `role_name`, `role_description`, `created_at`, `updated_at`) VALUES
    (_binary 0xEFBFBD040158EFBFBDEFBFBD4DEFBFBD, NULL, 'guest', 'Guest', '2020-06-03 12:00:00', '2020-06-03 12:00:00'),
    (_binary 0xEFBFBDEFBFBDEFBFBD0FEFBFBD5C4F61, _binary 0xEFBFBD040158EFBFBDEFBFBD4DEFBFBD, 'authenticated', 'Authenticated', '2020-06-03 12:00:00', '2020-06-03 12:00:00'),
    (_binary 0x337A0518664B40EFBFBDEFBFBD7914EF, _binary 0xEFBFBDEFBFBDEFBFBD0FEFBFBD5C4F61, 'administrator', 'Administrator', '2020-06-03 12:00:00', '2020-06-03 12:00:00'),
    (_binary 0x89F4A14F7F78421699B8584AB9229F1C, _binary 0xEFBFBDEFBFBDEFBFBD0FEFBFBD5C4F61, 'user-defined', 'User defined', '2020-06-03 12:00:00', '2020-06-03 12:00:00');

INSERT IGNORE INTO `fb_accounts_roles` (`role_id`, `account_id`) VALUES
    (_binary 0x337A0518664B40EFBFBDEFBFBD7914EF, _binary 0x5E79EFBFBD0D5B7C46EFBFBDEFBFBD34),
    (_binary 0xEFBFBDEFBFBDEFBFBD0FEFBFBD5C4F61, _binary 0xEFBFBDEFBFBD68EFBFBD770B40EFBFBD);

INSERT IGNORE INTO `fb_acl_resources` (`resource_id`, `parent_id`, `resource_name`, `resource_description`, `resource_origin`, `created_at`, `updated_at`) VALUES
    (_binary 0x58EFBFBD2FEFBFBD78EFBFBD4FEFBFBD, NULL, 'fb/auth-node', 'Resource desc', 'com.fastybird.auth-node', '2020-06-03 12:00:00', '2020-06-03 12:00:00'),
    (_binary 0xEFBFBDEFBFBD510E37744C64EFBFBDEF, NULL, 'fb/other-node', 'Resource desc', 'com.fastybird.auth-node', '2020-06-03 12:00:00', '2020-06-03 12:00:00'),
    (_binary 0xC3F6947C173642B0BFD473B9070B4491, _binary 0xEFBFBDEFBFBD510E37744C64EFBFBDEF, 'node entity', 'Specific entity resource', 'com.fastybird.auth-node', '2020-06-03 12:00:00', '2020-06-03 12:00:00'),
    (_binary 0xFE1152868CFD41BFACEB6CA95BAF6FE9, NULL, 'fastybird/auth-node', 'fastybird/auth-node', 'com.fastybird.auth-node', '2020-06-03 12:00:00', '2020-06-03 12:00:00'),
    (_binary 0x00892288A6F8485F9F05D47C8890D52A, NULL, 'fastybird/manage-access-control', 'fastybird/manage-access-control', 'com.fastybird.auth-node', '2020-06-03 12:00:00', '2020-06-03 12:00:00'),
    (_binary 0xBAEFEC20192741CEA7E0AE74BD641B9C, NULL, 'fastybird/manage-accounts', 'fastybird/manage-accounts', 'com.fastybird.auth-node', '2020-06-03 12:00:00', '2020-06-03 12:00:00'),
    (_binary 0x0B46D3D6C980494A8B40F19E6095E610, NULL, 'fastybird/manage-emails', 'fastybird/manage-emails', 'com.fastybird.auth-node', '2020-06-03 12:00:00', '2020-06-03 12:00:00'),
    (_binary 0xFDBE2CE23B1841F1AAABC3C56D286EB4, NULL, 'fastybird/manage-identities', 'fastybird/manage-identities', 'com.fastybird.auth-node', '2020-06-03 12:00:00', '2020-06-03 12:00:00');

INSERT IGNORE INTO `fb_acl_privileges` (`privilege_id`, `resource_id`, `privilege_name`, `privilege_description`, `created_at`, `updated_at`) VALUES
    (_binary 0x22EFBFBDEFBFBDEFBFBDEFBFBDEFBFBD, _binary 0x58EFBFBD2FEFBFBD78EFBFBD4FEFBFBD, 'access', 'Access to node', '2020-06-03 12:00:00', '2020-06-03 12:00:00'),
    (_binary 0x3DEFBFBD2E1AEFBFBDEFBFBD49EFBFBD, _binary 0xEFBFBDEFBFBD510E37744C64EFBFBDEF, 'manage emails', 'Manage emails. Warning: Give to trusted roles only; this permission has security implications.', '2020-06-03 12:00:00', '2020-06-03 12:00:00'),
    (_binary 0x77331268EFBFBD3449EFBFBDEFBFBD04, _binary 0xEFBFBDEFBFBD510E37744C64EFBFBDEF, 'read', 'Read emails. Warning: Give to trusted roles only; this permission has security implications.', '2020-06-03 12:00:00', '2020-06-03 12:00:00');

INSERT IGNORE INTO `fb_acl_rules` (`rule_id`, `role_id`, `privilege_id`, `access`, `created_at`, `updated_at`) VALUES
    (_binary 0x317CEFBFBD377E1743EFBFBDC99AEFBF, _binary 0xEFBFBDEFBFBDEFBFBD0FEFBFBD5C4F61, _binary 0x22EFBFBDEFBFBDEFBFBDEFBFBDEFBFBD, 1, '2020-06-03 12:00:00', '2020-06-03 12:00:00'),
    (_binary 0x365DEFBFBD09EFBFBD7E4F29EFBFBDEF, _binary 0xEFBFBDEFBFBDEFBFBD0FEFBFBD5C4F61, _binary 0x3DEFBFBD2E1AEFBFBDEFBFBD49EFBFBD, 1, '2020-06-03 12:00:00', '2020-06-03 12:00:00');

INSERT IGNORE INTO `fb_vernemq_acl` (`id`, `account_id`, `mountpoint`, `client_id`, `username`, `password`, `publish_acl`, `subscribe_acl`) VALUES
    (_binary 0x5E79EFBFBD0D5B7C46EFBFBDEFBFBD34, _binary 0xEFBFBDEFBFBD68EFBFBD770B40EFBFBD, '', '', 'jane.doe@fastybird.com', 'passwd', '[]', '[]');
