INSERT IGNORE INTO `fb_accounts` (`account_id`, `account_type`, `account_status`, `account_last_visit`, `params`, `created_at`, `updated_at`) VALUES
    (_binary 0x5E79EFBFBD0D5B7C46EFBFBDEFBFBD34, 'user', 'activated', '2019-11-07 22:30:56', '{"datetime": {"zone": "Europe/Prague", "format": {"date": "DD.MM.YYYY", "time": "HH:mm"}, "week_start": 1}}', '2017-01-03 11:30:00', '2017-01-03 11:30:00'),
    (_binary 0xEFBFBDEFBFBD68EFBFBD770B40EFBFBD, 'user', 'activated', '2019-05-29 07:38:24', '{"datetime": {"zone": "Europe/Prague", "format": {"date": "DD.MM.YYYY", "time": "HH:mm"}, "week_start": 1}}', '2017-01-04 12:30:00', '2017-01-04 12:30:00'),
    (_binary 0xFAE8D7817E2C43189C8543BA637D14C5, 'user', 'approvalWaiting', '2019-05-29 07:38:24', '{"datetime": {"zone": "Europe/Prague", "format": {"date": "DD.MM.YYYY", "time": "HH:mm"}, "week_start": 1}}', '2017-01-04 12:30:00', '2017-01-04 12:30:00'),
    (_binary 0xFD23CCB48D874EB394DA638AB4E10AE3, 'user', 'notActivated', '2019-05-29 07:38:24', '{"datetime": {"zone": "Europe/Prague", "format": {"date": "DD.MM.YYYY", "time": "HH:mm"}, "week_start": 1}}', '2017-01-04 12:30:00', '2017-01-04 12:30:00'),
    (_binary 0xFDBE2CE23B1841F1AAABC3C56D286EB4, 'user', 'deleted', '2019-05-29 07:38:24', '{"datetime": {"zone": "Europe/Prague", "format": {"date": "DD.MM.YYYY", "time": "HH:mm"}, "week_start": 1}}', '2017-01-04 12:30:00', '2017-01-04 12:30:00'),
    (_binary 0xFE1152868CFD41BFACEB6CA95BAF6FE9, 'user', 'blocked', '2019-05-29 07:38:24', '{"datetime": {"zone": "Europe/Prague", "format": {"date": "DD.MM.YYYY", "time": "HH:mm"}, "week_start": 1}}', '2017-01-04 12:30:00', '2017-01-04 12:30:00'),
    (_binary 0x16E5DB2900064484AC385CDEA5A008F5, 'machine', 'activated', '2019-11-07 22:30:56', '[]', '2017-01-03 11:30:00', '2017-01-03 11:30:00'),
    (_binary 0xFF32AC4EF0104C859CB3D310F6708A4E, 'machine', 'activated', '2019-11-07 22:30:56', '[]', '2017-01-03 11:30:00', '2017-01-03 11:30:00');

INSERT IGNORE INTO `fb_accounts_users` (`account_id`, `parent_id`, `account_request_hash`) VALUES
    (_binary 0x5E79EFBFBD0D5B7C46EFBFBDEFBFBD34, NULL, 'NGZqMmVxdnhubjJpIyMxNTc0NDUwNDAz'),
    (_binary 0xEFBFBDEFBFBD68EFBFBD770B40EFBFBD, NULL, 'YjRqZXFoZGw1Z3ZzIyMxNTc0MjA3NDQ1'),
    (_binary 0xFAE8D7817E2C43189C8543BA637D14C5, NULL, 'YjRqZXFoZGw1Z3ZzIyMxNTc0MjA3NDQ1'),
    (_binary 0xFD23CCB48D874EB394DA638AB4E10AE3, NULL, 'YjRqZXFoZGw1Z3ZzIyMxNTc0MjA3NDQ1'),
    (_binary 0xFDBE2CE23B1841F1AAABC3C56D286EB4, NULL, 'YjRqZXFoZGw1Z3ZzIyMxNTc0MjA3NDQ1'),
    (_binary 0xFE1152868CFD41BFACEB6CA95BAF6FE9, NULL, 'YjRqZXFoZGw1Z3ZzIyMxNTc0MjA3NDQ1');

INSERT IGNORE INTO `fb_accounts_machines` (`account_id`, `parent_id`) VALUES
    (_binary 0x16E5DB2900064484AC385CDEA5A008F5, NULL),
    (_binary 0xFF32AC4EF0104C859CB3D310F6708A4E, NULL);

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
    (_binary 0xFF99F3C470B943F99EF6C9B33B43ABCB, _binary 0xFAE8D7817E2C43189C8543BA637D14C5, 'clark.kent@fastybird.com', 'active', '2017-05-03 11:27:34', '2019-05-29 07:14:00', 'user', '11b9174dbbc8f3f5ce3b3d270a3c73eedbe33832489aaa70fe0f370566bcd2f12a1a15041b318b57fd6ce6bb82471d9e2d7bc753064491bf418f9f9c3de21fcf', '{"salt": "89bwo"}'),
    (_binary 0x540F6CFC39D1417587FBAAC729D78C0A, _binary 0xFF32AC4EF0104C859CB3D310F6708A4E, 'deviceUsername', 'active', '2017-05-03 11:27:34', '2019-05-29 07:14:00', 'machine', '11b9174dbbc8f3f5ce3b3d270a3c73eedbe33832489aaa70fe0f370566bcd2f12a1a15041b318b57fd6ce6bb82471d9e2d7bc753064491bf418f9f9c3de21fcf', '[]');

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

INSERT IGNORE INTO `fb_acl_resources` (`resource_id`, `parent_id`, `resource_name`, `created_at`, `updated_at`, `resource_description`, `created_by`, `updated_by`, `resource_origin`) VALUES
    (_binary 0x16E5DB2900064484AC385CDEA5A008F5, NULL, 'fastybird/manage-triggers', '2020-06-25 15:00:34', '2020-06-25 15:00:34', 'fastybird/manage-triggers', NULL, NULL, 'com.fastybird.triggers-node'),
    (_binary 0x2D19491BE821486EBADD6681E61C9950, NULL, 'fastybird/manage-identities', '2020-06-25 14:55:50', '2020-06-25 14:55:50', 'fastybird/manage-identities', NULL, NULL, 'com.fastybird.auth-node'),
    (_binary 0x2DAD7F73912A4F51B80ABB4047CB1020, NULL, 'fastybird/websockets-node', '2020-06-25 15:00:35', '2020-06-25 15:00:35', 'fastybird/websockets-node', NULL, NULL, 'com.fastybird.websockets-node'),
    (_binary 0x5280F66501394E438A2E50DAA3405124, NULL, 'fastybird/manage-accounts', '2020-06-25 14:55:49', '2020-06-25 14:55:49', 'fastybird/manage-accounts', NULL, NULL, 'com.fastybird.auth-node'),
    (_binary 0x71F35C4C5BAE41BB92F3566CD2683934, NULL, 'fastybird/auth-node', '2020-06-25 14:55:48', '2020-06-25 14:55:48', 'fastybird/auth-node', NULL, NULL, 'com.fastybird.auth-node'),
    (_binary 0x72D26676D7D44546AF22CBBA4965C42A, NULL, 'fastybird/devices-node', '2020-06-25 15:00:33', '2020-06-25 15:00:33', 'fastybird/devices-node', NULL, NULL, 'com.fastybird.devices-node'),
    (_binary 0x85A3C1583F11411C8695D552952B60A0, NULL, 'fastybird/ui-node', '2020-06-25 15:00:34', '2020-06-25 15:00:34', 'fastybird/ui-node', NULL, NULL, 'com.fastybird.ui-node'),
    (_binary 0xA7D7A91AD4B5439697FC8EC5E6601D77, NULL, 'fastybird/manage-widgets', '2020-06-25 15:00:34', '2020-06-25 15:00:34', 'fastybird/manage-widgets', NULL, NULL, 'com.fastybird.ui-node'),
    (_binary 0xC63350393B274A5DBF1D367FB3353DB5, NULL, 'fastybird/triggers-node', '2020-06-25 15:00:34', '2020-06-25 15:00:34', 'fastybird/triggers-node', NULL, NULL, 'com.fastybird.triggers-node'),
    (_binary 0xE5BCC972B0D24013ACC52035F6E4515B, NULL, 'fastybird/manage-access-control', '2020-06-25 19:37:44', '2020-06-25 19:37:44', 'fastybird/manage-access-control', NULL, NULL, 'com.fastybird.auth-node'),
    (_binary 0xE5D54B81504F48ECB5EB5DF691EC10ED, NULL, 'fastybird/manage-devices', '2020-06-25 15:00:33', '2020-06-25 15:00:33', 'fastybird/manage-devices', NULL, NULL, 'com.fastybird.devices-node'),
    (_binary 0xFCCAB458AB9349CDB63C9E46547AAE24, NULL, 'fastybird/manage-dashboards', '2020-06-25 15:00:34', '2020-06-25 15:00:34', 'fastybird/manage-dashboards', NULL, NULL, 'com.fastybird.ui-node'),
    (_binary 0xFF32AC4EF0104C859CB3D310F6708A4E, NULL, 'fastybird/manage-emails', '2020-06-25 14:55:50', '2020-06-25 14:55:50', 'fastybird/manage-emails', NULL, NULL, 'com.fastybird.auth-node'),
    (_binary 0xC3F6947C173642B0BFD473B9070B4491, _binary 0xFF32AC4EF0104C859CB3D310F6708A4E, 'node entity', '2020-06-25 14:55:50', '2020-06-25 14:55:50', 'node entity', NULL, NULL, 'com.fastybird.auth-node');

INSERT IGNORE INTO `fb_acl_privileges` (`privilege_id`, `resource_id`, `privilege_name`, `created_at`, `updated_at`, `privilege_description`, `created_by`, `updated_by`) VALUES
    (_binary 0x063BA1FA0515478C8434DBEE62329ADA, _binary 0x72D26676D7D44546AF22CBBA4965C42A, 'access', '2020-06-25 15:00:33', '2020-06-25 15:00:33', 'Access to module', NULL, NULL),
    (_binary 0x0AB1E04F362C4432B9B1B6C5E493F831, _binary 0xFF32AC4EF0104C859CB3D310F6708A4E, 'edit email', '2020-06-25 14:55:50', '2020-06-25 14:58:03', 'Access to update existing email', NULL, NULL),
    (_binary 0x10B646C638F045C4909D7468FF254F08, _binary 0xE5BCC972B0D24013ACC52035F6E4515B, 'update configuration', '2020-06-25 19:37:46', '2020-06-25 19:37:46', 'Access to update configuration row', NULL, NULL),
    (_binary 0x1832747B28904982A48B6291444DB89D, _binary 0xE5D54B81504F48ECB5EB5DF691EC10ED, 'delete device', '2020-06-25 15:00:34', '2020-06-25 15:00:34', 'Access to remove existing device', NULL, NULL),
    (_binary 0x1A46CF0ACCE94AB58B7575E2965F2957, _binary 0x2D19491BE821486EBADD6681E61C9950, 'create identity', '2020-06-25 14:55:50', '2020-06-25 14:58:03', 'Access to create new identity', NULL, NULL),
    (_binary 0x1A7A3442425F4D00AE76AD0320B4895D, _binary 0x2D19491BE821486EBADD6681E61C9950, 'delete identity', '2020-06-25 14:55:50', '2020-06-25 14:58:03', 'Access to remove existing identity', NULL, NULL),
    (_binary 0x28956FEAF25648A0A2CD13C5FF7A9417, _binary 0xA7D7A91AD4B5439697FC8EC5E6601D77, 'edit widget', '2020-06-25 15:00:35', '2020-06-25 15:00:35', 'Access to update existing widget', NULL, NULL),
    (_binary 0x29EA8E835F344B90A96A7BFB58FBF6E9, _binary 0xA7D7A91AD4B5439697FC8EC5E6601D77, 'read widgets', '2020-06-25 15:00:34', '2020-06-25 15:00:34', 'Access to widgets list', NULL, NULL),
    (_binary 0x2C8DB87EAAD74AEC80EB49583C55D567, _binary 0x16E5DB2900064484AC385CDEA5A008F5, 'edit trigger', '2020-06-25 15:00:34', '2020-06-25 15:00:34', 'Access to update existing trigger', NULL, NULL),
    (_binary 0x2DBA5C9B9E364C008E05D6D1C3CADAB6, _binary 0xFCCAB458AB9349CDB63C9E46547AAE24, 'read dashboards', '2020-06-25 15:00:34', '2020-06-25 15:00:34', 'Access to dashboards list', NULL, NULL),
    (_binary 0x3208FD6E669A42DCB045F3273D2A393A, _binary 0x2D19491BE821486EBADD6681E61C9950, 'read identities', '2020-06-25 14:55:50', '2020-06-25 14:58:03', 'Access to accounts list', NULL, NULL),
    (_binary 0x3352EDD8F00A4C09AC84631CFC341EDC, _binary 0xFCCAB458AB9349CDB63C9E46547AAE24, 'edit dashboard', '2020-06-25 15:00:34', '2020-06-25 15:00:34', 'Access to update existing dashboard', NULL, NULL),
    (_binary 0x34998957D278439FA7E2DE42D52DAA95, _binary 0x85A3C1583F11411C8695D552952B60A0, 'access', '2020-06-25 15:00:34', '2020-06-25 15:00:34', 'Access to module', NULL, NULL),
    (_binary 0x4789518FCF434BC4A940C7C74168201E, _binary 0xFF32AC4EF0104C859CB3D310F6708A4E, 'delete email', '2020-06-25 14:55:50', '2020-06-25 14:58:03', 'Access to remove existing email', NULL, NULL),
    (_binary 0x53B7888641C0446E9A40F781F2D6FE8F, _binary 0xE5D54B81504F48ECB5EB5DF691EC10ED, 'create device', '2020-06-25 15:00:33', '2020-06-25 15:00:33', 'Access to create new device', NULL, NULL),
    (_binary 0x540F6CFC39D1417587FBAAC729D78C0A, _binary 0xE5D54B81504F48ECB5EB5DF691EC10ED, 'read devices', '2020-06-25 15:00:33', '2020-06-25 15:00:33', 'Access to devices list', NULL, NULL),
    (_binary 0x54C6C043A5D74EBCB9E5FBB45A81FC8E, _binary 0x2D19491BE821486EBADD6681E61C9950, 'edit identity', '2020-06-25 14:55:50', '2020-06-25 14:58:03', 'Access to update existing identity', NULL, NULL),
    (_binary 0x57CBF2C8F56347E49C7F982229B8ADBB, _binary 0xE5BCC972B0D24013ACC52035F6E4515B, 'read configuration', '2020-06-25 19:37:46', '2020-06-25 19:37:46', 'Access to read configuration rows', NULL, NULL),
    (_binary 0x5C75A7DDB58541C5993C4CE761E6F2CF, _binary 0x5280F66501394E438A2E50DAA3405124, 'edit account', '2020-06-25 14:55:50', '2020-06-25 14:58:02', 'Access to update existing account', NULL, NULL),
    (_binary 0x6523D6EF304748D8B8A6C23FCC468F0B, _binary 0xFF32AC4EF0104C859CB3D310F6708A4E, 'create email', '2020-06-25 14:55:50', '2020-06-25 14:58:02', 'Access to create new email', NULL, NULL),
    (_binary 0x802E01730221477BB5B0805676448D3F, _binary 0x16E5DB2900064484AC385CDEA5A008F5, 'delete trigger', '2020-06-25 15:00:34', '2020-06-25 15:00:34', 'Access to remove existing trigger', NULL, NULL),
    (_binary 0x8BEC28521D55433EBAB0476CBF2020B5, _binary 0xC63350393B274A5DBF1D367FB3353DB5, 'access', '2020-06-25 15:00:34', '2020-06-25 15:00:34', 'Access to module', NULL, NULL),
    (_binary 0x9A680D935C964DAA806C9DD85B648B02, _binary 0xFCCAB458AB9349CDB63C9E46547AAE24, 'delete dashboard', '2020-06-25 15:00:34', '2020-06-25 15:00:34', 'Access to remove existing dashboard', NULL, NULL),
    (_binary 0x9AB58F107A9242EE8EF061A01E57B229, _binary 0x5280F66501394E438A2E50DAA3405124, 'read accounts', '2020-06-25 14:55:49', '2020-06-25 14:58:02', 'Access to accounts list', NULL, NULL),
    (_binary 0xA31CCF33772A4FD78A63A66C895F572F, _binary 0xA7D7A91AD4B5439697FC8EC5E6601D77, 'delete widget', '2020-06-25 15:00:35', '2020-06-25 15:00:35', 'Access to remove existing widget', NULL, NULL),
    (_binary 0xA4FB13BE7A08416592F37EB9824BDF69, _binary 0xFF32AC4EF0104C859CB3D310F6708A4E, 'read emails', '2020-06-25 14:55:50', '2020-06-25 14:58:02', 'Access to accounts list', NULL, NULL),
    (_binary 0xAB19282359E34D49AE875F0C12A5EA7E, _binary 0x16E5DB2900064484AC385CDEA5A008F5, 'create trigger', '2020-06-25 15:00:34', '2020-06-25 15:00:34', 'Access to create new trigger', NULL, NULL),
    (_binary 0xB5D3BEA242D847C3A0EB13EE6C61DF25, _binary 0x2DAD7F73912A4F51B80ABB4047CB1020, 'access', '2020-06-25 15:00:35', '2020-06-25 15:00:35', 'Access to module', NULL, NULL),
    (_binary 0xB5E2480C5DB344468AB355EAD9F8ED34, _binary 0xE5BCC972B0D24013ACC52035F6E4515B, 'delete configuration', '2020-06-25 19:37:46', '2020-06-25 19:37:46', 'Access to remove configuration row', NULL, NULL),
    (_binary 0xB70445C6B1F2408B937E441043B29430, _binary 0x5280F66501394E438A2E50DAA3405124, 'delete account', '2020-06-25 14:55:50', '2020-06-25 14:58:02', 'Access to remove existing account', NULL, NULL),
    (_binary 0xBFC25C0FBA1F472D9D37C98B71D196FB, _binary 0xA7D7A91AD4B5439697FC8EC5E6601D77, 'create widget', '2020-06-25 15:00:35', '2020-06-25 15:00:35', 'Access to create new widget', NULL, NULL),
    (_binary 0xCEE1410D28D14668A5BD89B2706912F9, _binary 0xE5BCC972B0D24013ACC52035F6E4515B, 'create configuration', '2020-06-25 19:37:46', '2020-06-25 19:37:46', 'Access to create new configuration row', NULL, NULL),
    (_binary 0xD6F2334176934336AFD1553E080916F6, _binary 0x16E5DB2900064484AC385CDEA5A008F5, 'read triggers', '2020-06-25 15:00:34', '2020-06-25 15:00:34', 'Access to triggers list', NULL, NULL),
    (_binary 0xF0526F61617D4F12AAF28134CBE0D5EE, _binary 0x5280F66501394E438A2E50DAA3405124, 'create account', '2020-06-25 14:55:49', '2020-06-25 14:58:02', 'Access to create new account', NULL, NULL),
    (_binary 0xF0F4339CC7BC45F6A0D79D45D141F394, _binary 0xFCCAB458AB9349CDB63C9E46547AAE24, 'create dashboard', '2020-06-25 15:00:34', '2020-06-25 15:00:34', 'Access to create new dashboard', NULL, NULL),
    (_binary 0xF4090490F9A44CA9864A443946F2F56C, _binary 0x71F35C4C5BAE41BB92F3566CD2683934, 'access', '2020-06-25 14:55:49', '2020-06-25 14:58:02', 'Access to module', NULL, NULL),
    (_binary 0xFA53D77C02EE443099B5D832C5B04556, _binary 0xE5D54B81504F48ECB5EB5DF691EC10ED, 'edit device', '2020-06-25 15:00:33', '2020-06-25 15:00:33', 'Access to update existing device', NULL, NULL);

INSERT IGNORE INTO `fb_acl_rules` (`rule_id`, `role_id`, `privilege_id`, `access`, `created_at`, `updated_at`) VALUES
    (_binary 0x317CEFBFBD377E1743EFBFBDC99AEFBF, _binary 0xEFBFBDEFBFBDEFBFBD0FEFBFBD5C4F61, _binary 0x53B7888641C0446E9A40F781F2D6FE8F, 1, '2020-06-03 12:00:00', '2020-06-03 12:00:00'),
    (_binary 0x365DEFBFBD09EFBFBD7E4F29EFBFBDEF, _binary 0xEFBFBDEFBFBDEFBFBD0FEFBFBD5C4F61, _binary 0x1832747B28904982A48B6291444DB89D, 0, '2020-06-03 12:00:00', '2020-06-03 12:00:00');

INSERT IGNORE INTO `fb_vernemq_acl` (`id`, `account_id`, `mountpoint`, `client_id`, `username`, `password`, `publish_acl`, `subscribe_acl`) VALUES
    (_binary 0x5E79EFBFBD0D5B7C46EFBFBDEFBFBD34, _binary 0xEFBFBDEFBFBD68EFBFBD770B40EFBFBD, '', '', 'jane.doe@fastybird.com', 'passwd', '[]', '[]');
