-- Create database if not exists
CREATE DATABASE IF NOT EXISTS `divinosys` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Use the database
USE `divinosys`;

-- Set SQL mode
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- Disable foreign key checks temporarily
SET FOREIGN_KEY_CHECKS = 0;

-- Source the main database file
SOURCE /docker-entrypoint-initdb.d/init.sql;

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- Commit transaction
COMMIT;
