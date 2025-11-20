-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 20, 2025 at 04:12 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `latest`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` varchar(20) NOT NULL,
  `admin_username` varchar(50) DEFAULT NULL,
  `admin_dob` date DEFAULT NULL,
  `admin_password` varchar(100) DEFAULT NULL,
  `admin_email` varchar(100) DEFAULT NULL,
  `registered_by` varchar(20) DEFAULT NULL,
  `admin_login_status` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `admin_username`, `admin_dob`, `admin_password`, `admin_email`, `registered_by`, `admin_login_status`) VALUES
('A001', 'admin1', '1995-04-10', 'adminpass', 'admin1@mail.com', 'SA001', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `login_session`
--

CREATE TABLE `login_session` (
  `session_id` int(11) NOT NULL,
  `user_id` varchar(20) DEFAULT NULL,
  `role` varchar(20) DEFAULT NULL,
  `login_time` datetime DEFAULT NULL,
  `logout_time` datetime DEFAULT NULL,
  `status` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `medicine_info`
--

CREATE TABLE `medicine_info` (
  `medicine_id` varchar(20) NOT NULL,
  `medicine_name` varchar(100) DEFAULT NULL,
  `medicine_price` decimal(10,2) DEFAULT NULL,
  `medicine_quantity` int(11) DEFAULT NULL,
  `medicine_description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medicine_info`
--

INSERT INTO `medicine_info` (`medicine_id`, `medicine_name`, `medicine_price`, `medicine_quantity`, `medicine_description`) VALUES
('M001', 'Panadol', 5.00, 99, 'Pain relief tablet'),
('M002', 'Amoxicillin', 12.50, 99, 'Antibiotic 500mg'),
('M003', 'Vitamin C', 8.00, 100, 'Immune booster tablet');

-- --------------------------------------------------------

--
-- Table structure for table `order`
--

CREATE TABLE `order` (
  `order_id` varchar(20) NOT NULL,
  `order_date` date DEFAULT NULL,
  `patient_id` varchar(20) DEFAULT NULL,
  `staff_id` varchar(20) DEFAULT NULL,
  `status_id` enum('Pending','Approved','Rejected','Done') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order`
--

INSERT INTO `order` (`order_id`, `order_date`, `patient_id`, `staff_id`, `status_id`) VALUES
('O001', '2025-01-15', 'PT001', 'D001', 'Done'),
('O002', '2025-01-18', 'PT002', 'D002', 'Approved');

-- --------------------------------------------------------

--
-- Table structure for table `order_approval`
--

CREATE TABLE `order_approval` (
  `approval_id` varchar(20) NOT NULL,
  `order_id` varchar(20) DEFAULT NULL,
  `approver_id` varchar(20) DEFAULT NULL,
  `approval_date` datetime DEFAULT NULL,
  `approval_status` varchar(20) DEFAULT NULL,
  `approval_comment` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_approval`
--

INSERT INTO `order_approval` (`approval_id`, `order_id`, `approver_id`, `approval_date`, `approval_status`, `approval_comment`) VALUES
('AP001', 'O001', 'P001', '2025-01-16 10:33:00', 'Approved', 'Ready for collection'),
('AP002', 'O002', 'P001', '2025-11-18 15:10:13', 'Approved', '1'),
('AP003', 'O002', 'P001', '2025-11-18 15:10:19', 'Rejected', '1'),
('AP004', 'O002', 'P001', '2025-11-18 15:21:33', 'Approved', ''),
('AP005', 'O002', 'P001', '2025-11-18 15:22:17', 'Done', '1'),
('AP006', 'O002', 'P001', '2025-11-18 15:22:31', 'Rejected', '1'),
('AP007', 'O002', 'P001', '2025-11-18 15:32:15', 'Rejected', '1'),
('AP008', 'O002', 'P001', '2025-11-18 15:46:22', 'Approved', ''),
('AP009', 'O001', 'P001', '2025-11-20 03:15:56', 'Approved', '1'),
('AP010', 'O002', 'P001', '2025-11-20 03:18:36', 'Approved', '');

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `order_detail_id` varchar(20) NOT NULL,
  `order_id` varchar(20) DEFAULT NULL,
  `medicine_id` varchar(20) DEFAULT NULL,
  `medicine_quantity` int(11) DEFAULT NULL,
  `medicine_price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_details`
--

INSERT INTO `order_details` (`order_detail_id`, `order_id`, `medicine_id`, `medicine_quantity`, `medicine_price`) VALUES
('OD001', 'O001', 'M001', 2, 5.00),
('OD002', 'O001', 'M003', 1, 8.00),
('OD003', 'O002', 'M002', 1, 12.50),
('OD004', 'O002', 'M001', 1, 5.00);

-- --------------------------------------------------------

--
-- Table structure for table `patient`
--

CREATE TABLE `patient` (
  `patient_id` varchar(20) NOT NULL,
  `patient_name` varchar(100) DEFAULT NULL,
  `patient_date_of_birth` date DEFAULT NULL,
  `patient_phone` varchar(15) DEFAULT NULL,
  `patient_gender` varchar(10) DEFAULT NULL,
  `patient_email` varchar(100) DEFAULT NULL,
  `patient_address` varchar(255) DEFAULT NULL,
  `registered_by` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patient`
--

INSERT INTO `patient` (`patient_id`, `patient_name`, `patient_date_of_birth`, `patient_phone`, `patient_gender`, `patient_email`, `patient_address`, `registered_by`) VALUES
('PT001', 'John Doe', '1990-03-12', '0123456789', 'Male', 'john@example.com', '123 Main Street', 'A001'),
('PT002', 'Emily Tan', '1987-08-21', '0198765432', 'Female', 'emily@example.com', '88 Pine Road', 'A001');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `staff_id` varchar(20) NOT NULL,
  `staff_name` varchar(100) DEFAULT NULL,
  `staff_dob` date DEFAULT NULL,
  `staff_specialization` varchar(100) DEFAULT NULL,
  `staff_role` enum('doctor','pharmacist') DEFAULT NULL,
  `staff_phone` varchar(15) DEFAULT NULL,
  `staff_email` varchar(100) DEFAULT NULL,
  `staff_password` varchar(100) DEFAULT NULL,
  `registered_by` varchar(20) DEFAULT NULL,
  `staff_status` enum('active','inactive','block') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`staff_id`, `staff_name`, `staff_dob`, `staff_specialization`, `staff_role`, `staff_phone`, `staff_email`, `staff_password`, `registered_by`, `staff_status`) VALUES
('D001', 'Dr. Lim Zhi Wei', '1980-02-12', 'General Medicine', 'doctor', '0182233445', 'lim@mail.com', 'doc123', 'A001', 'active'),
('D002', 'Dr. Wong Jia Hui', '1977-09-30', 'Pediatrics', 'doctor', '0164455667', 'wong@mail.com', 'doc234', 'A001', 'active'),
('P001', 'Pharmacist Lee', '1985-11-15', 'Pharmacy', 'pharmacist', '0179988776', 'lee@mail.com', 'pharm001', 'A001', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `super_admin`
--

CREATE TABLE `super_admin` (
  `super_admin_id` varchar(20) NOT NULL,
  `super_admin_username` varchar(50) DEFAULT NULL,
  `super_admin_password` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `super_admin`
--

INSERT INTO `super_admin` (`super_admin_id`, `super_admin_username`, `super_admin_password`) VALUES
('SA001', 'superadmin', 'password123');

-- --------------------------------------------------------

--
-- Table structure for table `user_approval`
--

CREATE TABLE `user_approval` (
  `approval_id` int(11) NOT NULL,
  `approver_id` varchar(20) DEFAULT NULL,
  `approved_user_id` varchar(20) DEFAULT NULL,
  `approval_date` datetime DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD KEY `registered_by` (`registered_by`);

--
-- Indexes for table `login_session`
--
ALTER TABLE `login_session`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `medicine_info`
--
ALTER TABLE `medicine_info`
  ADD PRIMARY KEY (`medicine_id`);

--
-- Indexes for table `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `staff_id` (`staff_id`);

--
-- Indexes for table `order_approval`
--
ALTER TABLE `order_approval`
  ADD PRIMARY KEY (`approval_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `approver_id` (`approver_id`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`order_detail_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `medicine_id` (`medicine_id`);

--
-- Indexes for table `patient`
--
ALTER TABLE `patient`
  ADD PRIMARY KEY (`patient_id`),
  ADD KEY `registered_by` (`registered_by`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`staff_id`),
  ADD KEY `registered_by` (`registered_by`);

--
-- Indexes for table `super_admin`
--
ALTER TABLE `super_admin`
  ADD PRIMARY KEY (`super_admin_id`);

--
-- Indexes for table `user_approval`
--
ALTER TABLE `user_approval`
  ADD PRIMARY KEY (`approval_id`),
  ADD KEY `approver_id` (`approver_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `login_session`
--
ALTER TABLE `login_session`
  MODIFY `session_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_approval`
--
ALTER TABLE `user_approval`
  MODIFY `approval_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin`
--
ALTER TABLE `admin`
  ADD CONSTRAINT `admin_ibfk_1` FOREIGN KEY (`registered_by`) REFERENCES `super_admin` (`super_admin_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `login_session`
--
ALTER TABLE `login_session`
  ADD CONSTRAINT `login_session_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `admin` (`admin_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `order`
--
ALTER TABLE `order`
  ADD CONSTRAINT `order_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`patient_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `order_ibfk_2` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`staff_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `order_approval`
--
ALTER TABLE `order_approval`
  ADD CONSTRAINT `order_approval_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `order` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `order_approval_ibfk_2` FOREIGN KEY (`approver_id`) REFERENCES `staff` (`staff_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `order` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`medicine_id`) REFERENCES `medicine_info` (`medicine_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `patient`
--
ALTER TABLE `patient`
  ADD CONSTRAINT `patient_ibfk_1` FOREIGN KEY (`registered_by`) REFERENCES `admin` (`admin_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `staff`
--
ALTER TABLE `staff`
  ADD CONSTRAINT `staff_ibfk_1` FOREIGN KEY (`registered_by`) REFERENCES `admin` (`admin_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_approval`
--
ALTER TABLE `user_approval`
  ADD CONSTRAINT `user_approval_ibfk_1` FOREIGN KEY (`approver_id`) REFERENCES `super_admin` (`super_admin_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
