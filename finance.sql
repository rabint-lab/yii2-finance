-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 08, 2019 at 09:33 AM
-- Server version: 5.7.26-0ubuntu0.19.04.1
-- PHP Version: 7.2.17-0ubuntu0.19.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cinema`
--

-- --------------------------------------------------------

--
-- Table structure for table `finance_draft`
--

CREATE TABLE `finance_draft` (
  `id` int(11) NOT NULL COMMENT 'شناسه',
  `user_id` int(11) DEFAULT NULL COMMENT 'کاربر',
  `checker_id` int(11) DEFAULT NULL COMMENT 'بررسی کننده',
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'عنوان',
  `bank` varchar(45) COLLATE utf8_unicode_ci NOT NULL COMMENT 'بانک',
  `form_id` varchar(45) COLLATE utf8_unicode_ci NOT NULL COMMENT 'شماره فیش',
  `created_at` int(11) DEFAULT NULL COMMENT 'تاریخ ایجاد',
  `updated_at` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'تاریخ تایید',
  `status` tinyint(1) DEFAULT NULL COMMENT 'وضعیت',
  `description` text COLLATE utf8_unicode_ci COMMENT 'توضیحات',
  `check_url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'تصویر فیش'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `finance_transactions`
--

CREATE TABLE `finance_transactions` (
  `id` int(11) NOT NULL COMMENT 'شناسه',
  `created_at` int(11) NOT NULL COMMENT 'زمان درخواست',
  `transactioner` int(11) DEFAULT NULL COMMENT 'انجام دهنده',
  `amount` int(11) NOT NULL COMMENT 'مبلغ',
  `status` int(11) NOT NULL COMMENT 'وضعیت',
  `gateway` int(11) DEFAULT NULL COMMENT 'درگاه',
  `gateway_reciept` varchar(255) DEFAULT NULL COMMENT 'رسید درگاه',
  `gateway_meta` varchar(255) DEFAULT NULL COMMENT 'اطلاعات درگاه',
  `transactioner_ip` varchar(255) NOT NULL COMMENT 'آی پی انجام دهنده',
  `internal_reciept` varchar(255) NOT NULL COMMENT 'سفره داخلی',
  `token` varchar(255) NOT NULL COMMENT 'کلید',
  `return_url` varchar(255) NOT NULL COMMENT 'لینک بازگشت',
  `additional_rows` text NOT NULL COMMENT 'اطلاعات فاکتور',
  `metadata` varchar(255) DEFAULT NULL COMMENT 'متادیتا'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `finance_wallet`
--

CREATE TABLE `finance_wallet` (
  `id` int(11) NOT NULL COMMENT 'شناسه',
  `created_at` int(11) NOT NULL COMMENT 'زمان درخواست',
  `user_id` int(11) DEFAULT NULL COMMENT 'ذینفغ',
  `amount` int(11) NOT NULL COMMENT 'مبلغ',
  `transactioner` int(11) DEFAULT NULL COMMENT 'انجام دهنده',
  `transactioner_ip` varchar(255) NOT NULL COMMENT 'آی پی انجام دهده',
  `description` varchar(255) NOT NULL COMMENT 'توضیحات',
  `metadata` varchar(255) NOT NULL COMMENT 'اطلاعات متا'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `finance_draft`
--
ALTER TABLE `finance_draft`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_finance_draft_user1` (`user_id`),
  ADD KEY `fk_finance_draft_user2` (`checker_id`);

--
-- Indexes for table `finance_transactions`
--
ALTER TABLE `finance_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_finance_transaction_user_id` (`transactioner`);

--
-- Indexes for table `finance_wallet`
--
ALTER TABLE `finance_wallet`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_finance_walet_user_user_id` (`user_id`),
  ADD KEY `fk_finance_walet_transactioner_user_id` (`transactioner`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `finance_draft`
--
ALTER TABLE `finance_draft`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'شناسه', AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `finance_transactions`
--
ALTER TABLE `finance_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'شناسه', AUTO_INCREMENT=55;
--
-- AUTO_INCREMENT for table `finance_wallet`
--
ALTER TABLE `finance_wallet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'شناسه', AUTO_INCREMENT=113;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `finance_draft`
--
ALTER TABLE `finance_draft`
  ADD CONSTRAINT `fk_finance_draft_user1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_finance_draft_user2` FOREIGN KEY (`checker_id`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `finance_transactions`
--
ALTER TABLE `finance_transactions`
  ADD CONSTRAINT `fk_finance_transaction_user_id` FOREIGN KEY (`transactioner`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `finance_wallet`
--
ALTER TABLE `finance_wallet`
  ADD CONSTRAINT `fk_finance_walet_transactioner_user_id` FOREIGN KEY (`transactioner`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_finance_walet_user_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
