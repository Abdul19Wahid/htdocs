SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (`id` INT PRIMARY KEY AUTO_INCREMENT,`name` VARCHAR(255) NOT NULL,`email` VARCHAR(255) NOT NULL,`email_verified_at` DATETIME,`password` VARCHAR(255) NOT NULL,`phone` VARCHAR(255),`role` VARCHAR(255) NOT NULL DEFAULT 'user',`currency` VARCHAR(255) NOT NULL DEFAULT 'GHS',`language` VARCHAR(255) NOT NULL DEFAULT 'en',`theme` VARCHAR(255) NOT NULL DEFAULT 'light',`remember_token` VARCHAR(255),`created_at` DATETIME,`updated_at` DATETIME) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `users` (`id`,`name`,`email`,`email_verified_at`,`password`,`phone`,`role`,`currency`,`language`,`theme`,`remember_token`,`created_at`,`updated_at`) VALUES ('1','Test User','test@example.com','2026-08-08 03:02:05','$2y$12$S8JYQo4oifjc7Ch8Rv5ccuJbbOHYxCABkdqgd/Qiwyccde816FU7a',NULL,'user','GHS','en','light','TJz5LD75gz','2026-08-08 03:02:05','2026-08-08 03:02:05');
INSERT INTO `users` (`id`,`name`,`email`,`email_verified_at`,`password`,`phone`,`role`,`currency`,`language`,`theme`,`remember_token`,`created_at`,`updated_at`) VALUES ('2','Hardi Abdul-Wahid','hardiabdulwahid19@gmail.com',NULL,'$2y$12$h1x04RaBLr2zb.pUWIe/GeISbXg8tVz5N4NuHoIszaWB5Wsra6clu',NULL,'user','GHS','en','light',NULL,'2026-08-08 03:05:15','2026-08-08 03:05:15');

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens` (`email` VARCHAR(255) PRIMARY KEY,`token` VARCHAR(255) NOT NULL,`created_at` DATETIME) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (`id` VARCHAR(255) PRIMARY KEY,`user_id` INT,`ip_address` VARCHAR(255),`user_agent` TEXT,`payload` TEXT NOT NULL,`last_activity` INT NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `sessions` (`id`,`user_id`,`ip_address`,`user_agent`,`payload`,`last_activity`) VALUES ('1rkmrOOv4DcgI6eyaafYFEIumtB0aiBqDdejJitp','2','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoibm9tcTUyem9MU2doejh6ZHRVVjJoelZFS1ZrclhNbWsycEdLbWR0diI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kYXNoYm9hcmQiO3M6NToicm91dGUiO3M6OToiZGFzaGJvYXJkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Mjt9','1786160225');
INSERT INTO `sessions` (`id`,`user_id`,`ip_address`,`user_agent`,`payload`,`last_activity`) VALUES ('kqZtFAXIVAYZO3RzM8xLxczL9v9UczgorH7c7z5j','2','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','YTo1OntzOjY6Il90b2tlbiI7czo0MDoibFU5WWx5aWhWZmdvbmZidlFjNnhBaUhQQ0dpSmtNNW5COXlOelYzZyI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjQwOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvcmVwb3J0cy9leHBvcnQvY3N2IjtzOjU6InJvdXRlIjtzOjExOiJyZXBvcnRzLmNzdiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjI7fQ==','1786332368');
INSERT INTO `sessions` (`id`,`user_id`,`ip_address`,`user_agent`,`payload`,`last_activity`) VALUES ('8rcU1WkC2KRqee0lZ3J7HZ2inFXbmq1YsUbHyiD9',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoia0tPS1Z2Z2ExbW9hZnlZNTNmd2FaUVAzeFNmUnFmbkNPWFJMSDlJOCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyOToiaHR0cDovLzEyNy4wLjAuMTo4MDAwL3Byb2ZpbGUiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czoyNzoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==','1786413791');
INSERT INTO `sessions` (`id`,`user_id`,`ip_address`,`user_agent`,`payload`,`last_activity`) VALUES ('QYcZskhBTIrUOtMtWI7eApiofQzTrQfTyio37FzT','2','172.20.10.1','Mozilla/5.0 (iPhone; CPU iPhone OS 26_5_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/150.0.7871.51 Mobile/15E148 Safari/604.1','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiN3pmVkJTSkphN0FMQmRKWXNNZm1mN0gyWW5uZUZLdWpjdkFpeTNtYiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzM6Imh0dHA6Ly8xNzIuMjAuMTAuMzo4MDAwL2Rhc2hib2FyZCI7czo1OiJyb3V0ZSI7czo5OiJkYXNoYm9hcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyO30=','1786413854');

DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache` (`key` VARCHAR(255) PRIMARY KEY,`value` TEXT NOT NULL,`expiration` INT NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks` (`key` VARCHAR(255) PRIMARY KEY,`owner` VARCHAR(255) NOT NULL,`expiration` INT NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (`id` INT PRIMARY KEY AUTO_INCREMENT,`queue` VARCHAR(255) NOT NULL,`payload` TEXT NOT NULL,`attempts` INT NOT NULL,`reserved_at` INT,`available_at` INT NOT NULL,`created_at` INT NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE `job_batches` (`id` VARCHAR(255) PRIMARY KEY,`name` VARCHAR(255) NOT NULL,`total_jobs` INT NOT NULL,`pending_jobs` INT NOT NULL,`failed_jobs` INT NOT NULL,`failed_job_ids` TEXT NOT NULL,`options` TEXT,`cancelled_at` INT,`created_at` INT NOT NULL,`finished_at` INT) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (`id` INT PRIMARY KEY AUTO_INCREMENT,`uuid` VARCHAR(255) NOT NULL,`connection` TEXT NOT NULL,`queue` TEXT NOT NULL,`payload` TEXT NOT NULL,`exception` TEXT NOT NULL,`failed_at` DATETIME NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (`id` INT PRIMARY KEY AUTO_INCREMENT,`user_id` INT NOT NULL,`name` VARCHAR(255) NOT NULL,`icon` VARCHAR(255),`color` VARCHAR(255),`description` TEXT,`is_default` VARCHAR(255) NOT NULL DEFAULT '0',`created_at` DATETIME,`updated_at` DATETIME) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `categories` (`id`,`user_id`,`name`,`icon`,`color`,`description`,`is_default`,`created_at`,`updated_at`) VALUES ('1','1','Food',NULL,NULL,NULL,'1','2026-08-08 03:02:05','2026-08-08 03:02:05');
INSERT INTO `categories` (`id`,`user_id`,`name`,`icon`,`color`,`description`,`is_default`,`created_at`,`updated_at`) VALUES ('2','1','Transport',NULL,NULL,NULL,'1','2026-08-08 03:02:05','2026-08-08 03:02:05');
INSERT INTO `categories` (`id`,`user_id`,`name`,`icon`,`color`,`description`,`is_default`,`created_at`,`updated_at`) VALUES ('3','1','Shopping',NULL,NULL,NULL,'1','2026-08-08 03:02:05','2026-08-08 03:02:05');
INSERT INTO `categories` (`id`,`user_id`,`name`,`icon`,`color`,`description`,`is_default`,`created_at`,`updated_at`) VALUES ('4','1','Bills',NULL,NULL,NULL,'1','2026-08-08 03:02:05','2026-08-08 03:02:05');
INSERT INTO `categories` (`id`,`user_id`,`name`,`icon`,`color`,`description`,`is_default`,`created_at`,`updated_at`) VALUES ('5','1','Utilities',NULL,NULL,NULL,'1','2026-08-08 03:02:05','2026-08-08 03:02:05');
INSERT INTO `categories` (`id`,`user_id`,`name`,`icon`,`color`,`description`,`is_default`,`created_at`,`updated_at`) VALUES ('6','1','Entertainment',NULL,NULL,NULL,'1','2026-08-08 03:02:05','2026-08-08 03:02:05');
INSERT INTO `categories` (`id`,`user_id`,`name`,`icon`,`color`,`description`,`is_default`,`created_at`,`updated_at`) VALUES ('7','1','Education',NULL,NULL,NULL,'1','2026-08-08 03:02:05','2026-08-08 03:02:05');
INSERT INTO `categories` (`id`,`user_id`,`name`,`icon`,`color`,`description`,`is_default`,`created_at`,`updated_at`) VALUES ('8','1','Health',NULL,NULL,NULL,'1','2026-08-08 03:02:05','2026-08-08 03:02:05');
INSERT INTO `categories` (`id`,`user_id`,`name`,`icon`,`color`,`description`,`is_default`,`created_at`,`updated_at`) VALUES ('9','1','Fuel',NULL,NULL,NULL,'1','2026-08-08 03:02:05','2026-08-08 03:02:05');
INSERT INTO `categories` (`id`,`user_id`,`name`,`icon`,`color`,`description`,`is_default`,`created_at`,`updated_at`) VALUES ('10','1','Rent',NULL,NULL,NULL,'1','2026-08-08 03:02:05','2026-08-08 03:02:05');
INSERT INTO `categories` (`id`,`user_id`,`name`,`icon`,`color`,`description`,`is_default`,`created_at`,`updated_at`) VALUES ('11','1','Savings',NULL,NULL,NULL,'1','2026-08-08 03:02:05','2026-08-08 03:02:05');
INSERT INTO `categories` (`id`,`user_id`,`name`,`icon`,`color`,`description`,`is_default`,`created_at`,`updated_at`) VALUES ('12','1','Investment',NULL,NULL,NULL,'1','2026-08-08 03:02:05','2026-08-08 03:02:05');
INSERT INTO `categories` (`id`,`user_id`,`name`,`icon`,`color`,`description`,`is_default`,`created_at`,`updated_at`) VALUES ('13','1','Miscellaneous',NULL,NULL,NULL,'1','2026-08-08 03:02:05','2026-08-08 03:02:05');

DROP TABLE IF EXISTS `payment_methods`;
CREATE TABLE `payment_methods` (`id` INT PRIMARY KEY AUTO_INCREMENT,`user_id` INT NOT NULL,`name` VARCHAR(255) NOT NULL,`icon` VARCHAR(255),`is_default` VARCHAR(255) NOT NULL DEFAULT '0',`created_at` DATETIME,`updated_at` DATETIME) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `payment_methods` (`id`,`user_id`,`name`,`icon`,`is_default`,`created_at`,`updated_at`) VALUES ('1','1','Cash',NULL,'0','2026-08-08 03:02:05','2026-08-08 03:02:05');
INSERT INTO `payment_methods` (`id`,`user_id`,`name`,`icon`,`is_default`,`created_at`,`updated_at`) VALUES ('2','1','Mobile Money',NULL,'0','2026-08-08 03:02:05','2026-08-08 03:02:05');
INSERT INTO `payment_methods` (`id`,`user_id`,`name`,`icon`,`is_default`,`created_at`,`updated_at`) VALUES ('3','1','Debit Card',NULL,'0','2026-08-08 03:02:05','2026-08-08 03:02:05');
INSERT INTO `payment_methods` (`id`,`user_id`,`name`,`icon`,`is_default`,`created_at`,`updated_at`) VALUES ('4','1','Credit Card',NULL,'0','2026-08-08 03:02:05','2026-08-08 03:02:05');
INSERT INTO `payment_methods` (`id`,`user_id`,`name`,`icon`,`is_default`,`created_at`,`updated_at`) VALUES ('5','1','Bank Transfer',NULL,'0','2026-08-08 03:02:05','2026-08-08 03:02:05');
INSERT INTO `payment_methods` (`id`,`user_id`,`name`,`icon`,`is_default`,`created_at`,`updated_at`) VALUES ('6','1','Other',NULL,'0','2026-08-08 03:02:05','2026-08-08 03:02:05');

DROP TABLE IF EXISTS `income_sources`;
CREATE TABLE `income_sources` (`id` INT PRIMARY KEY AUTO_INCREMENT,`user_id` INT NOT NULL,`name` VARCHAR(255) NOT NULL,`description` TEXT,`is_default` VARCHAR(255) NOT NULL DEFAULT '0',`created_at` DATETIME,`updated_at` DATETIME) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `income_sources` (`id`,`user_id`,`name`,`description`,`is_default`,`created_at`,`updated_at`) VALUES ('1','1','Salary',NULL,'1','2026-08-08 03:02:05','2026-08-08 03:02:05');
INSERT INTO `income_sources` (`id`,`user_id`,`name`,`description`,`is_default`,`created_at`,`updated_at`) VALUES ('2','1','Business',NULL,'1','2026-08-08 03:02:05','2026-08-08 03:02:05');
INSERT INTO `income_sources` (`id`,`user_id`,`name`,`description`,`is_default`,`created_at`,`updated_at`) VALUES ('3','1','Freelance',NULL,'1','2026-08-08 03:02:05','2026-08-08 03:02:05');
INSERT INTO `income_sources` (`id`,`user_id`,`name`,`description`,`is_default`,`created_at`,`updated_at`) VALUES ('4','1','Investment',NULL,'1','2026-08-08 03:02:05','2026-08-08 03:02:05');
INSERT INTO `income_sources` (`id`,`user_id`,`name`,`description`,`is_default`,`created_at`,`updated_at`) VALUES ('5','1','Gift',NULL,'1','2026-08-08 03:02:05','2026-08-08 03:02:05');
INSERT INTO `income_sources` (`id`,`user_id`,`name`,`description`,`is_default`,`created_at`,`updated_at`) VALUES ('6','1','Bonus',NULL,'1','2026-08-08 03:02:05','2026-08-08 03:02:05');
INSERT INTO `income_sources` (`id`,`user_id`,`name`,`description`,`is_default`,`created_at`,`updated_at`) VALUES ('7','1','Scholarship',NULL,'1','2026-08-08 03:02:05','2026-08-08 03:02:05');
INSERT INTO `income_sources` (`id`,`user_id`,`name`,`description`,`is_default`,`created_at`,`updated_at`) VALUES ('8','1','Other',NULL,'1','2026-08-08 03:02:05','2026-08-08 03:02:05');

DROP TABLE IF EXISTS `incomes`;
CREATE TABLE `incomes` (`id` INT PRIMARY KEY AUTO_INCREMENT,`user_id` INT NOT NULL,`income_source_id` INT,`payment_method_id` INT,`amount` DECIMAL(15,2) NOT NULL,`description` VARCHAR(255),`date` DATE NOT NULL,`notes` TEXT,`created_at` DATETIME,`updated_at` DATETIME) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `incomes` (`id`,`user_id`,`income_source_id`,`payment_method_id`,`amount`,`description`,`date`,`notes`,`created_at`,`updated_at`) VALUES ('1','2',NULL,NULL,'5000','salary','2026-08-10 00:00:00',NULL,'2026-08-10 03:23:50','2026-08-10 03:23:50');

DROP TABLE IF EXISTS `expenses`;
CREATE TABLE `expenses` (`id` INT PRIMARY KEY AUTO_INCREMENT,`user_id` INT NOT NULL,`category_id` INT,`payment_method_id` INT,`amount` DECIMAL(15,2) NOT NULL,`description` VARCHAR(255),`date` DATE NOT NULL,`receipt` VARCHAR(255),`notes` TEXT,`created_at` DATETIME,`updated_at` DATETIME) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `expenses` (`id`,`user_id`,`category_id`,`payment_method_id`,`amount`,`description`,`date`,`receipt`,`notes`,`created_at`,`updated_at`) VALUES ('1','2',NULL,NULL,'50',NULL,'2026-08-10 00:00:00',NULL,NULL,'2026-08-10 03:25:21','2026-08-10 03:25:21');

DROP TABLE IF EXISTS `budgets`;
CREATE TABLE `budgets` (`id` INT PRIMARY KEY AUTO_INCREMENT,`user_id` INT NOT NULL,`category_id` INT,`name` VARCHAR(255) NOT NULL,`amount` DECIMAL(15,2) NOT NULL,`start_date` DATE NOT NULL,`end_date` DATE NOT NULL,`alert_percentage` INT NOT NULL DEFAULT '90',`created_at` DATETIME,`updated_at` DATETIME) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `savings_goals`;
CREATE TABLE `savings_goals` (`id` INT PRIMARY KEY AUTO_INCREMENT,`user_id` INT NOT NULL,`name` VARCHAR(255) NOT NULL,`target_amount` DECIMAL(15,2) NOT NULL,`current_amount` DECIMAL(15,2) NOT NULL DEFAULT '0',`target_date` DATE,`description` TEXT,`status` VARCHAR(255) NOT NULL DEFAULT 'active',`created_at` DATETIME,`updated_at` DATETIME) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `recurring_transactions`;
CREATE TABLE `recurring_transactions` (`id` INT PRIMARY KEY AUTO_INCREMENT,`user_id` INT NOT NULL,`type` VARCHAR(255) NOT NULL,`amount` DECIMAL(15,2) NOT NULL,`description` VARCHAR(255),`category_id` INT,`income_source_id` INT,`payment_method_id` INT,`frequency` VARCHAR(255) NOT NULL,`start_date` DATE NOT NULL,`next_due_date` DATE NOT NULL,`end_date` DATE,`is_active` VARCHAR(255) NOT NULL DEFAULT '1',`created_at` DATETIME,`updated_at` DATETIME) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (`id` INT PRIMARY KEY AUTO_INCREMENT,`user_id` INT NOT NULL,`type` VARCHAR(255) NOT NULL,`title` VARCHAR(255) NOT NULL,`message` TEXT NOT NULL,`action_url` VARCHAR(255),`is_read` VARCHAR(255) NOT NULL DEFAULT '0',`read_at` DATETIME,`created_at` DATETIME,`updated_at` DATETIME) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `activity_logs`;
CREATE TABLE `activity_logs` (`id` INT PRIMARY KEY AUTO_INCREMENT,`user_id` INT,`action` VARCHAR(255) NOT NULL,`module` VARCHAR(255),`record_id` INT,`description` TEXT,`ip_address` VARCHAR(255),`user_agent` TEXT,`created_at` DATETIME,`updated_at` DATETIME) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`record_id`,`description`,`ip_address`,`user_agent`,`created_at`,`updated_at`) VALUES ('1','2','created','income',NULL,'Recorded income of 5000','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','2026-08-10 03:23:50','2026-08-10 03:23:50');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`record_id`,`description`,`ip_address`,`user_agent`,`created_at`,`updated_at`) VALUES ('2','2','created','expense','1','Recorded expense of 50','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','2026-08-10 03:25:21','2026-08-10 03:25:21');

DROP TABLE IF EXISTS `attachments`;
CREATE TABLE `attachments` (`id` INT PRIMARY KEY AUTO_INCREMENT,`user_id` INT NOT NULL,`attachable_type` VARCHAR(255),`attachable_id` INT,`file_name` VARCHAR(255) NOT NULL,`file_path` VARCHAR(255) NOT NULL,`file_type` VARCHAR(255),`file_size` INT,`created_at` DATETIME,`updated_at` DATETIME) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (`id` INT PRIMARY KEY AUTO_INCREMENT,`user_id` INT NOT NULL,`key` VARCHAR(255) NOT NULL,`value` TEXT,`created_at` DATETIME,`updated_at` DATETIME) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


SET FOREIGN_KEY_CHECKS=1;
