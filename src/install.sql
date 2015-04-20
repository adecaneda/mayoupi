
-- images table
CREATE TABLE `images` (
	`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`id_user` INT(11) UNSIGNED NOT NULL,
	`url` VARCHAR(64) NOT NULL,
	`type` VARCHAR(16) NOT NULL,
	PRIMARY KEY (`id`),
	INDEX `User` (`id_user`),
	CONSTRAINT `FK_images_users` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1;


-- users table
CREATE TABLE `users` (
	`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Primary key',
	`google_id` VARCHAR(128) NULL DEFAULT NULL COMMENT 'Google Plus identifier',
	`instagram_id` VARCHAR(128) NULL DEFAULT NULL COMMENT 'Instagram identifier',
	`id_image` INT(11) UNSIGNED NULL DEFAULT NULL COMMENT 'Avatar',
	`name` VARCHAR(50) NOT NULL,
	`email` VARCHAR(50) NOT NULL,
	`password` VARCHAR(128) NOT NULL COMMENT 'Encrypted password',
	`role` VARCHAR(16) NOT NULL,
	`tac_accepted` TINYINT(4) NOT NULL COMMENT 'Terms and Conditions accepted?',
	`last_sign_in` DATETIME NULL DEFAULT NULL COMMENT 'Last time the user signed in',
	`last_image_upload` DATETIME NULL DEFAULT NULL COMMENT 'Last time the user upload a profile image',
	PRIMARY KEY (`id`),
	UNIQUE INDEX `Email` (`email`),
	UNIQUE INDEX `Facebook` (`google_id`),
	UNIQUE INDEX `Instagram` (`instagram_id`),
	INDEX `FK_users_images` (`id_image`),
	CONSTRAINT `FK_users_images` FOREIGN KEY (`id_image`) REFERENCES `images` (`id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1;


-- data for table images
INSERT INTO `images` (`id`, `id_user`, `url`, `type`) VALUES (1, 1, 'avatar_1.jpg', 'avatar');

-- data for table users
INSERT INTO `users` (`id`, `google_id`, `instagram_id`, `id_image`, `name`, `email`, `password`, `role`, `tac_accepted`, `last_sign_in`, `last_image_upload`) VALUES (1, NULL, NULL, 1, 'Alejandro', 'alejandro@mail.com', '912ec803b2ce49e4a541068d495ab570', 'admin', 0, NULL, NULL);
INSERT INTO `users` (`id`, `google_id`, `instagram_id`, `id_image`, `name`, `email`, `password`, `role`, `tac_accepted`, `last_sign_in`, `last_image_upload`) VALUES (19, NULL, NULL, NULL, 'Kevin Johansen', 'kevin@mail.com', '912ec803b2ce49e4a541068d495ab570', 'user', 0, NULL, NULL);
INSERT INTO `users` (`id`, `google_id`, `instagram_id`, `id_image`, `name`, `email`, `password`, `role`, `tac_accepted`, `last_sign_in`, `last_image_upload`) VALUES (20, NULL, NULL, NULL, 'pepe', 'ale@mail.com', '', 'user', 1, '0000-00-00 00:00:00', NULL);
INSERT INTO `users` (`id`, `google_id`, `instagram_id`, `id_image`, `name`, `email`, `password`, `role`, `tac_accepted`, `last_sign_in`, `last_image_upload`) VALUES (21, NULL, NULL, NULL, 'pepe', 'alejo@mail.com', '', 'user', 1, '0000-00-00 00:00:00', NULL);
INSERT INTO `users` (`id`, `google_id`, `instagram_id`, `id_image`, `name`, `email`, `password`, `role`, `tac_accepted`, `last_sign_in`, `last_image_upload`) VALUES (22, NULL, NULL, NULL, 'pepe', 'alejo2@mail.com', '912ec803b2ce49e4a541068d495ab570', 'user', 1, '2015-04-19 11:27:43', NULL);
INSERT INTO `users` (`id`, `google_id`, `instagram_id`, `id_image`, `name`, `email`, `password`, `role`, `tac_accepted`, `last_sign_in`, `last_image_upload`) VALUES (23, NULL, NULL, NULL, 'pepito', 'pepito@mail.com', '912ec803b2ce49e4a541068d495ab570', 'user', 0, '2015-04-19 23:36:03', NULL);
INSERT INTO `users` (`id`, `google_id`, `instagram_id`, `id_image`, `name`, `email`, `password`, `role`, `tac_accepted`, `last_sign_in`, `last_image_upload`) VALUES (24, NULL, NULL, NULL, 'andres', 'andres@mail.com', '912ec803b2ce49e4a541068d495ab570', 'user', 1, '2015-04-19 23:42:44', NULL);


