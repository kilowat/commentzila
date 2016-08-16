CREATE TABLE `alienspro_commentzila` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `rang` int(11) DEFAULT 0,
  `element_id` int(11) DEFAULT NULL,
  `infoblock_id` int(11) NOT NULL,
  `infoblock_type` varchar(100) NOT NULL,
  `ip` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL,
  `site_id` varchar(100) NOT NULL, 
  `name` varchar(255) DEFAULT NULL,
  `msg` text DEFAULT NULL,
  `spam` boolean DEFAULT 0,
  `date_t` int(11) DEFAULT 0,
  `left_key` int(11) NOT NULL DEFAULT 0,
  `right_key` int(11) NOT NULL DEFAULT 0,
  `level` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  INDEX  `inx_keys` (`left_key`, `right_key`,`element_id`,`user_id`, `infoblock_id`,`infoblock_type`,`site_id`)
 
);
CREATE TABLE `alienspro_commentzila_comment_like` (
  `user_id` int(11) DEFAULT NULL,
  `comment_id` int(11) DEFAULT NULL,
  INDEX  `inx_keys` (`comment_id`)
);