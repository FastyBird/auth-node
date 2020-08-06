INSERT IGNORE INTO `fb_accounts` (`account_id`, `account_type`, `account_status`, `account_last_visit`, `params`,
                                  `created_at`, `updated_at`)
VALUES (_binary 0xBF4CD8702AAC45F0A85EE1CEFD2D6D9A, 'machine', 'activated', '2019-11-07 22:30:56', '[]',
        '2017-01-03 11:30:00', '2017-01-03 11:30:00');

INSERT IGNORE INTO `fb_accounts_machines` (`account_id`, `parent_id`)
VALUES (_binary 0xBF4CD8702AAC45F0A85EE1CEFD2D6D9A, NULL);

INSERT IGNORE INTO `fb_identities` (`identity_id`, `account_id`, `identity_uid`, `identity_status`, `created_at`,
                                    `updated_at`, `identity_type`, `identity_token`, `params`)
VALUES (_binary 0x4F6710A138AA42649C0CB45285181270, _binary 0xBF4CD8702AAC45F0A85EE1CEFD2D6D9A, 'second-device',
        'active', '2017-05-03 11:27:34', '2019-05-29 07:14:00', 'machine', 'unsecuredDevicePassword', '[]');

INSERT IGNORE INTO `fb_accounts_roles` (`role_id`, `account_id`)
VALUES (_binary 0xEFBFBDEFBFBDEFBFBD0FEFBFBD5C4F61, _binary 0xBF4CD8702AAC45F0A85EE1CEFD2D6D9A);


INSERT IGNORE INTO `vmq_auth_acl` (`id`, `identity_id`, `mountpoint`, `client_id`, `username`, `password`,
                                     `publish_acl`, `subscribe_acl`)
VALUES (_binary 0xBF4CD8702AAC45F0A85EE1CEFD2D6D9A, _binary 0x4F6710A138AA42649C0CB45285181270, '', '',
        'second-device', 'passwd', '[]', '[]');