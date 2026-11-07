-- ========================================================
-- Sumanth College Hostel Management System
-- MySQL Workbench & phpMyAdmin Database Schema
-- Database: sumanth_hostel_db
-- ========================================================

-- Create Database if not exists
CREATE DATABASE IF NOT EXISTS `sumanth_hostel_db` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `sumanth_hostel_db`;

-- Drop existing tables to avoid conflicts (in order of foreign keys)
DROP TABLE IF EXISTS `allocations`;
DROP TABLE IF EXISTS `rooms`;
DROP TABLE IF EXISTS `students`;

-- ========================================================
-- 1. Students Table
-- ========================================================
CREATE TABLE `students` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `student_id` VARCHAR(50) NOT NULL UNIQUE,
    `full_name` VARCHAR(100) NOT NULL,
    `gender` ENUM('Male', 'Female', 'Other') NOT NULL DEFAULT 'Male',
    `department` VARCHAR(100) NOT NULL,
    `year_of_study` VARCHAR(30) NOT NULL DEFAULT '1st Year',
    `phone` VARCHAR(25) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `guardian_name` VARCHAR(100) NOT NULL,
    `guardian_phone` VARCHAR(25) NOT NULL,
    `address` TEXT NULL,
    `status` ENUM('Active', 'Alumni', 'Suspended') NOT NULL DEFAULT 'Active',
    `registered_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================================
-- 2. Rooms Table
-- ========================================================
CREATE TABLE `rooms` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `room_number` VARCHAR(30) NOT NULL UNIQUE,
    `block_name` VARCHAR(80) NOT NULL,
    `floor` VARCHAR(20) NOT NULL DEFAULT '1st Floor',
    `room_type` VARCHAR(60) NOT NULL DEFAULT 'Double Sharing AC',
    `capacity` INT NOT NULL DEFAULT 2,
    `occupied_beds` INT NOT NULL DEFAULT 0,
    `fee_per_semester` DECIMAL(10,2) NOT NULL DEFAULT 35000.00,
    `status` ENUM('Available', 'Full', 'Maintenance') NOT NULL DEFAULT 'Available',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================================
-- 3. Room Allocations Table
-- ========================================================
CREATE TABLE `allocations` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `allocation_code` VARCHAR(50) NOT NULL UNIQUE,
    `student_id` INT NOT NULL,
    `room_id` INT NOT NULL,
    `bed_number` VARCHAR(20) NOT NULL DEFAULT 'Bed-1',
    `allocation_date` DATE NOT NULL,
    `academic_year` VARCHAR(30) NOT NULL DEFAULT '2026-2027',
    `fee_amount` DECIMAL(10,2) NOT NULL DEFAULT 35000.00,
    `payment_status` ENUM('Paid', 'Pending', 'Partial') NOT NULL DEFAULT 'Paid',
    `allocation_status` ENUM('Allocated', 'Vacated', 'Transferred') NOT NULL DEFAULT 'Allocated',
    `remarks` VARCHAR(255) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_allocations_student` FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_allocations_room` FOREIGN KEY (`room_id`) REFERENCES `rooms`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================================
-- Sample Seed Data
-- ========================================================

-- Insert Sample Students
INSERT INTO `students` (`id`, `student_id`, `full_name`, `gender`, `department`, `year_of_study`, `phone`, `email`, `guardian_name`, `guardian_phone`, `address`, `status`) VALUES
(1, 'SCH-2026-001', 'Sumanth Varma', 'Male', 'Computer Science & AI', '3rd Year', '9848012345', 'sumanth.varma@college.edu', 'Krishna Varma', '9848099887', 'Flat 402, Sai Residency, Vijayawada', 'Active'),
(2, 'SCH-2026-002', 'Ananya Sharma', 'Female', 'Data Science', '2nd Year', '9123456780', 'ananya.sharma@college.edu', 'Rajesh Sharma', '9123459999', 'Road No 12, Banjara Hills, Hyderabad', 'Active'),
(3, 'SCH-2026-003', 'Kiran Kumar', 'Male', 'Electronics & Comm', '4th Year', '9440123456', 'kiran.kumar@college.edu', 'Narayana Swamy', '9440199999', 'Sector 4, MVP Colony, Visakhapatnam', 'Active'),
(4, 'SCH-2026-004', 'Deepika Reddy', 'Female', 'Cyber Security', '1st Year', '9988776655', 'deepika.reddy@college.edu', 'Murali Reddy', '9988770000', 'MG Road, Vijayawada', 'Active');

-- Insert Sample Rooms
INSERT INTO `rooms` (`id`, `room_number`, `block_name`, `floor`, `room_type`, `capacity`, `occupied_beds`, `fee_per_semester`, `status`) VALUES
(1, 'A-101', 'Aryabhata Block (Boys)', '1st Floor', 'Double Sharing AC', 2, 1, 38000.00, 'Available'),
(2, 'A-102', 'Aryabhata Block (Boys)', '1st Floor', 'Single Deluxe AC', 1, 1, 55000.00, 'Full'),
(3, 'A-201', 'Aryabhata Block (Boys)', '2nd Floor', 'Triple Sharing Non-AC', 3, 0, 24000.00, 'Available'),
(4, 'B-101', 'Sarojini Block (Girls)', '1st Floor', 'Double Sharing AC', 2, 1, 38000.00, 'Available'),
(5, 'B-102', 'Sarojini Block (Girls)', '1st Floor', 'Double Sharing AC', 2, 1, 38000.00, 'Available'),
(6, 'C-301', 'Ramanujan Executive', '3rd Floor', 'Single Premium Studio', 1, 0, 65000.00, 'Available');

-- Insert Sample Allocations
INSERT INTO `allocations` (`id`, `allocation_code`, `student_id`, `room_id`, `bed_number`, `allocation_date`, `academic_year`, `fee_amount`, `payment_status`, `allocation_status`, `remarks`) VALUES
(1, 'ALC-9011', 1, 1, 'Bed-1', '2026-07-15', '2026-2027', 38000.00, 'Paid', 'Allocated', 'Allocated with AC pass & high-speed Wi-Fi'),
(2, 'ALC-9012', 3, 2, 'Bed-1', '2026-07-16', '2026-2027', 55000.00, 'Paid', 'Allocated', 'Single occupancy final year honor scholar'),
(3, 'ALC-9013', 2, 4, 'Bed-1', '2026-07-18', '2026-2027', 38000.00, 'Paid', 'Allocated', 'Sarojini Block Wing A'),
(4, 'ALC-9014', 4, 5, 'Bed-2', '2026-08-01', '2026-2027', 38000.00, 'Paid', 'Allocated', 'Freshman student room allocation');
