-- ============================================
-- Migration Script: Fix Login Schema Issues
-- ============================================
-- Run this in phpMyAdmin or MySQL command line
-- Database: pharmacy_management_system
-- ============================================

-- Step 1: Add email column to super_admin table
ALTER TABLE `super_admin` 
ADD COLUMN `super_admin_email` VARCHAR(100) COLLATE utf8mb4_general_ci DEFAULT NULL 
AFTER `super_admin_username`;

-- Step 2: Add sample email for existing superadmin
UPDATE `super_admin` 
SET `super_admin_email` = 'superadmin@mail.com' 
WHERE `super_admin_id` = 'SA001';

-- Step 3: Verify the changes
SELECT * FROM `super_admin`;

-- ============================================
-- IMPORTANT: After running this SQL, you MUST run:
-- migrate_passwords.php to hash all passwords
-- ============================================
