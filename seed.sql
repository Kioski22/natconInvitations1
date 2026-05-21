-- Empty database schema for natconInvitations1
-- Import this into a new MySQL database on cPanel.

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE TABLE `supervisor_invitations` (
  `id` int(11) NOT NULL,
  `supervisor_name` varchar(255) NOT NULL,
  `company` varchar(255) NOT NULL,
  `company_address` text DEFAULT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `invitation_batches` (
  `id` int(11) NOT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `invitation_queue` (
  `id` int(11) NOT NULL,
  `batch_id` int(11) DEFAULT NULL,
  `type` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `salutation` varchar(50) DEFAULT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `company` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `hr_email` varchar(255) DEFAULT NULL,
  `tracking_token` varchar(64) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'queued',
  `attempts` int(11) DEFAULT 0,
  `locked_at` timestamp NULL DEFAULT NULL,
  `locked_by` varchar(64) DEFAULT NULL,
  `last_attempt_at` timestamp NULL DEFAULT NULL,
  `error_message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `sent_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `email_messages` (
  `id` int(11) NOT NULL,
  `source_type` varchar(30) NOT NULL,
  `source_id` int(11) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `message_id` varchar(255) DEFAULT NULL,
  `gmail_message_id` varchar(128) DEFAULT NULL,
  `gmail_thread_id` varchar(128) DEFAULT NULL,
  `tracking_token` varchar(64) DEFAULT NULL,
  `tracking_id` varchar(64) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'sent',
  `sent_at` timestamp NULL DEFAULT NULL,
  `opened_at` timestamp NULL DEFAULT NULL,
  `replied_at` timestamp NULL DEFAULT NULL,
  `clicked_at` timestamp NULL DEFAULT NULL,
  `bounced_at` timestamp NULL DEFAULT NULL,
  `last_event_at` timestamp NULL DEFAULT NULL,
  `last_checked_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `email_events` (
  `id` int(11) NOT NULL,
  `email_message_id` int(11) NOT NULL,
  `event_type` varchar(20) NOT NULL,
  `event_key` varchar(128) NOT NULL,
  `event_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `meta_json` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `companies` (
  `company_id` int(11) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `company_address` varchar(255) DEFAULT NULL,
  `excel_filename` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `delegates` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `firstname` varchar(100) DEFAULT NULL,
  `middle` varchar(100) DEFAULT NULL,
  `lastname` varchar(100) DEFAULT NULL,
  `suffix` varchar(50) DEFAULT NULL,
  `dateofbirth` date DEFAULT NULL,
  `emailid` varchar(255) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `mobilenumber` varchar(50) DEFAULT NULL,
  `prc_license_type` varchar(255) DEFAULT NULL,
  `prc_license_number` varchar(255) DEFAULT NULL,
  `prc_license_expiration_date` date DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `chapter` varchar(100) DEFAULT NULL,
  `sector` varchar(50) DEFAULT NULL,
  `register_type` varchar(50) DEFAULT NULL,
  `isPWD` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `invitations` (
  `id` int(11) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `salutation` varchar(20) DEFAULT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `company` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `status` varchar(50) DEFAULT 'sent',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `event` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `soa_sequence` (
  `id` int(11) NOT NULL,
  `soa_number` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `supervisor_invitations`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `invitation_batches`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `invitation_queue`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_invitation_queue_batch` (`batch_id`),
  ADD KEY `idx_invitation_queue_status` (`status`),
  ADD KEY `idx_invitation_queue_locked` (`locked_at`);

ALTER TABLE `email_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_email_messages_email` (`email`),
  ADD KEY `idx_email_messages_token` (`tracking_token`),
  ADD KEY `idx_email_messages_gmail_thread` (`gmail_thread_id`),
  ADD KEY `idx_email_messages_gmail_message` (`gmail_message_id`),
  ADD KEY `idx_email_messages_status` (`status`);

ALTER TABLE `email_events`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_email_event` (`email_message_id`, `event_type`, `event_key`),
  ADD KEY `idx_email_events_message` (`email_message_id`);

ALTER TABLE `companies`
  ADD PRIMARY KEY (`company_id`);

ALTER TABLE `delegates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`);

ALTER TABLE `invitations`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `soa_sequence`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `supervisor_invitations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `invitation_batches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `invitation_queue`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `email_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `email_events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `companies`
  MODIFY `company_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `delegates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `invitations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `soa_sequence`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `delegates`
  ADD CONSTRAINT `delegates_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`company_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `email_events`
  ADD CONSTRAINT `fk_email_events_message` FOREIGN KEY (`email_message_id`) REFERENCES `email_messages` (`id`) ON DELETE CASCADE;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;