-- Add new tables for invitation tracking and bulk send

CREATE TABLE IF NOT EXISTS `supervisor_invitations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `supervisor_name` varchar(255) NOT NULL,
  `company` varchar(255) NOT NULL,
  `company_address` text DEFAULT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `invitation_batches` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `invitation_queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `error_message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `sent_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_invitation_queue_batch` (`batch_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `email_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `source_type` varchar(30) NOT NULL,
  `source_id` int(11) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `message_id` varchar(255) DEFAULT NULL,
  `tracking_token` varchar(64) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'sent',
  `sent_at` timestamp NULL DEFAULT NULL,
  `opened_at` timestamp NULL DEFAULT NULL,
  `replied_at` timestamp NULL DEFAULT NULL,
  `last_event_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_email_messages_email` (`email`),
  KEY `idx_email_messages_token` (`tracking_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
