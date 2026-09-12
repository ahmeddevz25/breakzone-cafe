/*
SQLyog Community Edition- MySQL GUI v6.14
MySQL - 8.0.42 : Database - dbPortal
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

create database if not exists `dbPortal`;

USE `dbPortal`;

/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

/*Table structure for table `tbl_cafe_admin_log` */

DROP TABLE IF EXISTS `tbl_cafe_admin_log`;

CREATE TABLE `tbl_cafe_admin_log` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `admin_id` int DEFAULT NULL,
  `section` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `old_data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `new_data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `browser` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `date_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `Admin` (`admin_id`),
  KEY `Section` (`section`),
  KEY `Action` (`action`)
) ENGINE=InnoDB AUTO_INCREMENT=9388 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `tbl_cafe_admin_pages` */

DROP TABLE IF EXISTS `tbl_cafe_admin_pages`;

CREATE TABLE `tbl_cafe_admin_pages` (
  `id` int NOT NULL,
  `module` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `section` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `files` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `position` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `Module` (`module`),
  KEY `Section` (`section`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

/*Table structure for table `tbl_cafe_admin_rights` */

DROP TABLE IF EXISTS `tbl_cafe_admin_rights`;

CREATE TABLE `tbl_cafe_admin_rights` (
  `admin_id` bigint NOT NULL DEFAULT '0',
  `page_id` int NOT NULL DEFAULT '0',
  `view` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `add` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `edit` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `delete` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `export` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'Y',
  PRIMARY KEY (`admin_id`,`page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

/*Table structure for table `tbl_cafe_admin_type_rights` */

DROP TABLE IF EXISTS `tbl_cafe_admin_type_rights`;

CREATE TABLE `tbl_cafe_admin_type_rights` (
  `type_id` tinyint NOT NULL DEFAULT '0',
  `page_id` int NOT NULL DEFAULT '0',
  `view` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `add` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `edit` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `delete` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `export` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'Y',
  PRIMARY KEY (`type_id`,`page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

/*Table structure for table `tbl_cafe_admin_types` */

DROP TABLE IF EXISTS `tbl_cafe_admin_types`;

CREATE TABLE `tbl_cafe_admin_types` (
  `id` tinyint NOT NULL DEFAULT '0',
  `title` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `status` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `created_by` int NOT NULL,
  `modified_at` datetime NOT NULL,
  `modified_by` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

/*Table structure for table `tbl_cafe_admins` */

DROP TABLE IF EXISTS `tbl_cafe_admins`;

CREATE TABLE `tbl_cafe_admins` (
  `id` int NOT NULL,
  `admin_id` int NOT NULL,
  `type_id` tinyint DEFAULT NULL,
  `name` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `mobile` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '0300-0000000',
  `email` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `dashboards` tinytext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `stores` varchar(300) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `schools` tinytext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `misc_rights` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `level` tinyint DEFAULT '0',
  `records` tinyint DEFAULT '50',
  `theme` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `picture` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `admins` varchar(300) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `last_passwords` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT ',,',
  `last_password_change` datetime NOT NULL,
  `password_reset_time` datetime DEFAULT NULL,
  `password_reset_ip_address` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `device_id` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `status` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by` int NOT NULL DEFAULT '1',
  `modified_at` datetime NOT NULL,
  `modified_by` int NOT NULL DEFAULT '0',
  `printer_ip` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '202.69.38.246',
  `printer_port` char(5) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '9100',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

/*Table structure for table `tbl_cafe_brands` */

DROP TABLE IF EXISTS `tbl_cafe_brands`;

CREATE TABLE `tbl_cafe_brands` (
  `id` int NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position` int DEFAULT NULL,
  `status` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `Status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `tbl_cafe_card_topups` */

DROP TABLE IF EXISTS `tbl_cafe_card_topups`;

CREATE TABLE `tbl_cafe_card_topups` (
  `id` int NOT NULL AUTO_INCREMENT,
  `card_id` bigint NOT NULL,
  `amount` int DEFAULT NULL,
  `date_time` datetime NOT NULL,
  `admin_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `card_id_amount` (`card_id`,`amount`)
) ENGINE=InnoDB AUTO_INCREMENT=2904 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_cafe_cards` */

DROP TABLE IF EXISTS `tbl_cafe_cards`;

CREATE TABLE `tbl_cafe_cards` (
  `id` int NOT NULL,
  `old_card_id` int NOT NULL,
  `code` varchar(100) NOT NULL,
  `type` char(1) NOT NULL,
  `school_id` int DEFAULT NULL,
  `session_id` int DEFAULT NULL,
  `class_id` int DEFAULT NULL,
  `student_session_id` bigint DEFAULT NULL,
  `employee_id` bigint DEFAULT NULL,
  `department_id` int DEFAULT NULL,
  `other_name` varchar(255) DEFAULT NULL,
  `other_mobile` varchar(15) DEFAULT NULL,
  `other_details` text,
  `amount` float NOT NULL,
  `transferd_amount` float NOT NULL,
  `old_card_amount` int NOT NULL,
  `card_fee` int NOT NULL,
  `refund_amount` int NOT NULL,
  `status` char(1) NOT NULL,
  `created_at` datetime NOT NULL,
  `created_by` int NOT NULL,
  `modified_at` datetime NOT NULL,
  `modified_by` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `school_id_id` (`school_id`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_cafe_cash_categories` */

DROP TABLE IF EXISTS `tbl_cafe_cash_categories`;

CREATE TABLE `tbl_cafe_cash_categories` (
  `id` int NOT NULL,
  `parent_id` int DEFAULT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'B',
  `position` int DEFAULT NULL,
  `status` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `Parent` (`parent_id`),
  KEY `Status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `tbl_cafe_cash_payments` */

DROP TABLE IF EXISTS `tbl_cafe_cash_payments`;

CREATE TABLE `tbl_cafe_cash_payments` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `category_id` int NOT NULL,
  `type` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` bigint DEFAULT NULL,
  `date` date DEFAULT NULL,
  `mode` char(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_account_id` int DEFAULT '0',
  `cheque_no` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `cheque_date` date DEFAULT NULL,
  `cheque_status` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `realize_date` date DEFAULT NULL,
  `person` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comments` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `invoice` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `modified_by` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `Type` (`type`),
  KEY `Mode` (`mode`),
  KEY `BankAccount` (`bank_account_id`),
  KEY `Date` (`date`),
  KEY `Status` (`status`),
  KEY `SchoolTypeMode` (`mode`,`type`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `tbl_cafe_categories` */

DROP TABLE IF EXISTS `tbl_cafe_categories`;

CREATE TABLE `tbl_cafe_categories` (
  `id` int NOT NULL,
  `parent_id` bigint NOT NULL,
  `category` varchar(100) NOT NULL,
  `status` char(1) NOT NULL,
  `position` int NOT NULL,
  `date_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_cafe_combo_product_details` */

DROP TABLE IF EXISTS `tbl_cafe_combo_product_details`;

CREATE TABLE `tbl_cafe_combo_product_details` (
  `id` int NOT NULL,
  `combo_product_id` int NOT NULL,
  `item_id` int NOT NULL,
  `food_id` int DEFAULT NULL,
  `quantity` float NOT NULL,
  `discount` float DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `tbl_cafe_combo_products` */

DROP TABLE IF EXISTS `tbl_cafe_combo_products`;

CREATE TABLE `tbl_cafe_combo_products` (
  `id` bigint NOT NULL,
  `name` varchar(250) DEFAULT NULL,
  `code` varchar(50) NOT NULL,
  `price` float DEFAULT NULL,
  `picture` varchar(250) NOT NULL,
  `status` char(1) NOT NULL,
  `position` int NOT NULL,
  `created_at` datetime NOT NULL,
  `created_by` int NOT NULL,
  `modified_at` datetime DEFAULT NULL,
  `modified_by` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_cafe_disposal_food_details` */

DROP TABLE IF EXISTS `tbl_cafe_disposal_food_details`;

CREATE TABLE `tbl_cafe_disposal_food_details` (
  `id` int NOT NULL AUTO_INCREMENT,
  `disposal_id` int NOT NULL,
  `food_id` int NOT NULL,
  `ingredient_id` int NOT NULL,
  `quantity` float NOT NULL,
  `price` float NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=293 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_cafe_disposal_item_details` */

DROP TABLE IF EXISTS `tbl_cafe_disposal_item_details`;

CREATE TABLE `tbl_cafe_disposal_item_details` (
  `id` bigint NOT NULL,
  `disposal_id` int NOT NULL,
  `item_id` int NOT NULL,
  `ingredient_id` int DEFAULT NULL,
  `food_id` int NOT NULL,
  `quantity` float NOT NULL,
  `price` float NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_cafe_disposal_items` */

DROP TABLE IF EXISTS `tbl_cafe_disposal_items`;

CREATE TABLE `tbl_cafe_disposal_items` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `store_id` int DEFAULT NULL,
  `disposal_no` varchar(250) DEFAULT NULL,
  `total` float NOT NULL,
  `remarks` text NOT NULL,
  `disposal_date` date NOT NULL,
  `verified` char(1) NOT NULL DEFAULT 'N',
  `created_at` datetime NOT NULL,
  `created_by` int NOT NULL,
  `modified_at` datetime NOT NULL,
  `modified_by` int NOT NULL,
  `verified_at` datetime NOT NULL,
  `verified_by` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=165 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_cafe_email_templates` */

DROP TABLE IF EXISTS `tbl_cafe_email_templates`;

CREATE TABLE `tbl_cafe_email_templates` (
  `id` int NOT NULL,
  `title` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `subject` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `message` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `variables` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `default_subject` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `default_message` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `status` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

/*Table structure for table `tbl_cafe_food_details` */

DROP TABLE IF EXISTS `tbl_cafe_food_details`;

CREATE TABLE `tbl_cafe_food_details` (
  `id` bigint NOT NULL,
  `food_id` int NOT NULL,
  `ingredient_id` int NOT NULL,
  `quantity` float NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `tbl_cafe_foods` */

DROP TABLE IF EXISTS `tbl_cafe_foods`;

CREATE TABLE `tbl_cafe_foods` (
  `id` bigint NOT NULL,
  `store_id` int NOT NULL,
  `category_id` int NOT NULL,
  `name` varchar(250) DEFAULT NULL,
  `code` varchar(50) NOT NULL,
  `picture` varchar(100) NOT NULL,
  `price` float DEFAULT NULL,
  `status` char(1) NOT NULL,
  `position` int DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `created_by` int NOT NULL,
  `modified_at` datetime DEFAULT NULL,
  `modified_by` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_cafe_ingredients` */

DROP TABLE IF EXISTS `tbl_cafe_ingredients`;

CREATE TABLE `tbl_cafe_ingredients` (
  `id` bigint NOT NULL,
  `store_id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `unit_id` int DEFAULT NULL,
  `usage_unit_id` int NOT NULL,
  `conversion` float NOT NULL,
  `price` float NOT NULL,
  `picture` varchar(300) NOT NULL,
  `details` tinytext NOT NULL,
  `status` char(1) NOT NULL,
  `created_at` datetime NOT NULL,
  `created_by` int NOT NULL,
  `modified_at` datetime NOT NULL,
  `modified_by` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_cafe_items` */

DROP TABLE IF EXISTS `tbl_cafe_items`;

CREATE TABLE `tbl_cafe_items` (
  `id` bigint NOT NULL,
  `store_id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(20) NOT NULL,
  `category_id` int NOT NULL,
  `unit_id` int DEFAULT NULL,
  `brand_id` int DEFAULT NULL,
  `stock` float DEFAULT NULL,
  `price` float NOT NULL,
  `sale_price` float NOT NULL,
  `details` tinytext NOT NULL,
  `picture` varchar(200) NOT NULL,
  `status` char(1) NOT NULL,
  `position` int NOT NULL,
  `created_at` datetime NOT NULL,
  `created_by` int NOT NULL,
  `modified_at` datetime NOT NULL,
  `modified_by` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_cafe_purchase_details` */

DROP TABLE IF EXISTS `tbl_cafe_purchase_details`;

CREATE TABLE `tbl_cafe_purchase_details` (
  `id` bigint NOT NULL,
  `purchase_id` int NOT NULL,
  `item_id` int NOT NULL,
  `ingredient_id` int NOT NULL,
  `food_id` int NOT NULL,
  `unit_id` int NOT NULL,
  `quantity` float NOT NULL,
  `price` float NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_cafe_purchase_food_details` */

DROP TABLE IF EXISTS `tbl_cafe_purchase_food_details`;

CREATE TABLE `tbl_cafe_purchase_food_details` (
  `id` int NOT NULL AUTO_INCREMENT,
  `purchase_id` int NOT NULL,
  `food_id` int NOT NULL,
  `ingredient_id` int NOT NULL,
  `quantity` float NOT NULL,
  `price` float NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9238 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_cafe_purchase_return_details` */

DROP TABLE IF EXISTS `tbl_cafe_purchase_return_details`;

CREATE TABLE `tbl_cafe_purchase_return_details` (
  `id` bigint NOT NULL,
  `purchase_return_id` int NOT NULL,
  `item_id` int NOT NULL,
  `ingredient_id` int NOT NULL,
  `food_id` int NOT NULL,
  `quantity` float NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_cafe_purchase_return_food_details` */

DROP TABLE IF EXISTS `tbl_cafe_purchase_return_food_details`;

CREATE TABLE `tbl_cafe_purchase_return_food_details` (
  `id` int NOT NULL AUTO_INCREMENT,
  `purchase_return_id` int NOT NULL,
  `food_id` int NOT NULL,
  `ingredient_id` int NOT NULL,
  `quantity` float NOT NULL,
  `price` float NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_cafe_purchase_returns` */

DROP TABLE IF EXISTS `tbl_cafe_purchase_returns`;

CREATE TABLE `tbl_cafe_purchase_returns` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `store_id` int NOT NULL,
  `po_no` varchar(250) DEFAULT NULL,
  `school_id` int DEFAULT NULL,
  `total` float NOT NULL,
  `return_date` date NOT NULL,
  `created_at` datetime NOT NULL,
  `created_by` int NOT NULL,
  `modified_at` datetime NOT NULL,
  `modified_by` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_cafe_purchases` */

DROP TABLE IF EXISTS `tbl_cafe_purchases`;

CREATE TABLE `tbl_cafe_purchases` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `school_id` int DEFAULT NULL,
  `store_id` int NOT NULL,
  `po_no` varchar(250) DEFAULT NULL,
  `total` float NOT NULL,
  `supplier_id` int NOT NULL,
  `purchase_date` date NOT NULL,
  `created_at` datetime NOT NULL,
  `created_by` int NOT NULL,
  `modified_at` datetime NOT NULL,
  `modified_by` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1161 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_cafe_quantities` */

DROP TABLE IF EXISTS `tbl_cafe_quantities`;

CREATE TABLE `tbl_cafe_quantities` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `store_id` int NOT NULL,
  `item_id` int NOT NULL,
  `ingredient_id` int NOT NULL,
  `food_id` int NOT NULL,
  `quantity` float NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=479 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_cafe_sale_details` */

DROP TABLE IF EXISTS `tbl_cafe_sale_details`;

CREATE TABLE `tbl_cafe_sale_details` (
  `id` bigint NOT NULL,
  `sale_id` int NOT NULL,
  `item_id` int NOT NULL,
  `food_id` int NOT NULL,
  `combo_product_id` int NOT NULL,
  `quantity` float NOT NULL,
  `price` float NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_cafe_sale_food_details` */

DROP TABLE IF EXISTS `tbl_cafe_sale_food_details`;

CREATE TABLE `tbl_cafe_sale_food_details` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sale_id` int NOT NULL,
  `food_id` int NOT NULL,
  `ingredient_id` int NOT NULL,
  `quantity` float NOT NULL,
  `price` float NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=579514 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `tbl_cafe_sale_return_details` */

DROP TABLE IF EXISTS `tbl_cafe_sale_return_details`;

CREATE TABLE `tbl_cafe_sale_return_details` (
  `id` bigint NOT NULL,
  `sale_return_id` int NOT NULL,
  `item_id` int NOT NULL,
  `food_id` int NOT NULL,
  `combo_product_id` int NOT NULL,
  `quantity` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_cafe_sale_return_food_details` */

DROP TABLE IF EXISTS `tbl_cafe_sale_return_food_details`;

CREATE TABLE `tbl_cafe_sale_return_food_details` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sale_return_id` int NOT NULL,
  `food_id` int NOT NULL,
  `ingredient_id` int NOT NULL,
  `quantity` float NOT NULL,
  `price` float NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `tbl_cafe_sale_returns` */

DROP TABLE IF EXISTS `tbl_cafe_sale_returns`;

CREATE TABLE `tbl_cafe_sale_returns` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `store_id` int NOT NULL,
  `sale_no` varchar(250) DEFAULT NULL,
  `school_id` int DEFAULT NULL,
  `session_id` int DEFAULT NULL,
  `class_id` int DEFAULT NULL,
  `department_id` int DEFAULT NULL,
  `quantity` int DEFAULT NULL,
  `total` float NOT NULL,
  `return_date` date NOT NULL,
  `verified` char(1) NOT NULL DEFAULT 'N',
  `created_at` datetime NOT NULL,
  `created_by` int NOT NULL,
  `modified_at` datetime NOT NULL,
  `modified_by` int NOT NULL,
  `verified_at` datetime NOT NULL,
  `verified_by` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_cafe_sales` */

DROP TABLE IF EXISTS `tbl_cafe_sales`;

CREATE TABLE `tbl_cafe_sales` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `store_id` int NOT NULL,
  `card_id` bigint NOT NULL,
  `sale_no` varchar(255) DEFAULT NULL,
  `type` char(2) DEFAULT NULL,
  `school_id` int DEFAULT NULL,
  `session_id` int DEFAULT NULL,
  `class_id` int DEFAULT NULL,
  `student_session_id` int DEFAULT NULL,
  `student_fee_id` bigint NOT NULL,
  `department_id` int DEFAULT NULL,
  `employee_id` int DEFAULT NULL,
  `other_name` varchar(255) DEFAULT NULL,
  `other_mobile` varchar(15) DEFAULT NULL,
  `other_details` text,
  `total` float NOT NULL,
  `advance_amount` float NOT NULL,
  `bank_charges` float NOT NULL,
  `payment_method` varchar(2) NOT NULL,
  `bank_account_id` tinyint DEFAULT NULL,
  `cheque_no` varchar(20) DEFAULT NULL,
  `sale_date` datetime NOT NULL,
  `status` char(1) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `created_by` int NOT NULL,
  `modified_at` datetime NOT NULL,
  `modified_by` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `card_id_total` (`card_id`,`total`),
  KEY `index_dashboard` (`store_id`,`school_id`,`created_at`,`created_by`,`payment_method`)
) ENGINE=InnoDB AUTO_INCREMENT=123883 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_cafe_settings` */

DROP TABLE IF EXISTS `tbl_cafe_settings`;

CREATE TABLE `tbl_cafe_settings` (
  `id` tinyint NOT NULL DEFAULT '0',
  `site_title` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `copyright` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `date_format` char(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `time_format` char(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `image_resize` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `website_mode` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `password_change_days` int NOT NULL,
  `password_reuse` int NOT NULL,
  `master_password` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `general_name` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `general_email` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `smtp` varchar(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `smtp_security` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `smtp_host` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `smtp_port` varchar(5) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `smtp_username` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `smtp_password` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `allow_ips` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `lock_days` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

/*Table structure for table `tbl_cafe_stores` */

DROP TABLE IF EXISTS `tbl_cafe_stores`;

CREATE TABLE `tbl_cafe_stores` (
  `id` int NOT NULL,
  `company_id` bigint NOT NULL,
  `store` varchar(100) NOT NULL,
  `printer_ip_address` varchar(20) DEFAULT NULL,
  `printer_port` char(5) NOT NULL DEFAULT '9100',
  `opening_balance` int NOT NULL DEFAULT '0',
  `status` char(1) NOT NULL,
  `position` int NOT NULL,
  `date_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_cafe_suppliers` */

DROP TABLE IF EXISTS `tbl_cafe_suppliers`;

CREATE TABLE `tbl_cafe_suppliers` (
  `id` bigint NOT NULL,
  `name` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `company` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `address` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `mobile` varchar(25) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `ntn_no` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `status` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `date_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

/*Table structure for table `tbl_cafe_transfer_item_details` */

DROP TABLE IF EXISTS `tbl_cafe_transfer_item_details`;

CREATE TABLE `tbl_cafe_transfer_item_details` (
  `id` bigint NOT NULL,
  `transfer_id` int NOT NULL,
  `item_id` int NOT NULL,
  `ingredient_id` int NOT NULL,
  `food_id` int NOT NULL,
  `quantity` int NOT NULL,
  `price` double NOT NULL,
  `sale_price` double NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_cafe_transfer_items` */

DROP TABLE IF EXISTS `tbl_cafe_transfer_items`;

CREATE TABLE `tbl_cafe_transfer_items` (
  `id` bigint NOT NULL,
  `from_store_id` int DEFAULT NULL,
  `to_store_id` int DEFAULT NULL,
  `date` date DEFAULT NULL,
  `document` varchar(300) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `remarks` tinytext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `verified` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'N',
  `admin_id` bigint DEFAULT NULL,
  `date_time` datetime DEFAULT NULL,
  `verified_at` datetime NOT NULL,
  `verified_by` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `Date` (`date`),
  KEY `FromStore` (`from_store_id`),
  KEY `ToStore` (`to_store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

/*Table structure for table `tbl_cafe_units` */

DROP TABLE IF EXISTS `tbl_cafe_units`;

CREATE TABLE `tbl_cafe_units` (
  `id` int NOT NULL,
  `unit` varchar(100) NOT NULL,
  `quantity` float DEFAULT NULL,
  `status` char(1) NOT NULL,
  `position` int NOT NULL,
  `date_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_classes` */

DROP TABLE IF EXISTS `tbl_classes`;

CREATE TABLE `tbl_classes` (
  `id` int NOT NULL,
  `parent_id` int DEFAULT NULL,
  `name` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `report_name` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `schools` tinytext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `leaves_quota` int NOT NULL DEFAULT '10',
  `registrations` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT 'Y',
  `position` int DEFAULT NULL,
  `status` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `date_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

/*Table structure for table `tbl_schools` */

DROP TABLE IF EXISTS `tbl_schools`;

CREATE TABLE `tbl_schools` (
  `id` tinyint NOT NULL,
  `school_types` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `report_name` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `code` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `address` tinytext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `phone` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `fax` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `logo` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `designations` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `bank_accounts` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `online_bank_account_id` int NOT NULL DEFAULT '0',
  `company_id` int NOT NULL,
  `designation_id` int NOT NULL,
  `employee_id` bigint NOT NULL,
  `designation2_id` int NOT NULL,
  `employee2_id` bigint NOT NULL,
  `designation3_id` int NOT NULL,
  `employee3_id` bigint NOT NULL,
  `designation4_id` int NOT NULL,
  `employee4_id` bigint NOT NULL,
  `appointment_designations` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `transfer_fee` int DEFAULT '10000',
  `position` tinyint DEFAULT NULL,
  `status` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `modified_by` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

/*Table structure for table `tbl_student_notifications` */

DROP TABLE IF EXISTS `tbl_student_notifications`;

CREATE TABLE `tbl_student_notifications` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `session_id` int NOT NULL DEFAULT '0',
  `schools` text,
  `classes` text,
  `students` text,
  `status` char(1) DEFAULT NULL,
  `defaulters` char(1) DEFAULT 'N',
  `title` varchar(250) DEFAULT NULL,
  `details` text,
  `sale` char(1) NOT NULL,
  `admin_id` int DEFAULT NULL,
  `date_time` datetime DEFAULT NULL,
  `modified_by` int DEFAULT '0',
  `modified_at` datetime DEFAULT '0000-00-00 00:00:00',
  `sent` char(1) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT 'Y',
  `sent_by` int DEFAULT '0',
  `sent_at` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=398888 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_students` */

DROP TABLE IF EXISTS `tbl_students`;

CREATE TABLE `tbl_students` (
  `id` bigint NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `gender` char(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `birth_place` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `blood_group` char(4) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `hand` char(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `religion_id` tinyint DEFAULT NULL,
  `nationality_id` int DEFAULT NULL,
  `bform_no` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `weight` int DEFAULT NULL,
  `height` int DEFAULT NULL,
  `picture` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `address` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `location_id` int DEFAULT NULL,
  `postal_address` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `postal_location_id` int DEFAULT NULL,
  `phone` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `mobile` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `email` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `comments` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `school_id` tinyint DEFAULT NULL,
  `class_id` int DEFAULT NULL,
  `fee_group_id` int DEFAULT '1',
  `academic_session_id` int DEFAULT NULL,
  `roll_no` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `registration_no` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `booklet_no` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `registration_date` date DEFAULT NULL,
  `joining_date` date DEFAULT NULL,
  `previous_school` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `previous_class` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `username` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `password` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `android_device_id` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `ios_device_id` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `device_last_login` datetime DEFAULT NULL,
  `siblings_mode` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT 'F',
  `tax_payer` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `tax_payer_name` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `tax_payer_cnic` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `tax_payer_ntn` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `house` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `official_email` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `status` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `modified_by` int DEFAULT NULL,
  `mobile_code` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `ios_code` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `vehicle1` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `vehicle2` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `vehicle3` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `vehicle4` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `guardian` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT 'Father',
  PRIMARY KEY (`id`),
  KEY `School_Class` (`school_id`,`class_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
