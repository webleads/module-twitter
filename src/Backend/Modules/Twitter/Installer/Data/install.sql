CREATE TABLE IF NOT EXISTS `twitter_tweets` (
 `tweet_id` bigint(20) unsigned NOT NULL,
 `widget_id` int(11) NOT NULL,
 `text` varchar(255) DEFAULT NULL,
 `source` varchar(255) DEFAULT NULL,
 `truncated` enum('N','Y') NOT NULL DEFAULT 'N',
 `in_reply_to_status_id` bigint(20) unsigned DEFAULT NULL,
 `in_reply_to_user_id` bigint(20) unsigned DEFAULT NULL,
 `in_reply_to_screen_name` varchar(50) DEFAULT NULL,
 `created_on` datetime NOT NULL,
 PRIMARY KEY (`tweet_id`,`widget_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `twitter_users` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `twitter_id` bigint(20) unsigned DEFAULT NULL,
 `username` varchar(50) NOT NULL,
 `name` varchar(255) DEFAULT NULL,
 `location` varchar(255) DEFAULT NULL,
 `description` varchar(255) DEFAULT NULL,
 `profile_image_url` varchar(255) DEFAULT NULL,
 `url` varchar(255) DEFAULT NULL,
 `oauth_status` enum('approved','not_approved') NOT NULL DEFAULT 'not_approved',
 `oauth_token` varchar(255) DEFAULT NULL,
 `oauth_token_secret` varchar(255) DEFAULT NULL,
 PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `twitter_widgets` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `user_id` int(11) NOT NULL,
 `extra_id` int(11) NOT NULL,
 `tag` varchar(255) DEFAULT NULL,
 `number_of_items` int(3) NOT NULL DEFAULT '5',
 `last_synced_on` datetime DEFAULT NULL,
 PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
