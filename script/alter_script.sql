ALTER TABLE `user_details` MODIFY COLUMN `first_name` varchar(100);

ALTER TABLE `user_details` MODIFY COLUMN `last_name` varchar(100);

ALTER TABLE `user_details` MODIFY COLUMN `user_code` VARCHAR(25);

ALTER TABLE `user_details` MODIFY COLUMN `email` VARCHAR(100);

ALTER TABLE `customer_details` MODIFY COLUMN `s_first_name` VARCHAR(70);
ALTER TABLE `customer_details` MODIFY COLUMN `s_last_name` VARCHAR(70);

ALTER TABLE `customer_details` MODIFY COLUMN `b_first_name` VARCHAR(70);
ALTER TABLE `customer_details` MODIFY COLUMN `b_last_name` VARCHAR(70);

ALTER TABLE `customer_details` MODIFY COLUMN `s_country` VARCHAR(70);
ALTER TABLE `customer_details` MODIFY COLUMN `b_country` VARCHAR(70);


ALTER TABLE `user_details` ADD COLUMN `store_domain` text(400);
