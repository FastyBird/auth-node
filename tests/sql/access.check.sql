INSERT IGNORE INTO `fb_acl_privileges` (`privilege_id`, `resource_id`, `privilege_name`, `privilege_description`, `created_at`, `updated_at`) VALUES
	(_binary 0xFAE8D7817E2C43189C8543BA637D14C5, _binary 0x00892288A6F8485F9F05D47C8890D52A, 'read configuration', 'Access to read configuration rows', '2020-06-03 12:00:00', '2020-06-03 12:00:00');

INSERT IGNORE INTO `fb_acl_rules` (`rule_id`, `role_id`, `privilege_id`, `access`, `created_at`, `updated_at`) VALUES
	(_binary 0xFDBE2CE23B1841F1AAABC3C56D286EB4, _binary 0xEFBFBDEFBFBDEFBFBD0FEFBFBD5C4F61, _binary 0xFAE8D7817E2C43189C8543BA637D14C5, 1, '2020-06-03 12:00:00', '2020-06-03 12:00:00');
