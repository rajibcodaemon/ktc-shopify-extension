ALTER TABLE `user_details` ADD COLUMN `store_domain` text(400);
ALTER TABLE `customer_details` MODIFY COLUMN `s_country` VARCHAR(70);
ALTER TABLE `customer_details` MODIFY COLUMN `b_country` VARCHAR(70);