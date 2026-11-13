-- --------------------------------------------------------
-- Database: mailer_db
-- --------------------------------------------------------

-- --------------------------------------------------------
-- Table structure for table: sent_emails
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `sent_emails` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `subject` VARCHAR(255) NOT NULL,
    `from_email` VARCHAR(255) NOT NULL,
    `status` ENUM('sent', 'failed', 'pending') DEFAULT 'pending',
    `success_count` INT DEFAULT 0,
    `fail_count` INT DEFAULT 0,
    `total_count` INT DEFAULT 0,
    `sent_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `error_message` TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table: email_recipients
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `email_recipients` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `sent_email_id` INT NOT NULL,
    `recipient_email` VARCHAR(255) NOT NULL,
    `recipient_name` VARCHAR(255) DEFAULT NULL,
    `status` ENUM('sent', 'failed') NOT NULL,
    `error_message` TEXT,
    `sent_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`sent_email_id`) REFERENCES `sent_emails` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample data for testing
INSERT INTO `sent_emails` (`subject`, `from_email`, `status`, `success_count`, `fail_count`, `total_count`, `sent_at`) VALUES
('Test Email - Welcome', 'admin@example.com', 'sent', 10, 0, 10, NOW());

INSERT INTO `email_recipients` (`sent_email_id`, `recipient_email`, `recipient_name`, `status`) VALUES
(1, 'john@example.com', 'John Doe', 'sent'),
(1, 'jane@example.com', 'Jane Smith', 'sent'),
(1, 'test@example.com', 'Test User', 'sent');