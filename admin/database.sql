-- Netcoder 2026 Full Database Setup

-- Create the database
CREATE DATABASE IF NOT EXISTS `netcoder2026`;
USE `netcoder2026`;

-- 1. Create Gallery Table
CREATE TABLE IF NOT EXISTS `gallery` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `image_path` VARCHAR(255) NOT NULL,
    `category` VARCHAR(100) DEFAULT 'General',
    `uploaded_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Create Blogs Table
CREATE TABLE IF NOT EXISTS `blogs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `excerpt` TEXT,
    `main_content` TEXT,
    `main_image` VARCHAR(255),
    `author` VARCHAR(100) DEFAULT 'Admin',
    `tags` VARCHAR(255),
    `date_posted` DATE DEFAULT CURRENT_DATE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Create Blog Sections Table
CREATE TABLE IF NOT EXISTS `blog_sections` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `blog_id` INT NOT NULL,
    `section_title` VARCHAR(255),
    `section_content` TEXT,
    `section_image` VARCHAR(255),
    CONSTRAINT `fk_blog_id` FOREIGN KEY (`blog_id`) REFERENCES `blogs`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Create Admin Table (Standard for login systems)
CREATE TABLE IF NOT EXISTS `admins` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert a default admin (password is 'admin123' hashed or just 'admin123' depending on system)
-- INSERT INTO `admins` (`username`, `password`) VALUES ('admin', 'admin123');
