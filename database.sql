CREATE TABLE `sms_gateways` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`title` varchar(255) NOT NULL,
	`handler` varchar(255) NOT NULL,
	`status` tinyint(4) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `sms_gateways_numbers` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`gateway` int(11) NOT NULL,
	`number` varchar(20) NOT NULL,
	`status` tinyint(4) NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `number_2` (`number`),
	KEY `number` (`gateway`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `sms_gateways_numbers` ADD FOREIGN KEY (`gateway`) REFERENCES `sms_gateways`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;

CREATE TABLE `sms_gateways_params` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`gateway` int(11) NOT NULL,
	`name` varchar(255) NOT NULL,
	`value` text NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `gateway` (`gateway`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `sms_gateways_params` ADD FOREIGN KEY (`gateway`) REFERENCES `sms_gateways`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;

CREATE TABLE `sms_templates` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(255) COLLATE utf8_persian_ci NOT NULL,
	`lang` varchar(2) COLLATE utf8_persian_ci NOT NULL,
	`event` varchar(255) COLLATE utf8_persian_ci DEFAULT NULL,
	`variables` text COLLATE utf8_persian_ci,
	`render` varchar(255) COLLATE utf8_persian_ci DEFAULT NULL,
	`text` text COLLATE utf8_persian_ci NOT NULL,
	`status` tinyint(4) NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `name` (`name`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

CREATE TABLE `sms_get` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`receive_at` int(11) NOT NULL,
	`sender_number` varchar(12) COLLATE utf8_persian_ci NOT NULL,
	`sender_user` int(11) DEFAULT NULL,
	`receiver_number` int(11) NOT NULL,
	`text` text COLLATE utf8_persian_ci NOT NULL,
	`status` tinyint(4) NOT NULL,
	PRIMARY KEY (`id`),
	KEY `sender_user` (`sender_user`),
	KEY `reciver_number` (`receiver_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

ALTER TABLE `sms_get` ADD FOREIGN KEY (`sender_user`) REFERENCES `userpanel_users`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;
ALTER TABLE `sms_get` ADD FOREIGN KEY (`receiver_number`) REFERENCES `sms_gateways_numbers`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;

CREATE TABLE `sms_sent` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`send_at` int(11) NOT NULL,
	`sender_number` int(11) NOT NULL,
	`sender_user` int(11) DEFAULT NULL,
	`receiver_number` varchar(14) COLLATE utf8_persian_ci NOT NULL,
	`receiver_user` int(11) DEFAULT NULL,
	`text` text COLLATE utf8_persian_ci NOT NULL,
	`status` tinyint(4) NOT NULL,
	PRIMARY KEY (`id`),
	KEY `sender_user` (`sender_user`),
	KEY `sender_number` (`sender_number`),
	KEY `receiver_user` (`receiver_user`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

ALTER TABLE `sms_sent` ADD FOREIGN KEY (`sender_number`) REFERENCES `sms_gateways_numbers`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;
ALTER TABLE `sms_sent` ADD FOREIGN KEY (`sender_user`) REFERENCES `userpanel_users`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;
ALTER TABLE `sms_sent` ADD FOREIGN KEY (`receiver_user`) REFERENCES `userpanel_users`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;

CREATE TABLE `sms_sent_params` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`sms` int(11) NOT NULL,
	`name` varchar(100) NOT NULL,
	`value` varchar(100) NOT NULL,
	PRIMARY KEY (`id`),
	KEY `sms` (`sms`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `sms_sent_params` ADD FOREIGN KEY (`sms`) REFERENCES `sms_sent`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;

INSERT INTO `userpanel_usertypes_permissions` (`type`, `name`) VALUES
(1, 'sms_get_list'),
(1, 'sms_get_list_anonymous'),
(1, 'sms_send'),
(1, 'sms_sent_list'),
(1, 'sms_sent_list_anonymous'),
(1, 'sms_settings_gateways_add'),
(1, 'sms_settings_gateways_delete'),
(1, 'sms_settings_gateways_edit'),
(1, 'sms_settings_gateways_list'),
(1, 'sms_settings_templates_add'),
(1, 'sms_settings_templates_delete'),
(1, 'sms_settings_templates_edit'),
(1, 'sms_settings_templates_list'),
(2, 'sms_get_list'),
(2, 'sms_sent_list');