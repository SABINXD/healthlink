ALTER TABLE `posts` 
ADD COLUMN `post_title` TEXT NOT NULL AFTER `post_img`,
ADD COLUMN `post_category` VARCHAR(50) NOT NULL AFTER `post_title`,
ADD COLUMN `post_desc` TEXT NULL AFTER `post_category`;