CREATE TABLE `plugins` (
    `identifier` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `name` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `color` varchar(7) COLLATE utf8mb4_unicode_ci DEFAULT 'black',
    `status` int(11) DEFAULT '0',
    UNIQUE KEY `identifier` (`identifier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEPARATOR --

INSERT IGNORE INTO `plugins` (`identifier`, `name`, `color`, `status`) VALUES ('facebook', 'Facebook Analyzer', '#4268B2', 0);

-- SEPARATOR --

INSERT IGNORE INTO `plugins` (`identifier`, `name`, `color`, `status`) VALUES ('youtube', 'YouTube Analyzer', '#FF0000', 0);

-- SEPARATOR --

INSERT IGNORE INTO `plugins` (`identifier`, `name`, `color`, `status`) VALUES ('twitter', 'Twitter Analyzer', '#1da1f2', 1);

-- SEPARATOR --

INSERT IGNORE INTO `plugins` (`identifier`, `name`, `color`, `status`) VALUES ('instagram', 'Instagram Analyzer', '#f75581', 1);

-- SEPARATOR --

INSERT IGNORE INTO `plugins` (`identifier`, `name`, `color`, `status`) VALUES ('twitch', 'Twitch Analyzer', '#6441a5', 0);

-- SEPARATOR --

CREATE TABLE `favorites` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) DEFAULT NULL,
    `source_user_id` int(11) DEFAULT NULL,
    `source` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT 'INSTAGRAM',
    `date` datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `favorites_id_uindex` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEPARATOR --

CREATE TABLE `instagram_logs` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `instagram_user_id` int(11) DEFAULT NULL,
    `username` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `followers` int(11) DEFAULT NULL,
    `following` int(11) DEFAULT NULL,
    `uploads` int(11) DEFAULT NULL,
    `average_engagement_rate` varchar(8) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `date` datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `instagram_logs_id_uindex` (`id`),
    KEY `username` (`username`),
    KEY `instagram_logs_instagram_user_id_index` (`instagram_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEPARATOR --

CREATE TABLE `instagram_media` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `media_id` bigint(20) DEFAULT NULL,
    `instagram_user_id` int(11) DEFAULT NULL,
    `shortcode` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `created_date` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
    `caption` mediumtext COLLATE utf8mb4_unicode_ci,
    `comments` int(11) DEFAULT NULL,
    `likes` bigint(20) DEFAULT NULL,
    `media_url` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `media_image_url` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `type` enum('VIDEO','IMAGE','SIDECAR','CAROUSEL') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `mentions` mediumtext COLLATE utf8mb4_unicode_ci,
    `hashtags` mediumtext COLLATE utf8mb4_unicode_ci,
    `date` datetime DEFAULT NULL,
    `last_check_date` datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `instagram_media_id_uindex` (`id`),
    UNIQUE KEY `instagram_media_pk` (`media_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEPARATOR --

CREATE TABLE `instagram_users` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `instagram_id` bigint(20) DEFAULT NULL,
    `username` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `full_name` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `description` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `website` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `followers` int(11) DEFAULT NULL,
    `following` int(11) DEFAULT NULL,
    `uploads` int(11) DEFAULT NULL,
    `added_date` datetime DEFAULT NULL,
    `last_check_date` datetime DEFAULT NULL,
    `last_successful_check_date` datetime DEFAULT NULL,
    `profile_picture_url` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `is_private` int(11) DEFAULT '0',
    `is_verified` int(11) DEFAULT '0',
    `average_engagement_rate` varchar(8) COLLATE utf8mb4_unicode_ci DEFAULT '0',
    `details` mediumtext COLLATE utf8mb4_unicode_ci,
    `is_demo` int(11) DEFAULT '0',
    `is_featured` int(11) DEFAULT '1',
    PRIMARY KEY (`id`),
    UNIQUE KEY `instagram_users_id_uindex` (`id`),
    UNIQUE KEY `instagram_users_pk` (`instagram_id`),
    UNIQUE KEY `username_2` (`username`),
    KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEPARATOR --

INSERT INTO `instagram_users` (`id`, `instagram_id`, `username`, `full_name`, `description`, `website`, `followers`, `following`, `uploads`, `added_date`, `last_check_date`, `profile_picture_url`, `is_private`, `is_verified`, `average_engagement_rate`, `details`, `is_demo`)
VALUES
  (1,'25945306','badgalriri','badgalriri','introducing @savagexfenty lingerie coming MAY.11.2018. sign up now!','http://ri-hanna.io/savagexfenty',62554084,1302,4293,'2018-05-05 18:33:53','2018-05-10 16:04:17','https://instagram.fsbz1-1.fna.fbcdn.net/vp/667a62925a82cf1445a7e800239ff35b/5B792186/t51.2885-19/11032926_1049846535031474_260957621_a.jpg',0,1,'3.64','{\"total_likes\":22640490,\"total_comments\":158523,\"average_comments\":\"15,852.30\",\"average_likes\":\"2,264,049.00\",\"top_hashtags\":{\"metgala2018\":1,\"heavenlybodies\":1,\"galliano\":1,\"margiella\":1,\"DAMN\":1},\"top_mentions\":{\"savagexfenty\":3,\"dennisleupold\":1},\"top_posts\":{\"BigJr94lkbM\":\"7.74\",\"Bie_F1EFqnw\":\"5.87\",\"BigMenillsS\":\"4.71\"}}',1),
  (2,'232192182','therock','therock','Gratitude. Mana.','http://www.nbc.com/titans',105741229,235,3548,'2018-05-07 12:35:07','2018-05-10 01:39:35','https://instagram.fsbz1-1.fna.fbcdn.net/vp/5eeaf27ff63f2135e91cc8f6501cc098/5B9AE1FC/t51.2885-19/11850309_1674349799447611_206178162_a.jpg',0,1,'1.25','{\"total_likes\":13185786,\"total_comments\":61606,\"average_comments\":\"6,160.60\",\"average_likes\":\"1,318,578.60\",\"top_hashtags\":{\"ProjectRock\":3,\"UnderArmour\":2,\"JungleCruise\":2,\"SKYSCRAPER\":2,\"HardestWorkerInTheRoom\":2,\"PaulGiamatti\":1,\"AllAboard\":1,\"NowGetTheFuckOffMe\":1,\"GetOnMyShoulders\":1,\"AndMyTattoos\":1,\"NobelPeacePrize\":1,\"GlobalGratitude\":1,\"DreamsAintJustForDreamers\":1,\"0\":1,\"GlobalEnterprise\":1},\"top_mentions\":{\"garciacompanies\":2,\"danygarciaco,\":1,\"underarmour\":1,\"kristenrandol\":1,\"kevinhart4real\":1,\"underarmour\\u2019s\":1},\"top_posts\":{\"Bic-vMLlRtW\":\"2.66\",\"Bia-qIKlh-9\":\"2.44\",\"Bigj_-bFPCZ\":\"2.29\"}}',1),
  (3,'13164944','g_eazy','G-Eazy','Endless Summer Tour Tix $20 For National Concert Week | Ends May 8th','http://bit.ly/NCW20GEazy',5959042,918,2992,'2018-05-07 12:35:36','2018-05-10 01:29:16','https://instagram.fsbz1-1.fna.fbcdn.net/vp/ad043770b7c57bec2ba5c6e132e7e430/5B7A9259/t51.2885-19/s150x150/23421504_1974793426097273_1903626335624888320_n.jpg',0,1,'5.98','{\"total_likes\":3530163,\"total_comments\":33618,\"average_comments\":\"3,361.80\",\"average_likes\":\"353,016.30\",\"top_hashtags\":{\"TheEndlessSummer\":2,\"TheBeautifulAndDamned\":1},\"top_mentions\":{\"livenation\":2,\"dkessler\":2,\"p_lo\":2,\"tristan_edouard\":2,\"liluzivert\":1,\"tydollasign\":1,\"ybnnahmir\":1,\"murdabeatz\":1},\"top_posts\":{\"BicoovInGXa\":\"11.62\",\"Bih3FuunIeE\":\"10.00\",\"Bijt0LrnSkE\":\"8.37\"}}',1);

-- SEPARATOR --

CREATE TABLE `email_reports` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `source` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT 'INSTAGRAM',
    `source_user_id` int(11) DEFAULT NULL,
    `user_id` int(11) DEFAULT NULL,
    `status` enum('SUCCESS','FAILED') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `date` datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `email_reports_id_uindex` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEPARATOR --

CREATE TABLE `pages` (
    `page_id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
    `url` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
    `description` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
    `position` int(11) NOT NULL,
    PRIMARY KEY (`page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEPARATOR --

INSERT INTO `pages` (`page_id`, `title`, `url`, `description`, `position`)
VALUES
  (1,'Terms of Service','terms-of-service','<p>&nbsp;</p>\r\n<p>Your terms of service go here..</p>',0),
  (2,'Frequently Asked Questions','faqs','<p>Here you can write your own FAQ for the website..</p>',1),
  (3,'About','about','<p>About..</p>',1);

-- SEPARATOR --

CREATE TABLE `payments` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `user_id` int(11) DEFAULT NULL,
    `type` enum('PAYPAL','STRIPE') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `payment_id` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `payer_id` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `email` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `name` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `amount` float DEFAULT NULL,
    `currency` varchar(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `date` datetime DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEPARATOR --

CREATE TABLE `settings` (
    `id` int(11) NOT NULL,
    `title` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
    `logo` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT '',
    `favicon` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT '',
    `time_zone` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'America/New_York',
    `meta_description` varchar(1024) COLLATE utf8mb4_unicode_ci NOT NULL,
    `meta_keywords` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `analytics_code` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
    `email_confirmation` int(11) NOT NULL DEFAULT '0',
    `recaptcha` int(11) NOT NULL DEFAULT '1',
    `public_key` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
    `private_key` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
    `facebook_login` int(11) NOT NULL,
    `facebook_app_id` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
    `facebook_app_secret` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
    `smtp_host` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
    `smtp_port` int(11) NOT NULL,
    `smtp_encryption` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT '0',
    `smtp_auth` int(11) DEFAULT '0',
    `smtp_user` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
    `smtp_pass` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
    `smtp_from` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
    `facebook` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
    `twitter` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
    `youtube` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `instagram` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `store_currency` varchar(12) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USD',
    `store_paypal_client_id` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
    `store_paypal_secret` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
    `store_paypal_mode` enum('sandbox','live') COLLATE utf8mb4_unicode_ci DEFAULT 'live',
    `store_stripe_publishable_key` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT '',
    `store_stripe_secret_key` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT '',
    `store_stripe_webhook_secret` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT '',
    `store_unlock_report_price` float DEFAULT '1',
    `store_unlock_report_time` int(11) DEFAULT '0',
    `store_no_ads_price` float NOT NULL DEFAULT '5',
    `store_user_default_points` int(11) DEFAULT '0',
    `report_ad` varchar(2560) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
    `index_ad` varchar(2560) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
    `account_sidebar_ad` varchar(2560) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `instagram_check_interval` int(11) DEFAULT '1',
    `instagram_minimum_followers` int(11) DEFAULT '5',
    `instagram_calculator_media_count` int(11) DEFAULT '10',
    `cron_queries` int(11) DEFAULT '1',
    `cron_mode` enum('ACTIVE','ALL') COLLATE utf8mb4_unicode_ci DEFAULT 'ACTIVE',
    `cron_auto_add_missing_logs` int(11) DEFAULT '0',
    `instagram_login` int(11) DEFAULT '0',
    `instagram_client_id` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `instagram_client_secret` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `activation_email_template` longtext COLLATE utf8mb4_unicode_ci,
    `lost_password_email_template` longtext COLLATE utf8mb4_unicode_ci,
    `credentials_email_template` longtext COLLATE utf8mb4_unicode_ci,
    `admin_email_notification_emails` mediumtext COLLATE utf8mb4_unicode_ci,
    `admin_new_user_email_notification` int(11) DEFAULT '0',
    `admin_new_payment_email_notification` int(11) DEFAULT '0',
    `directory` enum('ALL','LOGGED_IN','DISABLED') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `directory_pagination` int(11) DEFAULT '10',
    `proxy` int(11) DEFAULT '0',
    `proxy_exclusive` int(11) DEFAULT '0',
    `proxy_timeout` int(11) DEFAULT '15',
    `proxy_failed_requests_pause` int(11) DEFAULT '3',
    `proxy_pause_duration` int(11) DEFAULT '1440',
    `email_reports` int(11) DEFAULT '0',
    `email_reports_default` int(11) DEFAULT '0',
    `email_reports_frequency` enum('DAILY','WEEKLY','MONTHLY') COLLATE utf8mb4_unicode_ci DEFAULT 'WEEKLY',
    `email_reports_favorites` int(11) DEFAULT '0',
    `default_language` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT 'english',
    `facebook_check_interval` int(11) DEFAULT '24',
    `facebook_minimum_likes` int(11) DEFAULT '1000',
    `youtube_api_key` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `youtube_check_interval` int(11) DEFAULT '24',
    `youtube_minimum_subscribers` int(11) DEFAULT '100',
    `youtube_check_videos` int(11) DEFAULT '10',
    `twitter_consumer_key` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `twitter_secret_key` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `twitter_oauth_token` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `twitter_oauth_token_secret` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `twitter_check_interval` int(11) DEFAULT '24',
    `twitter_minimum_followers` int(11) DEFAULT '100',
    `twitter_check_tweets` int(11) DEFAULT '15',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEPARATOR --

INSERT INTO `settings` (`id`, `title`, `logo`, `favicon`, `time_zone`, `meta_description`, `meta_keywords`, `analytics_code`, `email_confirmation`, `recaptcha`, `public_key`, `private_key`, `facebook_login`, `facebook_app_id`, `facebook_app_secret`, `smtp_host`, `smtp_port`, `smtp_encryption`, `smtp_auth`, `smtp_user`, `smtp_pass`, `smtp_from`, `facebook`, `twitter`, `youtube`, `instagram`, `store_currency`, `store_paypal_client_id`, `store_paypal_secret`, `store_stripe_publishable_key`, `store_stripe_secret_key`, `store_unlock_report_price`, `store_unlock_report_time`, `store_no_ads_price`, `report_ad`, `index_ad`, `account_sidebar_ad`, `instagram_check_interval`, `instagram_minimum_followers`, `instagram_calculator_media_count`, `cron_queries`, `instagram_login`, `instagram_client_id`, `instagram_client_secret`) VALUES
  (1, 'phpAnalyzer.com', '', '9fa8a623783fd2d277c53e1d216068ce.ico', 'UTC', '', '', '', 0, 0, '', '', 0, '', '', '', 587, 'tls', 1, '', '', '', '', '', '', '', 'USD', '', '', '', '', 1, 5, 5, '', '', '', 1, 5, 10, 1, 0, '', '');

-- SEPARATOR --

UPDATE `settings` SET activation_email_template = '{"subject":"Welcome {{NAME}}! - Activation email","body":"Hey there {{NAME}},<br \/><br \/>We are glad you joined us! <br \/><br \/>One more step and you are ready,<br \/><br \/>you just need to click the following link in order to join {{WEBSITE_TITLE}}<br \/><br \/>{{ACTIVATION_LINK}}<br \/><br \/>Hope you have a great day!"}' WHERE id = 1;

-- SEPARATOR --

UPDATE `settings` SET credentials_email_template = '{"subject":"Welcome to {{WEBSITE_TITLE}} - Your credentials","body":"Hey there {{NAME}},<br \/><br \/>We are glad you joined us! <br \/><br \/>These are your new generated account details:<br \/><br \/>Username: {{ACCOUNT_USERNAME}}<br \/>Password: {{ACCOUNT_PASSWORD}}<br \/><br \/>And you can login here: {{WEBSITE_LINK}}<br \/><br \/>Hope you have a great day!"}' WHERE id = 1;

-- SEPARATOR --

UPDATE `settings` SET lost_password_email_template = '{"subject":"{{WEBSITE_TITLE}} - Reset your password","body":"Hey {{NAME}},<br \/><br \/>We know that you might lose your passwords, we are here to help!<br \/><br \/>This is your reset password link: {{LOST_PASSWORD_LINK}}<br \/><br \/>If you did not request this, you can ignore it!<br \/><br \/>All the best from {{WEBSITE_TITLE}}!"}' WHERE id = 1;

-- SEPARATOR --

CREATE TABLE `unlocked_reports` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) DEFAULT NULL,
    `source_user_id` int(11) DEFAULT NULL,
    `date` datetime DEFAULT NULL,
    `expiration_date` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT '0',
    `source` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT 'INSTAGRAM',
    PRIMARY KEY (`id`),
    UNIQUE KEY `unlocked_reports_id_uindex` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEPARATOR --

CREATE TABLE `users` (
    `user_id` int(11) NOT NULL AUTO_INCREMENT,
    `username` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
    `password` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
    `email` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
    `name` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
    `facebook_id` bigint(20) DEFAULT NULL,
    `token_code` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `email_activation_code` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT '',
    `lost_password_code` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT '',
    `type` int(11) NOT NULL DEFAULT '0',
    `active` int(11) NOT NULL DEFAULT '0',
    `date` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT '',
    `last_activity` datetime DEFAULT NULL,
    `points` float NOT NULL DEFAULT '15',
    `no_ads` int(11) DEFAULT '0',
    `instagram_id` bigint(11) DEFAULT NULL,
    `api_key` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `email_reports` int(11) DEFAULT '0',
    PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEPARATOR --

INSERT INTO `users` (`user_id`, `username`, `password`, `email`, `name`, `facebook_id`, `email_activation_code`, `lost_password_code`, `type`, `active`, `date`, `last_activity`, `points`, `no_ads`, `instagram_id`, `api_key`)
VALUES (1,'admin','$2y$10$VuA/EFBXGEz7BKynUZkg2eqLMR2xwmC3o94eR7hIPylfKOZB/T0nW','mail@mail.com','AmazCode',NULL,'','0',1,1,'',NOW(),0,1,NULL,'098f6bcd4621d373cade4e832627b4f6');

-- SEPARATOR --

CREATE TABLE `proxies` (
    `proxy_id` int(11) NOT NULL AUTO_INCREMENT,
    `address` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `port` int(11) DEFAULT NULL,
    `username` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `password` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `note` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `method` int(11) DEFAULT '0',
    `failed_requests` int(11) DEFAULT '0',
    `successful_requests` int(11) DEFAULT '0',
    `total_failed_requests` int(11) DEFAULT '0',
    `total_successful_requests` int(11) DEFAULT '0',
    `date` datetime DEFAULT NULL,
    `last_date` datetime DEFAULT NULL,
    PRIMARY KEY (`proxy_id`),
    UNIQUE KEY `proxies_id_uindex` (`proxy_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEPARATOR --

CREATE TABLE `twitter_logs` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `twitter_user_id` int(11) NOT NULL,
    `username` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
    `followers` int(11) NOT NULL,
    `following` int(11) NOT NULL,
    `tweets` int(11) NOT NULL,
    `likes` int(11) NOT NULL,
    `date` datetime NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `facebook_logs_id_uindex` (`id`),
    KEY `facebook_user_id` (`twitter_user_id`),
    KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEPARATOR --

CREATE TABLE `twitter_tweets` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `twitter_user_id` int(11) NOT NULL,
    `tweet_id` bigint(20) NOT NULL,
    `text` varchar(280) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
    `source` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `language` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `retweets` int(11) NOT NULL DEFAULT '0',
    `likes` int(11) NOT NULL DEFAULT '0',
    `details` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
    `created_date` datetime NOT NULL,
    `date` datetime NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `tweet_id` (`tweet_id`),
    KEY `twitter_user_id` (`twitter_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEPARATOR --

CREATE TABLE `twitter_users` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `twitter_id` bigint(20) NOT NULL,
    `username` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
    `full_name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
    `description` varchar(512) COLLATE utf8mb4_unicode_ci NULL,
    `website` varchar(256) COLLATE utf8mb4_unicode_ci NULL,
    `followers` int(11) NOT NULL,
    `following` int(11) NOT NULL,
    `tweets` int(11) NOT NULL,
    `likes` int(11) NOT NULL,
    `profile_picture_url` varchar(256) COLLATE utf8mb4_unicode_ci NULL,
    `is_private` tinyint(4) NOT NULL DEFAULT '0',
    `is_verified` tinyint(4) NOT NULL DEFAULT '0',
    `details` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
    `added_date` datetime NOT NULL,
    `last_check_date` datetime NOT NULL,
    `last_successful_check_date` datetime DEFAULT NULL,
    `is_demo` tinyint(4) NOT NULL DEFAULT '0',
    `is_featured` tinyint(4) NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    UNIQUE KEY `twitter_id_2` (`twitter_id`),
    UNIQUE KEY `username_2` (`username`),
    KEY `twitter_id` (`twitter_id`),
    KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEPARATOR --

CREATE TABLE `youtube_users` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `youtube_id` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `username` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `title` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `description` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `profile_picture_url` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `subscribers` bigint(20) DEFAULT NULL,
    `views` bigint(20) DEFAULT NULL,
    `videos` int(11) DEFAULT NULL,
    `uploads_playlist_id` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT '',
    `details` longtext COLLATE utf8mb4_unicode_ci,
    `added_date` datetime DEFAULT NULL,
    `last_check_date` datetime DEFAULT NULL,
    `last_successful_check_date` datetime DEFAULT NULL,
    `is_demo` int(11) DEFAULT '0',
    `is_private` int(11) DEFAULT '0',
    `is_featured` int(11) DEFAULT '0',
    PRIMARY KEY (`id`),
    UNIQUE KEY `youtube_users_id_uindex` (`id`),
    UNIQUE KEY `youtube_id` (`youtube_id`),
    KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEPARATOR --

CREATE TABLE `youtube_logs` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `youtube_user_id` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `youtube_id` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `subscribers` bigint(20) DEFAULT NULL,
    `views` bigint(20) DEFAULT NULL,
    `videos` int(11) DEFAULT NULL,
    `date` datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `youtube_logs_id_uindex` (`id`),
    KEY `youtube_user_id` (`youtube_user_id`),
    KEY `username` (`youtube_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEPARATOR --

CREATE TABLE `youtube_videos` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `youtube_user_id` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `video_id` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
    `title` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `description` text COLLATE utf8mb4_unicode_ci,
    `views` int(11) DEFAULT NULL,
    `likes` int(11) DEFAULT NULL,
    `dislikes` int(11) DEFAULT NULL,
    `comments` int(11) DEFAULT NULL,
    `thumbnail_url` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `details` longtext COLLATE utf8mb4_unicode_ci,
    `created_date` datetime DEFAULT NULL,
    `date` datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `youtube_videos_id_uindex` (`id`),
    UNIQUE KEY `video_id` (`video_id`),
    KEY `youtube_user_id` (`youtube_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEPARATOR --

CREATE TABLE `facebook_users` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `username` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `name` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `profile_picture_url` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `likes` int(11) DEFAULT NULL,
    `followers` int(11) DEFAULT NULL,
    `details` longtext COLLATE utf8mb4_unicode_ci,
    `is_verified` int(11) DEFAULT '0',
    `added_date` datetime DEFAULT NULL,
    `last_check_date` datetime DEFAULT NULL,
    `last_successful_check_date` datetime DEFAULT NULL,
    `is_demo` int(11) DEFAULT '0',
    `is_private` int(11) DEFAULT '0',
    `is_featured` int(11) DEFAULT '0',
    PRIMARY KEY (`id`),
    UNIQUE KEY `facebook_users_id_uindex` (`id`),
    UNIQUE KEY `facebook_users_pk` (`username`),
    KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEPARATOR --

CREATE TABLE `facebook_logs` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `facebook_user_id` int(11) DEFAULT NULL,
    `username` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `likes` int(11) DEFAULT NULL,
    `followers` int(11) DEFAULT NULL,
    `date` datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `facebook_logs_id_uindex` (`id`),
    KEY `facebook_user_id` (`facebook_user_id`),
    KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

