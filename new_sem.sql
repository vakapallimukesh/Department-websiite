-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:4306
-- Generation Time: Feb 12, 2026 at 04:51 PM
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
-- Database: `new_sem`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `hid` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `username`, `password`, `created_at`, `hid`) VALUES
(1, 'prudhvi_admin', 'prudhvi123', '2025-08-22 16:19:03', 1),
(2, 'vayu_admin', 'vayu456', '2025-08-22 16:19:03', 2),
(3, 'agni_admin', 'agni789', '2025-08-22 16:19:03', 3),
(4, 'aakash_admin', 'aakash101', '2025-08-22 16:19:03', 4),
(5, 'jal_admin', 'jal202', '2025-08-22 16:19:03', 5);

-- --------------------------------------------------------

--
-- Table structure for table `alumni_employment_history`
--

CREATE TABLE `alumni_employment_history` (
  `id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `designation` varchar(100) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `industry` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `appreciations`
--

CREATE TABLE `appreciations` (
  `appreciation_id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `event_id` int(11) NOT NULL,
  `points` int(11) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appreciations`
--

INSERT INTO `appreciations` (`appreciation_id`, `student_id`, `event_id`, `points`, `reason`, `created_at`, `created_by`) VALUES
(1, '23B91A0710', 5, 2, 'JAM', '2026-02-12 15:46:18', 14),
(2, '23B91A0708', 5, 100, 'GG', '2026-02-12 15:50:39', 14);

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `class_id` int(11) NOT NULL,
  `academic_year` varchar(20) NOT NULL,
  `year` tinyint(1) NOT NULL,
  `semester` tinyint(1) NOT NULL,
  `branch` varchar(50) NOT NULL,
  `section` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`class_id`, `academic_year`, `year`, `semester`, `branch`, `section`) VALUES
(1, '2022-2026', 4, 1, 'CSD', 'A'),
(2, '2023-2027', 3, 1, 'CSD', 'A'),
(3, '2023-2027', 3, 1, 'CSIT', 'A'),
(4, '2024-2028', 2, 1, 'CSD', 'A'),
(5, '2024-2028', 2, 1, 'CSIT', 'A'),
(6, '2024-2028', 2, 1, 'CSIT', 'B');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `event_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `venue` varchar(150) DEFAULT NULL,
  `event_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `hid` int(11) DEFAULT NULL,
  `accept_registrations` tinyint(1) NOT NULL DEFAULT 1,
  `participate_points` int(11) NOT NULL DEFAULT 5,
  `winner_points` int(11) NOT NULL DEFAULT 10,
  `runner_points` int(11) NOT NULL DEFAULT 8,
  `organiser_points` int(11) NOT NULL DEFAULT 7
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`event_id`, `admin_id`, `title`, `description`, `venue`, `event_date`, `start_time`, `end_time`, `image_path`, `created_at`, `updated_at`, `hid`, `accept_registrations`, `participate_points`, `winner_points`, `runner_points`, `organiser_points`) VALUES
(5, 4, 'House Event 1', '1st house event', 'Back Lab', '2026-01-20', '16:00:00', NULL, 'admin/pages/files/events/Zephyrus Duo 15 x ZЯØFØRM_3840x2160.jpg', '2025-11-29 09:49:36', '2026-01-19 04:24:49', 4, 1, 5, 15, 8, 7);

-- --------------------------------------------------------

--
-- Table structure for table `event_feedback`
--

CREATE TABLE `event_feedback` (
  `feedback_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `rating` tinyint(1) NOT NULL,
  `comments` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `faculties`
--

CREATE TABLE `faculties` (
  `faculty_id` int(11) NOT NULL,
  `faculty_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `class_id` int(11) DEFAULT NULL,
  `phone_number` varchar(15) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faculties`
--

INSERT INTO `faculties` (`faculty_id`, `faculty_name`, `email`, `password`, `class_id`, `phone_number`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Dr.M.Suresh Babu', 'sureshbabu.k@srkrec.edu.in', 'suresh123', 1, '9123456781', 1, '2025-08-22 16:19:03', '2025-09-04 09:48:46'),
(2, 'Dr. K. Srinivasa Rao', 'ksinivasarao@srkrec.edu.in', 'srinivasa123', 2, NULL, 1, '2025-08-22 16:19:03', '2025-08-22 16:19:03'),
(3, 'Mr. K. Bhanu Rajesh Naidu', 'bhanurajeshnaidu@srkrec.edu.in', 'bhanu123', 4, NULL, 1, '2025-08-22 16:19:03', '2025-08-22 16:19:03'),
(4, 'A ASWINI PRIYANKA', 'aswini.areti@srkrec.edu.in', 'aswini123', 1, NULL, 1, '2025-08-22 16:19:03', '2025-08-22 16:19:03'),
(5, 'ANGARA SATYAM', 'satyama@srkrec.edu.in', 'satyam123', 2, NULL, 1, '2025-08-22 16:19:03', '2025-08-22 16:19:03'),
(6, 'S MOHAN KRISHNA', 'mohankrishna.seerla@srkrec.edu.in', 'mohan123', 0, NULL, 1, '2025-08-22 16:19:03', '2026-02-06 04:16:21'),
(7, 'P S V Surya Kumar', 'suryakumar.poduru@srkrec.edu.in', 'surya123', 1, NULL, 1, '2025-08-22 16:19:03', '2025-08-22 16:19:03'),
(8, 'Dr N. Gopala Krishna Murthy', 'gopinukala@srkrec.edu.in', 'gopala123', 3, NULL, 1, '2025-08-22 16:19:03', '2025-08-22 16:19:03'),
(9, 'Jonnapalli Tulasi Rajesh', 'jtulasirajesh@srkrec.edu.in', 'tulasi123', 5, NULL, 1, '2025-08-22 16:19:03', '2025-08-22 16:19:03'),
(10, 'Navya Nallaparaju', 'navyanallaparaju@srkrec.edu.in', 'navya123', 6, NULL, 1, '2025-08-22 16:19:03', '2025-08-22 16:19:03'),
(11, 'N PRAVEEN', 'neti.praveen@srkrec.edu.in', 'praveen123', 3, NULL, 1, '2025-08-22 16:19:03', '2025-08-22 16:19:03'),
(12, 'Mr. A. Krishna Veni', 'krishnaveni@srkrec.edu.in', 'krishna123', 5, NULL, 1, '2025-08-22 16:19:03', '2025-08-22 16:19:03'),
(13, 'Mr. K.V.V.S. Trinadh Naidu', 'kvvstrinadhnaidu@srkrec.edu.in', 'trinadh123', 6, NULL, 1, '2025-08-22 16:19:03', '2025-08-22 16:19:03'),
(14, 'Penmetsa Mouna', 'mouna.nandyala@srkrec.edu.in', 'mouna123', 3, NULL, 1, '2025-08-22 16:19:03', '2025-08-22 16:19:03'),
(15, 'Pericherla Manoj', 'manoj.p@srkrec.edu.in', 'manoj123', 5, NULL, 1, '2025-08-22 16:19:03', '2025-08-22 16:19:03'),
(16, 'K V Sunil Varma', 'sunil.kunuku@srkrec.edu.in', 'sunil123', 6, NULL, 1, '2025-08-22 16:19:03', '2025-08-22 16:19:03'),
(17, 'N Aneela', 'n.aneela@srkrec.edu.in', 'aneela123', 1, '', 1, '2025-12-13 04:05:31', '2025-12-13 04:05:31'),
(18, 'M S Suseela', 'm.s.suseela@srkrec.edu.in', 'suseela123', 1, '', 1, '2025-12-13 04:05:31', '2025-12-13 04:05:31');

-- --------------------------------------------------------

--
-- Table structure for table `houses`
--

CREATE TABLE `houses` (
  `hid` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `houses`
--

INSERT INTO `houses` (`hid`, `name`, `created_at`) VALUES
(1, 'PRUDHVI', '2025-08-22 16:19:03'),
(2, 'VAYU', '2025-08-22 16:19:03'),
(3, 'AGNI', '2025-08-22 16:19:03'),
(4, 'AAKASH', '2025-08-22 16:19:03'),
(5, 'JAL', '2025-08-22 16:19:03');

-- --------------------------------------------------------

--
-- Table structure for table `house_admins`
--

CREATE TABLE `house_admins` (
  `house_admin_id` int(11) NOT NULL,
  `house_name` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `hid` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `house_admins`
--

INSERT INTO `house_admins` (`house_admin_id`, `house_name`, `password`, `hid`, `created_at`) VALUES
(1, 'PRUDHVI_Admin', 'prudhvi123', 1, '2025-08-22 16:19:04'),
(2, 'VAYU_Admin', 'vayu456', 2, '2025-08-22 16:19:04'),
(3, 'AGNI_Admin', 'agni789', 3, '2025-08-22 16:19:04'),
(4, 'AAKASH_Admin', 'aakash101', 4, '2025-08-22 16:19:04'),
(5, 'JAL_Admin', 'jal202', 5, '2025-08-22 16:19:04');

-- --------------------------------------------------------

--
-- Table structure for table `leave_applications`
--

CREATE TABLE `leave_applications` (
  `id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `section` varchar(10) NOT NULL,
  `leave_type` enum('sick','personal','emergency','other') NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `reason` text NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `status_description` text DEFAULT NULL,
  `applied_at` timestamp NULL DEFAULT current_timestamp(),
  `processed_at` timestamp NULL DEFAULT NULL,
  `processed_by` int(11) DEFAULT NULL,
  `hod_remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leave_applications`
--

INSERT INTO `leave_applications` (`id`, `student_id`, `section`, `leave_type`, `start_date`, `end_date`, `reason`, `status`, `status_description`, `applied_at`, `processed_at`, `processed_by`, `hod_remarks`) VALUES
(0, '23B91A0749', '', 'personal', '2025-12-02', '2025-12-06', 'Medical Emergency\r\n', 'approved', NULL, '2025-12-01 04:45:38', '2025-12-01 04:45:56', 8, '\nFaculty: Approved by faculty.');

-- --------------------------------------------------------

--
-- Table structure for table `organizers`
--

CREATE TABLE `organizers` (
  `organizer_id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `event_id` int(11) NOT NULL,
  `points` int(11) DEFAULT 0,
  `role` varchar(50) DEFAULT 'organizer',
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `participants`
--

CREATE TABLE `participants` (
  `participant_id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `event_id` int(11) NOT NULL,
  `registered_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `participation_status` enum('registered','attended','absent','cancelled') DEFAULT 'attended',
  `points` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `penalties`
--

CREATE TABLE `penalties` (
  `penalty_id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `event_id` int(11) NOT NULL,
  `points` int(11) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `registrations`
--

CREATE TABLE `registrations` (
  `registered_id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `event_id` int(11) NOT NULL,
  `status` enum('pending','confirmed','cancelled') DEFAULT 'confirmed',
  `registered_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `registrations`
--

INSERT INTO `registrations` (`registered_id`, `student_id`, `event_id`, `status`, `registered_at`, `updated_at`) VALUES
(0, '23B91A0749', 5, 'confirmed', '2025-11-30 10:16:06', '2025-11-30 10:16:06'),
(0, '24B91A0759', 5, 'confirmed', '2026-01-19 04:25:20', '2026-01-19 04:25:20');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `branch` varchar(50) NOT NULL,
  `section` varchar(10) NOT NULL,
  `class_id` int(11) NOT NULL,
  `hid` int(11) DEFAULT NULL,
  `is_alumni` tinyint(1) DEFAULT 0,
  `profile_picture` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `name`, `email`, `password`, `branch`, `section`, `class_id`, `hid`, `is_alumni`, `profile_picture`, `created_at`) VALUES
('21B91A6216', 'G UDAY KIRAN', '21b91a6216@srkrec.edu.in', '21B91A6216', 'CSD', 'A', 1, 1, 0, NULL, '2025-08-22 16:19:03'),
('22B91A6201', 'ARNEPALLI MEGANA', '22b91a6201@srkrec.edu.in', '22B91A6201', 'CSD', 'A', 1, 2, 0, NULL, '2025-08-22 16:19:03'),
('22B91A6202', 'BAYYE JOSEPH KUMAR', '22b91a6202@srkrec.edu.in', '22B91A6202', 'CSD', 'A', 1, 1, 0, NULL, '2025-08-22 16:19:03'),
('22B91A6203', 'BHAVANAM LAKSHMAN KUMAR REDDY', '22b91a6203@srkrec.edu.in', '22B91A6203', 'CSD', 'A', 1, 4, 0, NULL, '2025-08-22 16:19:03'),
('22B91A6204', 'BORRA AVINASH', '22b91a6204@srkrec.edu.in', '22B91A6204', 'CSD', 'A', 1, 3, 0, NULL, '2025-08-22 16:19:03'),
('22B91A6205', 'BORRA HIMA SRI', '22b91a6205@srkrec.edu.in', '22B91A6205', 'CSD', 'A', 1, 2, 0, NULL, '2025-08-22 16:19:03'),
('22B91A6206', 'BUDDE VENKATA SATYA TEJESH', '22b91a6206@srkrec.edu.in', '22B91A6206', 'CSD', 'A', 1, 1, 0, NULL, '2025-08-22 16:19:03'),
('22B91A6207', 'CHIKILE RAJESH', '22b91a6207@srkrec.edu.in', '22B91A6207', 'CSD', 'A', 1, 4, 0, NULL, '2025-08-22 16:19:03'),
('22B91A6208', 'CHILAKALAPUDI ABHI RAAMA PHANINDRA', '22b91a6208@srkrec.edu.in', '22B91A6208', 'CSD', 'A', 1, 4, 0, NULL, '2025-08-22 16:19:03'),
('22B91A6209', 'CHIMAKURTHI TEJA RUPAK', '22b91a6209@srkrec.edu.in', '22B91A6209', 'CSD', 'A', 1, 3, 0, NULL, '2025-08-22 16:19:03'),
('22B91A6210', 'DAKKUMALLA VARSHA', '22b91a6210@srkrec.edu.in', '22B91A6210', 'CSD', 'A', 1, 2, 0, NULL, '2025-08-22 16:19:03'),
('22B91A6211', 'DONAVALLI REVATHI', '22b91a6211@srkrec.edu.in', '22B91A6211', 'CSD', 'A', 1, 5, 0, NULL, '2025-08-22 16:19:03'),
('22B91A6212', 'GEDELA SAI ABHINAY', '22b91a6212@srkrec.edu.in', '22B91A6212', 'CSD', 'A', 1, 1, 0, NULL, '2025-08-22 16:19:03'),
('22B91A6213', 'GOTTUMUKKALA BHARGAVI', '22b91a6213@srkrec.edu.in', '22B91A6213', 'CSD', 'A', 1, 5, 0, NULL, '2025-08-22 16:19:03'),
('22B91A6214', 'INUMARTHI SRINAVYA', '22b91a6214@srkrec.edu.in', '22B91A6214', 'CSD', 'A', 1, 5, 0, NULL, '2025-08-22 16:19:03'),
('22B91A6215', 'JADDU JYOTHIRMAI INDIRA PRIYADARSINI DEVI', '22b91a6215@srkrec.edu.in', '22B91A6215', 'CSD', 'A', 1, 1, 0, NULL, '2025-08-22 16:19:03'),
('22B91A6216', 'JAKKAMSETTI SANJANI', '22b91a6216@srkrec.edu.in', '22B91A6216', 'CSD', 'A', 1, 3, 0, NULL, '2025-08-22 16:19:03'),
('22B91A6217', 'JOGI PAVAN TEJA', '22b91a6217@srkrec.edu.in', '22B91A6217', 'CSD', 'A', 1, 1, 0, NULL, '2025-08-22 16:19:03'),
('22B91A6218', 'KAMBHAMPATI SHALANI SINDHU SRI', '22b91a6218@srkrec.edu.in', '22B91A6218', 'CSD', 'A', 1, 4, 0, NULL, '2025-08-22 16:19:03'),
('22B91A6219', 'KANUMURI RISHITHA VARMA', '22b91a6219@srkrec.edu.in', '22B91A6219', 'CSD', 'A', 1, 1, 0, NULL, '2025-08-22 16:19:03'),
('22B91A6220', 'KAPUDASI SNIGDHA', '22b91a6220@srkrec.edu.in', '22B91A6220', 'CSD', 'A', 1, 5, 0, NULL, '2025-08-22 16:19:03'),
('22B91A6221', 'KARUMURI TEJA SIDDARDHA PAVAN KUMAR', '22b91a6221@srkrec.edu.in', '22B91A6221', 'CSD', 'A', 1, 4, 0, NULL, '2025-08-22 16:19:03'),
('22B91A6222', 'KETHA SURYA PRAKASH', '22b91a6222@srkrec.edu.in', '22B91A6222', 'CSD', 'A', 1, 5, 0, NULL, '2025-08-22 16:19:03'),
('22B91A6223', 'KOLA YESWANTH', '22b91a6223@srkrec.edu.in', '22B91A6223', 'CSD', 'A', 1, 5, 0, NULL, '2025-08-22 16:19:03'),
('22B91A6224', 'KOLATI STEPHEN SOUDH', '22b91a6224@srkrec.edu.in', '22B91A6224', 'CSD', 'A', 1, 3, 0, NULL, '2025-08-22 16:19:03'),
('22B91A6225', 'KOLLABATHULA SHYAM BABU', '22b91a6225@srkrec.edu.in', '22B91A6225', 'CSD', 'A', 1, 4, 0, NULL, '2025-08-22 16:19:03'),
('22B91A6226', 'KOLLATI VISHNU TEJA', '22b91a6226@srkrec.edu.in', '22B91A6226', 'CSD', 'A', 1, 1, 0, NULL, '2025-08-22 16:19:03'),
('22B91A6227', 'KOPPARTI HONEY NAGA SANDEEP', '22b91a6227@srkrec.edu.in', '22B91A6227', 'CSD', 'A', 1, 3, 0, NULL, '2025-08-22 16:19:03'),
('22B91A6228', 'LAKSHMI VENKATA NIKHITHA', '22b91a6228@srkrec.edu.in', '22B91A6228', 'CSD', 'A', 1, 1, 0, NULL, '2025-08-22 16:19:03'),
('22B91A6229', 'MADDI AKSHAYA SRI', '22b91a6229@srkrec.edu.in', '22B91A6229', 'CSD', 'A', 1, 3, 0, NULL, '2025-08-22 16:19:03'),
('22B91A6230', 'MANDANGI MOUNIKA', '22b91a6230@srkrec.edu.in', '22B91A6230', 'CSD', 'A', 1, 2, 0, NULL, '2025-08-22 16:19:03'),
('22B91A6231', 'MANGENA JAHNAVI', '22b91a6231@srkrec.edu.in', '22B91A6231', 'CSD', 'A', 1, 3, 0, NULL, '2025-08-22 16:19:03'),
('22B91A6232', 'MANGINETI MOHAN SATYA SIVA ROHITH KUMAR', '22b91a6232@srkrec.edu.in', '22B91A6232', 'CSD', 'A', 1, 2, 0, NULL, '2025-08-22 16:19:03'),
('22B91A6233', 'MATTA BALA VEERRAJU', '22b91a6233@srkrec.edu.in', '22B91A6233', 'CSD', 'A', 1, 2, 0, NULL, '2025-08-22 16:19:03'),
('22B91A6234', 'MOTURI SANDILYA', '22b91a6234@srkrec.edu.in', '22B91A6234', 'CSD', 'A', 1, 3, 0, NULL, '2025-08-22 16:19:03'),
('22B91A6235', 'MUDUNURI MANOJ SAI ASWANTH VARMA', '22b91a6235@srkrec.edu.in', '22B91A6235', 'CSD', 'A', 1, 1, 0, NULL, '2025-08-22 16:19:03'),
('22B91A6236', 'NALLAM HEMA SAI SRI LAKSHMI', '22b91a6236@srkrec.edu.in', '22B91A6236', 'CSD', 'A', 1, 5, 0, NULL, '2025-08-22 16:19:03'),
('22B91A6237', 'PAILA NIKHIL', '22b91a6237@srkrec.edu.in', '22B91A6237', 'CSD', 'A', 1, 2, 0, NULL, '2025-08-22 16:19:03'),
('22B91A6238', 'PANAKALA RAMA NAGESWARA RAO', '22b91a6238@srkrec.edu.in', '22B91A6238', 'CSD', 'A', 1, 3, 0, NULL, '2025-08-22 16:19:03'),
('22B91A6239', 'PEPETI GANESH', '22b91a6239@srkrec.edu.in', '22B91A6239', 'CSD', 'A', 1, 5, 0, NULL, '2025-08-22 16:19:03'),
('22B91A6240', 'PERABATHULA SOMESWARA RAO', '22b91a6240@srkrec.edu.in', '22B91A6240', 'CSD', 'A', 1, 4, 0, NULL, '2025-08-22 16:19:03'),
('22B91A6241', 'PIPPALLA RUSHI GUNA SHANMUKH', '22b91a6241@srkrec.edu.in', '22B91A6241', 'CSD', 'A', 1, 2, 0, NULL, '2025-08-22 16:19:03'),
('22B91A6242', 'POSIMSETTY SRI VISWA BHARATH', '22b91a6242@srkrec.edu.in', '22B91A6242', 'CSD', 'A', 1, 3, 0, NULL, '2025-08-22 16:19:03'),
('22B91A6243', 'POTHAMSETTI KODANDA RAMA NAGA GANESH', '22b91a6243@srkrec.edu.in', '22B91A6243', 'CSD', 'A', 1, 5, 0, NULL, '2025-08-22 16:19:03'),
('22B91A6244', 'POTTURI GAYATRI', '22b91a6244@srkrec.edu.in', '22B91A6244', 'CSD', 'A', 1, 2, 0, NULL, '2025-08-22 16:19:03'),
('22B91A6245', 'PULI DURGA BHAVANI', '22b91a6245@srkrec.edu.in', '22B91A6245', 'CSD', 'A', 1, 3, 0, NULL, '2025-08-22 16:19:03'),
('22B91A6246', 'PULLURU KRISHNA VAMSI', '22b91a6246@srkrec.edu.in', '22B91A6246', 'CSD', 'A', 1, 2, 0, NULL, '2025-08-22 16:19:03'),
('22B91A6247', 'PUTHINIDI JNANESWARI', '22b91a6247@srkrec.edu.in', '22B91A6247', 'CSD', 'A', 1, 4, 0, NULL, '2025-08-22 16:19:03'),
('22B91A6248', 'RAAVI CHARWAK', '22b91a6248@srkrec.edu.in', '22B91A6248', 'CSD', 'A', 1, 1, 0, NULL, '2025-08-22 16:19:03'),
('22B91A6249', 'SETTI NARENDRA KUMAR', '22b91a6249@srkrec.edu.in', '22B91A6249', 'CSD', 'A', 1, 5, 0, NULL, '2025-08-22 16:19:03'),
('22B91A6250', 'SHAIK AHMED', '22b91a6250@srkrec.edu.in', '22B91A6250', 'CSD', 'A', 1, 2, 0, NULL, '2025-08-22 16:19:03'),
('22B91A6251', 'SHAIK KARIMUNNISA', '22b91a6251@srkrec.edu.in', '22B91A6251', 'CSD', 'A', 1, 1, 0, NULL, '2025-08-22 16:19:03'),
('22B91A6252', 'TELLAKULA VEERA RAGHAVA', '22b91a6252@srkrec.edu.in', '22B91A6252', 'CSD', 'A', 1, 1, 0, NULL, '2025-08-22 16:19:03'),
('22B91A6253', 'UNDAPALLI DIVYA', '22b91a6253@srkrec.edu.in', '22B91A6253', 'CSD', 'A', 1, 5, 0, NULL, '2025-08-22 16:19:03'),
('22B91A6254', 'UNDURTHI MANOJ', '22b91a6254@srkrec.edu.in', '22B91A6254', 'CSD', 'A', 1, 4, 0, NULL, '2025-08-22 16:19:03'),
('22B91A6255', 'VAKAPALLI H V SAI SURYA SWAPANTH', '22b91a6255@srkrec.edu.in', '22B91A6255', 'CSD', 'A', 1, 5, 0, NULL, '2025-08-22 16:19:03'),
('22B91A6256', 'VATAPALLI GNANA SEKHAR', '22b91a6256@srkrec.edu.in', '22B91A6256', 'CSD', 'A', 1, 1, 0, NULL, '2025-08-22 16:19:03'),
('22B91A6257', 'VEERAVALLI SATYA VENKATA SRINADH', '22b91a6257@srkrec.edu.in', '22B91A6257', 'CSD', 'A', 1, 2, 0, NULL, '2025-08-22 16:19:03'),
('22B91A6259', 'VILLURI MOHINI MANGA LAKSHMI MANASA', '22b91a6259@srkrec.edu.in', '22B91A6259', 'CSD', 'A', 1, 4, 0, NULL, '2025-08-22 16:19:03'),
('23B91A0701', 'BARAKATA TARUN SWAMY', '23b91a0701@srkrec.edu.in', '23B91A0701', 'CSIT', 'A', 3, 1, 0, NULL, '2025-08-22 16:19:03'),
('23B91A0702', 'BARRI SRAVYA SREE', '23b91a0702@srkrec.edu.in', '23B91A0702', 'CSIT', 'A', 3, 5, 0, NULL, '2025-08-22 16:19:03'),
('23B91A0703', 'BEERA JNANENDRA VARMA', '23b91a0703@srkrec.edu.in', '23B91A0703', 'CSIT', 'A', 3, 2, 0, NULL, '2025-08-22 16:19:03'),
('23B91A0704', 'BILLA SAHITHI', '23b91a0704@srkrec.edu.in', '23B91A0704', 'CSIT', 'A', 3, 4, 0, NULL, '2025-08-22 16:19:03'),
('23B91A0705', 'CHADARAM BHANU VENKATA MANIKANTA', '23b91a0705@srkrec.edu.in', '23B91A0705', 'CSIT', 'A', 3, 5, 0, NULL, '2025-08-22 16:19:03'),
('23B91A0706', 'CHEEPU SAI VIKAS', '23b91a0706@srkrec.edu.in', '23B91A0706', 'CSIT', 'A', 3, 1, 0, NULL, '2025-08-22 16:19:03'),
('23B91A0707', 'DATTI VENKATA RAMANA', '23b91a0707@srkrec.edu.in', '23B91A0707', 'CSIT', 'A', 3, 4, 0, NULL, '2025-08-22 16:19:03'),
('23B91A0708', 'DHARMAVARUPU CHANDANA', '23b91a0708@srkrec.edu.in', '23B91A0708', 'CSIT', 'A', 3, 2, 0, NULL, '2025-08-22 16:19:03'),
('23B91A0709', 'DURU MERY SUNEETHA', '23b91a0709@srkrec.edu.in', '23B91A0709', 'CSIT', 'A', 3, 3, 0, NULL, '2025-08-22 16:19:03'),
('23B91A0710', 'GADDAM CHANDRIKA SRI PRIYA', '23b91a0710@srkrec.edu.in', '23B91A0710', 'CSIT', 'A', 3, 3, 0, NULL, '2025-08-22 16:19:03'),
('23B91A0711', 'GANESNA SATYA RAJESH', '23b91a0711@srkrec.edu.in', '23B91A0711', 'CSIT', 'A', 3, 5, 0, NULL, '2025-08-22 16:19:03'),
('23B91A0712', 'GIRIJALA PRASHANTH KUMAR', '23b91a0712@srkrec.edu.in', '23B91A0712', 'CSIT', 'A', 3, 1, 0, NULL, '2025-08-22 16:19:03'),
('23B91A0713', 'GOPATHI KALYANI', '23b91a0713@srkrec.edu.in', '23B91A0713', 'CSIT', 'A', 3, 4, 0, NULL, '2025-08-22 16:19:03'),
('23B91A0714', 'GOTTUMUKKALA NIKHILA VALLI', '23b91a0714@srkrec.edu.in', '23B91A0714', 'CSIT', 'A', 3, 2, 0, NULL, '2025-08-22 16:19:03'),
('23B91A0715', 'GOWRIPATNAM BHAGYAKIRAN', '23b91a0715@srkrec.edu.in', '23B91A0715', 'CSIT', 'A', 3, 2, 0, NULL, '2025-08-22 16:19:03'),
('23B91A0716', 'JALDHI PRINCESS GLORY JASMINE', '23b91a0716@srkrec.edu.in', '23B91A0716', 'CSIT', 'A', 3, 4, 0, NULL, '2025-08-22 16:19:03'),
('23B91A0717', 'KADIYALA NAVYA SRI', '23b91a0717@srkrec.edu.in', '23B91A0717', 'CSIT', 'A', 3, 5, 0, NULL, '2025-08-22 16:19:03'),
('23B91A0718', 'KAGITHA BHANU DURGA PRASAD', '23b91a0718@srkrec.edu.in', '23B91A0718', 'CSIT', 'A', 3, 4, 0, NULL, '2025-08-22 16:19:03'),
('23B91A0719', 'KALLA GUNADEEP', '23b91a0719@srkrec.edu.in', '23B91A0719', 'CSIT', 'A', 3, 4, 0, NULL, '2025-08-22 16:19:03'),
('23B91A0720', 'KANUBOINA VIJAYA LAKSHMI', '23b91a0720@srkrec.edu.in', '23B91A0720', 'CSIT', 'A', 3, 4, 0, NULL, '2025-08-22 16:19:03'),
('23B91A0721', 'KANUMURI SUDHA', '23b91a0721@srkrec.edu.in', '23B91A0721', 'CSIT', 'A', 3, 4, 0, NULL, '2025-08-22 16:19:03'),
('23B91A0722', 'KARRI LAKSHMI PRASANNA', '23b91a0722@srkrec.edu.in', '23B91A0722', 'CSIT', 'A', 3, 3, 0, NULL, '2025-08-22 16:19:03'),
('23B91A0723', 'KATTA SRAVANI', '23b91a0723@srkrec.edu.in', '23B91A0723', 'CSIT', 'A', 3, 2, 0, NULL, '2025-08-22 16:19:03'),
('23B91A0724', 'KHANDAVALLI VYSHNAVI', '23b91a0724@srkrec.edu.in', '23B91A0724', 'CSIT', 'A', 3, 4, 0, NULL, '2025-08-22 16:19:03'),
('23B91A0725', 'KOCHERLA YESWANTH', '23b91a0725@srkrec.edu.in', '23B91A0725', 'CSIT', 'A', 3, 2, 0, NULL, '2025-08-22 16:19:03'),
('23B91A0726', 'KODI VAISHNAVI', '23b91a0726@srkrec.edu.in', '23B91A0726', 'CSIT', 'A', 3, 5, 0, NULL, '2025-08-22 16:19:03'),
('23B91A0727', 'KOLLI SHANMUKHA SRIRAM CHARAN TEJA', '23b91a0727@srkrec.edu.in', '23B91A0727', 'CSIT', 'A', 3, 3, 0, NULL, '2025-08-22 16:19:03'),
('23B91A0728', 'KOTTA S N VASAVI SRIVALLI', '23b91a0728@srkrec.edu.in', '23B91A0728', 'CSIT', 'A', 3, 4, 0, NULL, '2025-08-22 16:19:03'),
('23B91A0729', 'KURASALA HARSHA VARDHAN', '23b91a0729@srkrec.edu.in', '23B91A0729', 'CSIT', 'A', 3, 5, 0, NULL, '2025-08-22 16:19:03'),
('23B91A0730', 'LOKAM MAHITANJALI', '23b91a0730@srkrec.edu.in', '23B91A0730', 'CSIT', 'A', 3, 1, 0, NULL, '2025-08-22 16:19:03'),
('23B91A0731', 'MAILABATTULA LOUKYATHA', '23b91a0731@srkrec.edu.in', '23B91A0731', 'CSIT', 'A', 3, 5, 0, NULL, '2025-08-22 16:19:03'),
('23B91A0732', 'MANELLI SRAVANI', '23b91a0732@srkrec.edu.in', '23B91A0732', 'CSIT', 'A', 3, 5, 0, NULL, '2025-08-22 16:19:03'),
('23B91A0733', 'MEDISETTI RAMA KRISHNA SAI', '23b91a0733@srkrec.edu.in', '23B91A0733', 'CSIT', 'A', 3, 1, 0, NULL, '2025-08-22 16:19:03'),
('23B91A0734', 'MUTHABATHULA PUNEETH', '23b91a0734@srkrec.edu.in', '23B91A0734', 'CSIT', 'A', 3, 4, 0, NULL, '2025-08-22 16:19:03'),
('23B91A0735', 'NAKKA MOHITH SRI NAGA SAI PAVAN', '23b91a0735@srkrec.edu.in', '23B91A0735', 'CSIT', 'A', 3, 5, 0, NULL, '2025-08-22 16:19:03'),
('23B91A0736', 'NANDAMURI BALA SESHA SATYA SRI', '23b91a0736@srkrec.edu.in', '23B91A0736', 'CSIT', 'A', 3, 3, 0, NULL, '2025-08-22 16:19:03'),
('23B91A0737', 'NANDRU VINAY BABU', '23b91a0737@srkrec.edu.in', '23B91A0737', 'CSIT', 'A', 3, 4, 0, NULL, '2025-08-22 16:19:03'),
('23B91A0738', 'NULAKANI LEELA MADHAVA RAO', '23b91a0738@srkrec.edu.in', '23B91A0738', 'CSIT', 'A', 3, 2, 0, NULL, '2025-08-22 16:19:03'),
('23B91A0739', 'PACHIGOLLA RISHITHA MANASA SURYA GAYATRI', '23b91a0739@srkrec.edu.in', '23B91A0739', 'CSIT', 'A', 3, 3, 0, NULL, '2025-08-22 16:19:03'),
('23B91A0740', 'PANJA MUKUNDA SRI NAGA SANTOSH', '23b91a0740@srkrec.edu.in', '23B91A0740', 'CSIT', 'A', 3, 1, 0, NULL, '2025-08-22 16:19:03'),
('23B91A0741', 'PANJA NAGA VENKATA PRASAD RAJA', '23b91a0741@srkrec.edu.in', '23B91A0741', 'CSIT', 'A', 3, 1, 0, NULL, '2025-08-22 16:19:03'),
('23B91A0742', 'PASUPULETI DAIVA PRASAD', '23b91a0742@srkrec.edu.in', '23B91A0742', 'CSIT', 'A', 3, 2, 0, NULL, '2025-08-22 16:19:03'),
('23B91A0743', 'PASUPULETI JASWANTH RAMANA TEJA', '23b91a0743@srkrec.edu.in', '23B91A0743', 'CSIT', 'A', 3, 5, 0, NULL, '2025-08-22 16:19:03'),
('23B91A0744', 'PECHETTI SRI VINAYAK', '23b91a0744@srkrec.edu.in', '23B91A0744', 'CSIT', 'A', 3, 1, 0, NULL, '2025-08-22 16:19:03'),
('23B91A0745', 'PETTA PRANATHI', '23b91a0745@srkrec.edu.in', '23B91A0745', 'CSIT', 'A', 3, 5, 0, NULL, '2025-08-22 16:19:03'),
('23B91A0746', 'PUVVALA DEVI AISHWARYA', '23b91a0746@srkrec.edu.in', '23B91A0746', 'CSIT', 'A', 3, 4, 0, NULL, '2025-08-22 16:19:03'),
('23B91A0747', 'RAMANA DIVYA JYOTHIKA', '23b91a0747@srkrec.edu.in', '23B91A0747', 'CSIT', 'A', 3, 2, 0, NULL, '2025-08-22 16:19:03'),
('23B91A0748', 'SEELABOINA RAMADEVI', '23b91a0748@srkrec.edu.in', '23B91A0748', 'CSIT', 'A', 3, 4, 0, NULL, '2025-08-22 16:19:03'),
('23B91A0749', 'SEELABOINA SANTOSH KUMAR', 'santoshkumar90101s@gmail.com', '$2y$10$lBrCBoxBoHyXMSmV3GkUOeK5.Kzb1Var4YWJ2Xgl2/B2564X8K0wG', 'CSIT', 'A', 3, 4, 0, 'uploads/profile_pictures/23B91A0749_1764263994.jpg', '2025-08-22 16:19:03'),
('23B91A0750', 'SHAIK REENAZ', '23b91a0750@srkrec.edu.in', '23B91A0750', 'CSIT', 'A', 3, 1, 0, NULL, '2025-08-22 16:19:03'),
('23B91A0751', 'SHAIK THAHIR BASHA', '23b91a0751@srkrec.edu.in', '23B91A0751', 'CSIT', 'A', 3, 4, 0, NULL, '2025-08-22 16:19:03'),
('23B91A0752', 'SIRAPARAPU PRANATHI SAI VARSHINI', '23b91a0752@srkrec.edu.in', '23B91A0752', 'CSIT', 'A', 3, 5, 0, NULL, '2025-08-22 16:19:03'),
('23B91A0753', 'TAMMA LOKESH', '23b91a0753@srkrec.edu.in', '23B91A0753', 'CSIT', 'A', 3, 5, 0, NULL, '2025-08-22 16:19:03'),
('23B91A0754', 'TUMMA SRI HARSHA', '23b91a0754@srkrec.edu.in', '23B91A0754', 'CSIT', 'A', 3, 5, 0, NULL, '2025-08-22 16:19:03'),
('23B91A0755', 'VALLABHANI SAHITHI', '23b91a0755@srkrec.edu.in', '23B91A0755', 'CSIT', 'A', 3, 3, 0, NULL, '2025-08-22 16:19:03'),
('23B91A0756', 'VEERANKI MAHESH BABU', '23b91a0756@srkrec.edu.in', '23B91A0756', 'CSIT', 'A', 3, 5, 0, NULL, '2025-08-22 16:19:03'),
('23B91A0757', 'VETCHA G N V S L SAISREE', '23b91a0757@srkrec.edu.in', '23B91A0757', 'CSIT', 'A', 3, 3, 0, NULL, '2025-08-22 16:19:03'),
('23B91A0758', 'YATHAM LAKSHMI PRASANNA', '23b91a0758@srkrec.edu.in', '23B91A0758', 'CSIT', 'A', 3, 5, 0, NULL, '2025-08-22 16:19:03'),
('23B91A6201', 'ADDAGARLA SRI VIDYA SAGAR', '23b91a6201@srkrec.edu.in', '23B91A6201', 'CSD', 'A', 2, 2, 0, NULL, '2025-08-22 16:19:03'),
('23B91A6202', 'AKSHINTALA HARSHATH', '23b91a6202@srkrec.edu.in', '23B91A6202', 'CSD', 'A', 2, 3, 0, NULL, '2025-08-22 16:19:03'),
('23B91A6203', 'BANDARU BHANU SATYA PRAKASH', '23b91a6203@srkrec.edu.in', '23B91A6203', 'CSD', 'A', 2, 2, 0, NULL, '2025-08-22 16:19:03'),
('23B91A6204', 'BODDETI DEVI NAGA VENKATA SAI DEEPAK', '23b91a6204@srkrec.edu.in', '23B91A6204', 'CSD', 'A', 2, 5, 0, NULL, '2025-08-22 16:19:03'),
('23B91A6206', 'BOLISETTY KEDARESWARI', '23b91a6206@srkrec.edu.in', '23B91A6206', 'CSD', 'A', 2, 2, 0, NULL, '2025-08-22 16:19:03'),
('23B91A6207', 'BORRA TERESSA', '23b91a6207@srkrec.edu.in', '23B91A6207', 'CSD', 'A', 2, 5, 0, NULL, '2025-08-22 16:19:03'),
('23B91A6208', 'CHAGANTI DHANESH KUMAR', '23b91a6208@srkrec.edu.in', '23B91A6208', 'CSD', 'A', 2, 2, 0, NULL, '2025-08-22 16:19:03'),
('23B91A6209', 'CHELLABOYINA YAMINI', '23b91a6209@srkrec.edu.in', '23B91A6209', 'CSD', 'A', 2, 2, 0, NULL, '2025-08-22 16:19:03'),
('23B91A6210', 'CHINDADA JYOTHI', '23b91a6210@srkrec.edu.in', '23B91A6210', 'CSD', 'A', 2, 3, 0, NULL, '2025-08-22 16:19:03'),
('23B91A6211', 'CHINTADA NISSY SUDEEPTHI', '23b91a6211@srkrec.edu.in', '23B91A6211', 'CSD', 'A', 2, 5, 0, NULL, '2025-08-22 16:19:03'),
('23B91A6212', 'CHINTAPALLI NAGA SYAMALA', '23b91a6212@srkrec.edu.in', '23B91A6212', 'CSD', 'A', 2, 5, 0, NULL, '2025-08-22 16:19:03'),
('23B91A6213', 'CHINTAPALLI PREM TEJA', '23b91a6213@srkrec.edu.in', '23B91A6213', 'CSD', 'A', 2, 3, 0, NULL, '2025-08-22 16:19:03'),
('23B91A6214', 'CHODAGAM SHANMUKHA SIVA SRI VENKAT', '23b91a6214@srkrec.edu.in', '23B91A6214', 'CSD', 'A', 2, 4, 0, NULL, '2025-08-22 16:19:03'),
('23B91A6215', 'DAGGU ROHITH SUBRAHMANYA SAI', '23b91a6215@srkrec.edu.in', '23B91A6215', 'CSD', 'A', 2, 5, 0, NULL, '2025-08-22 16:19:03'),
('23B91A6216', 'DODDI NIVEDITHA', '23b91a6216@srkrec.edu.in', '23B91A6216', 'CSD', 'A', 2, 5, 0, NULL, '2025-08-22 16:19:03'),
('23B91A6217', 'DODDIPATLA POOJA SAI PRAVEENA', '23b91a6217@srkrec.edu.in', '23B91A6217', 'CSD', 'A', 2, 4, 0, NULL, '2025-08-22 16:19:03'),
('23B91A6218', 'DONTHU VIJAYA SRI', '23b91a6218@srkrec.edu.in', '23B91A6218', 'CSD', 'A', 2, 5, 0, NULL, '2025-08-22 16:19:03'),
('23B91A6219', 'GADDAM MANOJ KUMAR', '23b91a6219@srkrec.edu.in', '23B91A6219', 'CSD', 'A', 2, 4, 0, NULL, '2025-08-22 16:19:03'),
('23B91A6220', 'GANDROJU ESWAR SRI KALI KRISHNA', '23b91a6220@srkrec.edu.in', '23B91A6220', 'CSD', 'A', 2, 4, 0, NULL, '2025-08-22 16:19:03'),
('23B91A6221', 'GANTA HARSHINI', '23b91a6221@srkrec.edu.in', '23B91A6221', 'CSD', 'A', 2, 2, 0, NULL, '2025-08-22 16:19:03'),
('23B91A6222', 'GEDDAM JACINTHA', '23b91a6222@srkrec.edu.in', '23B91A6222', 'CSD', 'A', 2, 5, 0, NULL, '2025-08-22 16:19:03'),
('23B91A6223', 'GUBBALA RESHMA GANGAVATHI', '23b91a6223@srkrec.edu.in', '23B91A6223', 'CSD', 'A', 2, 1, 0, NULL, '2025-08-22 16:19:03'),
('23B91A6224', 'GUMMALLA NAGA GAYATHRI', '23b91a6224@srkrec.edu.in', '23B91A6224', 'CSD', 'A', 2, 1, 0, NULL, '2025-08-22 16:19:03'),
('23B91A6225', 'INDIGIMELLI RESHMA SUDEEPA', '23b91a6225@srkrec.edu.in', '23B91A6225', 'CSD', 'A', 2, 4, 0, NULL, '2025-08-22 16:19:03'),
('23B91A6226', 'JILLELA VINAY', '23b91a6226@srkrec.edu.in', '23B91A6226', 'CSD', 'A', 2, 4, 0, NULL, '2025-08-22 16:19:03'),
('23B91A6227', 'JONNALAGADDA LAKSHMI MOUNIKA', '23b91a6227@srkrec.edu.in', '23B91A6227', 'CSD', 'A', 2, 1, 0, NULL, '2025-08-22 16:19:03'),
('23B91A6228', 'KALIGITA SIDDHU', '23b91a6228@srkrec.edu.in', '23B91A6228', 'CSD', 'A', 2, 3, 0, NULL, '2025-08-22 16:19:03'),
('23B91A6229', 'KARATAM SANTHOSH KUMAR', '23b91a6229@srkrec.edu.in', '23B91A6229', 'CSD', 'A', 2, 1, 0, NULL, '2025-08-22 16:19:03'),
('23B91A6230', 'KARIMERAKA DOLLY GANYA', '23b91a6230@srkrec.edu.in', '23B91A6230', 'CSD', 'A', 2, 5, 0, NULL, '2025-08-22 16:19:03'),
('23B91A6231', 'KARRI REVANTH RATAN REDDY', '23b91a6231@srkrec.edu.in', '23B91A6231', 'CSD', 'A', 2, 2, 0, NULL, '2025-08-22 16:19:03'),
('23B91A6232', 'KARUMANCHI SUNEEL', '23b91a6232@srkrec.edu.in', '23B91A6232', 'CSD', 'A', 2, 4, 0, NULL, '2025-08-22 16:19:03'),
('23B91A6233', 'KOLLATI SAILAJA', '23b91a6233@srkrec.edu.in', '23B91A6233', 'CSD', 'A', 2, 3, 0, NULL, '2025-08-22 16:19:03'),
('23B91A6234', 'KOLLEPARA PREM', '23b91a6234@srkrec.edu.in', '23B91A6234', 'CSD', 'A', 2, 4, 0, NULL, '2025-08-22 16:19:03'),
('23B91A6235', 'KUKKALA SUDHEERA', '23b91a6235@srkrec.edu.in', '23B91A6235', 'CSD', 'A', 2, 2, 0, NULL, '2025-08-22 16:19:03'),
('23B91A6236', 'KUSAMPUDI VENKATA SATYA SAI TEJAS VARMA', '23b91a6236@srkrec.edu.in', '23B91A6236', 'CSD', 'A', 2, 4, 0, NULL, '2025-08-22 16:19:03'),
('23B91A6237', 'MADABHUSHI SRI RANGA SUDARSAN', '23b91a6237@srkrec.edu.in', '23B91A6237', 'CSD', 'A', 2, 2, 0, NULL, '2025-08-22 16:19:03'),
('23B91A6238', 'MAMIDISETTI VASUDHA BHANU', '23b91a6238@srkrec.edu.in', '23B91A6238', 'CSD', 'A', 2, 2, 0, NULL, '2025-08-22 16:19:03'),
('23B91A6239', 'MANCHALA SHANMUKA LAKSHMI DEEPIKA', '23b91a6239@srkrec.edu.in', '23B91A6239', 'CSD', 'A', 2, 4, 0, NULL, '2025-08-22 16:19:03'),
('23B91A6240', 'MANDA TANMAY VENKATA SAI LALA GUPTA', '23b91a6240@srkrec.edu.in', '23B91A6240', 'CSD', 'A', 2, 4, 0, NULL, '2025-08-22 16:19:03'),
('23B91A6241', 'MANGENA SAI VENKATA VENU GOPALA CHARAN', '23b91a6241@srkrec.edu.in', '23B91A6241', 'CSD', 'A', 2, 4, 0, NULL, '2025-08-22 16:19:03'),
('23B91A6242', 'MATTAPARTHI REETHIKA', '23b91a6242@srkrec.edu.in', '23B91A6242', 'CSD', 'A', 2, 5, 0, NULL, '2025-08-22 16:19:03'),
('23B91A6243', 'MEESALA KARTHIK RAJ KUMAR', '23b91a6243@srkrec.edu.in', '23B91A6243', 'CSD', 'A', 2, 1, 0, NULL, '2025-08-22 16:19:03'),
('23B91A6244', 'MOHAMMAD IBRAHIM KHAN', '23b91a6244@srkrec.edu.in', '23B91A6244', 'CSD', 'A', 2, 1, 0, NULL, '2025-08-22 16:19:03'),
('23B91A6245', 'MUCHU MAHADEV', '23b91a6245@srkrec.edu.in', '23B91A6245', 'CSD', 'A', 2, 2, 0, NULL, '2025-08-22 16:19:03'),
('23B91A6246', 'MURALA NEETHI SURYA', '23b91a6246@srkrec.edu.in', '23B91A6246', 'CSD', 'A', 2, 3, 0, NULL, '2025-08-22 16:19:03'),
('23B91A6247', 'NAKKA SUNISCHAL', '23b91a6247@srkrec.edu.in', '23B91A6247', 'CSD', 'A', 2, 3, 0, NULL, '2025-08-22 16:19:03'),
('23B91A6248', 'NOUPADA LIKHITHA', '23b91a6248@srkrec.edu.in', '23B91A6248', 'CSD', 'A', 2, 5, 0, NULL, '2025-08-22 16:19:03'),
('23B91A6249', 'NUKALA CHARAN JASWANTH', '23b91a6249@srkrec.edu.in', '23B91A6249', 'CSD', 'A', 2, 3, 0, NULL, '2025-08-22 16:19:03'),
('23B91A6250', 'NUKALA KAUSHAL', '23b91a6250@srkrec.edu.in', '23B91A6250', 'CSD', 'A', 2, 3, 0, NULL, '2025-08-22 16:19:03'),
('23B91A6251', 'NUKALA NAGA HARSHINI', '23b91a6251@srkrec.edu.in', '23B91A6251', 'CSD', 'A', 2, 2, 0, NULL, '2025-08-22 16:19:03'),
('23B91A6252', 'PABOLU SAI HARSHA', '23b91a6252@srkrec.edu.in', '23B91A6252', 'CSD', 'A', 2, 2, 0, NULL, '2025-08-22 16:19:03'),
('23B91A6253', 'PAREPALLI RAMA HARI NAIDU', '23b91a6253@srkrec.edu.in', '23B91A6253', 'CSD', 'A', 2, 1, 0, NULL, '2025-08-22 16:19:03'),
('23B91A6254', 'PERICHARLA HEMA ASWANI', '23b91a6254@srkrec.edu.in', '23B91A6254', 'CSD', 'A', 2, 2, 0, NULL, '2025-08-22 16:19:03'),
('23B91A6255', 'POLIMERA SWAPNA', '23b91a6255@srkrec.edu.in', '23B91A6255', 'CSD', 'A', 2, 4, 0, NULL, '2025-08-22 16:19:03'),
('23B91A6256', 'RELLU LAKSHMI PRASANNA', '23b91a6256@srkrec.edu.in', '23B91A6256', 'CSD', 'A', 2, 4, 0, NULL, '2025-08-22 16:19:03'),
('23B91A6257', 'RUDRAKSHULA PRAVEENA', '23b91a6257@srkrec.edu.in', '23B91A6257', 'CSD', 'A', 2, 1, 0, NULL, '2025-08-22 16:19:03'),
('23B91A6258', 'SARELLA VINCY ANGELINE', '23b91a6258@srkrec.edu.in', '23B91A6258', 'CSD', 'A', 2, 5, 0, NULL, '2025-08-22 16:19:03'),
('23B91A6259', 'SHAIK ILIYAS', '23b91a6259@srkrec.edu.in', '23B91A6259', 'CSD', 'A', 2, 4, 0, NULL, '2025-08-22 16:19:03'),
('23B91A6260', 'SURARAPU HASINI', '23b91a6260@srkrec.edu.in', '23B91A6260', 'CSD', 'A', 2, 1, 0, NULL, '2025-08-22 16:19:03'),
('23B91A6261', 'SYED MANSOOR', '23b91a6261@srkrec.edu.in', '23B91A6261', 'CSD', 'A', 2, 1, 0, NULL, '2025-08-22 16:19:03'),
('23B91A6262', 'THIRUMALARAJU VENKATA SATYA PAVAN RAJU', '23b91a6262@srkrec.edu.in', '23B91A6262', 'CSD', 'A', 4, 2, 0, NULL, '2025-08-22 16:19:03'),
('23B91A6263', 'YALLA CHANDANA', '23b91a6263@srkrec.edu.in', '23B91A6263', 'CSD', 'A', 2, 5, 0, NULL, '2025-08-22 16:19:03'),
('23B91A6264', 'YARAMALA MOHAN BHAGAVAN NARASIMHA', '23b91a6264@srkrec.edu.in', '23B91A6264', 'CSD', 'A', 2, 1, 0, NULL, '2025-08-22 16:19:03'),
('23B91A7237', 'MADABHUSHI SRI RANGA SUDARSAN ', '23b91a7237@srkrec.edu.in', '23B91A7237', 'CSD', 'A', 2, 1, 0, NULL, '2025-09-26 10:16:33'),
('23B95A6201', 'ANDE NAGA SATYA SAI VAMSI KIRAN', '23b95a6201@srkrec.edu.in', '23B95A6201', 'CSD', 'A', 1, 4, 0, NULL, '2025-08-22 16:19:03'),
('23B95A6202', 'GUTTULA TEJASWI', '23b95a6202@srkrec.edu.in', '23B95A6202', 'CSD', 'A', 1, 3, 0, NULL, '2025-08-22 16:19:03'),
('23B95A6203', 'KELLA CHAKRA VAMSI', '23b95a6203@srkrec.edu.in', '23B95A6203', 'CSD', 'A', 1, 2, 0, NULL, '2025-08-22 16:19:03'),
('23B95A6204', 'MADDULA AAKASH NAGENDRA SAI PAVAN', '23b95a6204@srkrec.edu.in', '23B95A6204', 'CSD', 'A', 1, 4, 0, NULL, '2025-08-22 16:19:03'),
('23B95A6205', 'MOHAMMAD SIKINDAR KHAN', '23b95a6205@srkrec.edu.in', '23B95A6205', 'CSD', 'A', 1, 2, 0, NULL, '2025-08-22 16:19:03'),
('23B95A6206', 'NAKKINA GANESH', '23b95a6206@srkrec.edu.in', '23B95A6206', 'CSD', 'A', 1, 1, 0, NULL, '2025-08-22 16:19:03'),
('23B95A6207', 'TANGUTURI S V NAGA PAVAN SAI', '23b95a6207@srkrec.edu.in', '23B95A6207', 'CSD', 'A', 1, 3, 0, NULL, '2025-08-22 16:19:03'),
('23B95A6208', 'THOTA SUJAY BABU', '23b95a6208@srkrec.edu.in', '23B95A6208', 'CSD', 'A', 1, 4, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0701', 'A PREETHI', '24b91a0701@srkrec.edu.in', '24B91A0701', 'CSIT', 'A', 5, 2, 0, 'uploads/profile_pictures/24B91A0701_1757571497.jpg', '2025-08-22 16:19:03'),
('24B91A0702', 'ACHANTA MOKSHITH CHOWDARY', '24b91a0702@srkrec.edu.in', '24B91A0702', 'CSIT', 'A', 5, 4, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0703', 'ADDAGARLA HEMANTH NAGA MANIKANTA', '24b91a0703@srkrec.edu.in', '24B91A0703', 'CSIT', 'A', 5, 2, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0704', 'ADDAGARLA R S S K V V S D N RAJESH', '24b91a0704@srkrec.edu.in', '24B91A0704', 'CSIT', 'A', 5, 3, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0705', 'ALLADI DILEEP KUMAR', '24b91a0705@srkrec.edu.in', '24B91A0705', 'CSIT', 'A', 5, 3, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0706', 'ATCHUTHUNI SAI SPURANTHI', '24b91a0706@srkrec.edu.in', '24B91A0706', 'CSIT', 'A', 5, 3, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0707', 'BANAVATHU MALLIKARJUNA SAI', '24b91a0707@srkrec.edu.in', '24B91A0707', 'CSIT', 'A', 5, 4, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0708', 'BANDE DALI AKSHAYA', '24b91a0708@srkrec.edu.in', '24B91A0708', 'CSIT', 'A', 5, 5, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0709', 'BANDI HARI KRISHNA', '24b91a0709@srkrec.edu.in', '24B91A0709', 'CSIT', 'A', 5, 1, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0710', 'BASIVIREDDY HEMALATHA', '24b91a0710@srkrec.edu.in', '24B91A0710', 'CSIT', 'A', 5, 1, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0711', 'BHOGIREDDY TEJASRI SAI VAISHNAVI', '24b91a0711@srkrec.edu.in', '24B91A0711', 'CSIT', 'A', 5, 2, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0712', 'BODDETI SARVANI', '24b91a0712@srkrec.edu.in', '24B91A0712', 'CSIT', 'A', 5, 5, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0713', 'BOPPINEEDI GEETHIKA', '24b91a0713@srkrec.edu.in', '24B91A0713', 'CSIT', 'A', 5, 4, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0714', 'BUDDIGA GAYATRI', '24b91a0714@srkrec.edu.in', '24B91A0714', 'CSIT', 'A', 5, 5, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0715', 'BUDITHI SAI ADARSH', '24b91a0715@srkrec.edu.in', '24B91A0715', 'CSIT', 'A', 5, 2, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0716', 'CHALLA JITHENDRA ABHIRAM', '24b91a0716@srkrec.edu.in', '24B91A0716', 'CSIT', 'A', 5, NULL, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0717', 'CHALLAGUNDLA HINDRIKA SRI', '24b91a0717@srkrec.edu.in', '24B91A0717', 'CSIT', 'A', 5, 1, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0718', 'CHAMARLAKOTA SIREESH VALI', '24b91a0718@srkrec.edu.in', '24B91A0718', 'CSIT', 'A', 5, 3, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0719', 'CHANDAKA KEDARA SRINIVAS', '24b91a0719@srkrec.edu.in', '24B91A0719', 'CSIT', 'A', 5, NULL, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0720', 'CHANDANI VIVEKANANDA', '24b91a0720@srkrec.edu.in', '24B91A0720', 'CSIT', 'A', 5, 2, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0721', 'CHINTAPALLI VENKATA DURGESH', '24b91a0721@srkrec.edu.in', '24B91A0721', 'CSIT', 'A', 5, NULL, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0722', 'CHITAKANA RACHITHA', '24b91a0722@srkrec.edu.in', '24B91A0722', 'CSIT', 'A', 5, 3, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0723', 'CHITTALA DILEEP RAM KUMAR', '24b91a0723@srkrec.edu.in', '24B91A0723', 'CSIT', 'A', 5, 5, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0724', 'CHUNDRU GOWTHAM KRISHNA', '24b91a0724@srkrec.edu.in', '24B91A0724', 'CSIT', 'A', 5, 2, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0725', 'DACHEPALLI BHANU UDAY', '24b91a0725@srkrec.edu.in', '24B91A0725', 'CSIT', 'A', 5, 2, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0726', 'DAMMU PRANEETH KUMAR', '24b91a0726@srkrec.edu.in', '24B91A0726', 'CSIT', 'A', 5, 5, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0727', 'DEVADA SRI VENKATESWARA SWAMY', '24b91a0727@srkrec.edu.in', '24B91A0727', 'CSIT', 'A', 5, 4, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0728', 'DHANANI SRI LAKSHMI VENKATA AASHRITA', '24b91a0728@srkrec.edu.in', '24B91A0728', 'CSIT', 'A', 5, 3, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0729', 'ESURU CHAITANYA', '24b91a0729@srkrec.edu.in', '24B91A0729', 'CSIT', 'A', 5, NULL, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0730', 'EUDU HARSHA VARDHAN', '24b91a0730@srkrec.edu.in', '24B91A0730', 'CSIT', 'A', 5, 5, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0731', 'GADDAMUDI VENKATA GOPICHAND', '24b91a0731@srkrec.edu.in', '24B91A0731', 'CSIT', 'A', 5, NULL, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0732', 'GANDHAM MAHATHI', '24b91a0732@srkrec.edu.in', '24B91A0732', 'CSIT', 'A', 5, 4, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0733', 'GANTA GOWTHAM', '24b91a0733@srkrec.edu.in', '24B91A0733', 'CSIT', 'A', 5, 1, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0734', 'GAYATRI PADHI', '24b91a0734@srkrec.edu.in', '24B91A0734', 'CSIT', 'A', 5, 3, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0735', 'GHANTA LIKITHA VENKATA RAGHU SAI', '24b91a0735@srkrec.edu.in', '24B91A0735', 'CSIT', 'A', 5, 3, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0736', 'GOPINEEDI DIVIJA', '24b91a0736@srkrec.edu.in', '24B91A0736', 'CSIT', 'A', 5, 5, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0737', 'GUDIMETLA JNANA SANDEEP REDDY', '24b91a0737@srkrec.edu.in', '24B91A0737', 'CSIT', 'A', 5, NULL, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0738', 'GUNDU TARUN SAI', '24b91a0738@srkrec.edu.in', '24B91A0738', 'CSIT', 'A', 5, 2, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0739', 'JADDU LEELA PAVAN KRISHNA', '24b91a0739@srkrec.edu.in', '24B91A0739', 'CSIT', 'A', 5, 5, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0740', 'JANAKI MADDALA', '24b91a0740@srkrec.edu.in', '24B91A0740', 'CSIT', 'A', 5, 3, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0741', 'KANDIBOYINA CHANDRASHEKAR', '24b91a0741@srkrec.edu.in', '24B91A0741', 'CSIT', 'A', 5, 3, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0742', 'KANUMURI DEEKSHITA', '24b91a0742@srkrec.edu.in', '24B91A0742', 'CSIT', 'A', 5, 3, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0743', 'KAPAKAYALA NAGA SAI PAVAN', '24b91a0743@srkrec.edu.in', '24B91A0743', 'CSIT', 'A', 5, NULL, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0744', 'KARIBANDI PAVAN RAVINDRA KUMAR', '24b91a0744@srkrec.edu.in', '24B91A0744', 'CSIT', 'A', 5, 1, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0745', 'KATRAGADDA ARJUN NAIDU', '24b91a0745@srkrec.edu.in', '24B91A0745', 'CSIT', 'A', 5, 5, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0746', 'KAVURU GUNA SRAVANI', '24b91a0746@srkrec.edu.in', '24B91A0746', 'CSIT', 'A', 5, 3, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0747', 'KAYITHA LAHARI', '24b91a0747@srkrec.edu.in', '24B91A0747', 'CSIT', 'A', 5, NULL, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0748', 'KESANAKURTHI MANASA SATYA', '24b91a0748@srkrec.edu.in', '24B91A0748', 'CSIT', 'A', 5, 1, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0749', 'KETA PURNA PAVAN', '24b91a0749@srkrec.edu.in', '24B91A0749', 'CSIT', 'A', 5, 1, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0750', 'KODETI SATISH', '24b91a0750@srkrec.edu.in', '24B91A0750', 'CSIT', 'A', 5, 5, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0751', 'KODI HEMANTH KUMAR', '24b91a0751@srkrec.edu.in', '24B91A0751', 'CSIT', 'A', 5, 4, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0752', 'KOLLI VINEEL', '24b91a0752@srkrec.edu.in', '24B91A0752', 'CSIT', 'A', 5, 4, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0753', 'KOMATI JAYASRI LAKSHMI', '24b91a0753@srkrec.edu.in', '24B91A0753', 'CSIT', 'A', 5, 2, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0754', 'KONDAPALLI SUBHAKAR BHANCY RAJ', '24b91a0754@srkrec.edu.in', '24B91A0754', 'CSIT', 'A', 5, 3, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0755', 'KONKEY BINDHU VASANTHI', '24b91a0755@srkrec.edu.in', '24B91A0755', 'CSIT', 'A', 5, 4, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0756', 'KOTLA VENKAT', '24b91a0756@srkrec.edu.in', '24B91A0756', 'CSIT', 'A', 5, NULL, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0757', 'KUSUMA KOMALI', '24b91a0757@srkrec.edu.in', '24B91A0757', 'CSIT', 'A', 5, 1, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0758', 'MADAMANCHI MANIKANTA', '24b91a0758@srkrec.edu.in', '24B91A0758', 'CSIT', 'A', 5, NULL, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0759', 'MADDALA VARSHINI', '24b91a0759@srkrec.edu.in', '24B91A0759', 'CSIT', 'A', 5, 4, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0760', 'MADUPALLI JNANESH', '24b91a0760@srkrec.edu.in', '24B91A0760', 'CSIT', 'A', 5, 4, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0761', 'MALLA DEEPANVITHA', '24b91a0761@srkrec.edu.in', '24B91A0761', 'CSIT', 'A', 5, 1, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0762', 'MALLAVARAPU GANGOTHRI', '24b91a0762@srkrec.edu.in', '24B91A0762', 'CSIT', 'A', 5, 3, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0763', 'MALLULA KAVERI', '24b91a0763@srkrec.edu.in', '24B91A0763', 'CSIT', 'A', 5, 2, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0764', 'MALLULA MADHU VARSHINI', '24b91a0764@srkrec.edu.in', '24B91A0764', 'CSIT', 'A', 5, 4, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0765', 'MANDA KEERTHI', '24b91a0765@srkrec.edu.in', '24B91A0765', 'CSIT', 'A', 5, NULL, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0766', 'MANDAGIRI SAI ASWITHA', '24b91a0766@srkrec.edu.in', '24B91A0766', 'CSIT', 'A', 5, 1, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0767', 'MANDAPATI VENKATA YAMINI', '24b91a0767@srkrec.edu.in', '24B91A0767', 'CSIT', 'A', 5, 3, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0768', 'MANDAVA YAGNA AKHIL SAI', '24b91a0768@srkrec.edu.in', '24B91A0768', 'CSIT', 'A', 5, 2, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0769', 'MANDAVALLI DHANA KARTHIKEYA', '24b91a0769@srkrec.edu.in', '24B91A0769', 'CSIT', 'A', 5, NULL, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0770', 'MARUBOINA KARTHIK VENKATA SRI SAI TEJA', '24b91a0770@srkrec.edu.in', '24B91A0770', 'CSIT', 'A', 5, NULL, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0771', 'MEDABALIMI ADITHYA VARDHAN', '24b91a0771@srkrec.edu.in', '24B91A0771', 'CSIT', 'A', 5, 3, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0772', 'MEDIDI LALITH KUMAR', '24b91a0772@srkrec.edu.in', '24B91A0772', 'CSIT', 'A', 5, 2, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0773', 'MEDISETTI SRINIJA', '24b91a0773@srkrec.edu.in', '24B91A0773', 'CSIT', 'B', 6, 4, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0774', 'MULAGALA PRANATI SANDHYA', '24b91a0774@srkrec.edu.in', '24B91A0774', 'CSIT', 'B', 6, 4, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0775', 'MURIKITHA ARCHANA SAI SRI', '24b91a0775@srkrec.edu.in', '24B91A0775', 'CSIT', 'B', 6, 3, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0776', 'NALAMALA KEVIN RISHITH', '24b91a0776@srkrec.edu.in', '24B91A0776', 'CSIT', 'B', 6, NULL, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0777', 'NAMALA THANUSHA', '24b91a0777@srkrec.edu.in', '24B91A0777', 'CSIT', 'B', 6, 4, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0778', 'NELLURI CHAITRIKA SRI NIDHI', '24b91a0778@srkrec.edu.in', '24B91A0778', 'CSIT', 'B', 6, 3, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0779', 'NETHALA HEMA DURGA SAI KUMAR', '24b91a0779@srkrec.edu.in', '24B91A0779', 'CSIT', 'B', 6, NULL, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0780', 'NETHULA MAHESH', '24b91a0780@srkrec.edu.in', '24B91A0780', 'CSIT', 'B', 6, 5, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0781', 'NIMMALA BHANU SRI HARSHA', '24b91a0781@srkrec.edu.in', '24B91A0781', 'CSIT', 'B', 6, 3, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0782', 'NIMMALA BHUVANA LAKSHMI', '24b91a0782@srkrec.edu.in', '24B91A0782', 'CSIT', 'B', 6, 2, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0783', 'NULI LAKSHMI SAI LIKITH', '24b91a0783@srkrec.edu.in', '24B91A0783', 'CSIT', 'B', 6, 4, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0784', 'OGURI LAKSHMI NARAYANA', '24b91a0784@srkrec.edu.in', '24B91A0784', 'CSIT', 'B', 6, 3, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0785', 'PAKA RENITA JESSIE', '24b91a0785@srkrec.edu.in', '24B91A0785', 'CSIT', 'B', 6, NULL, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0786', 'PALANI BHUVANA SAI KRUTHI', '24b91a0786@srkrec.edu.in', '24B91A0786', 'CSIT', 'B', 6, 1, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0787', 'PALAPARTHI SANTHOSH KUMAR', '24b91a0787@srkrec.edu.in', '24B91A0787', 'CSIT', 'B', 6, 2, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0788', 'PALLAPU HARITHA', '24b91a0788@srkrec.edu.in', '24B91A0788', 'CSIT', 'B', 6, 1, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0789', 'PANJA SOMARANGA SAI', '24b91a0789@srkrec.edu.in', '24B91A0789', 'CSIT', 'B', 6, 2, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0790', 'PARAVASTU VENKATA RAMA SURI', '24b91a0790@srkrec.edu.in', '24B91A0790', 'CSIT', 'B', 6, 1, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0791', 'PENMETSA HARSHINI', '24b91a0791@srkrec.edu.in', '24B91A0791', 'CSIT', 'B', 6, 3, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0792', 'PONNAGANTI JYOTHIKA SAI', '24b91a0792@srkrec.edu.in', '24B91A0792', 'CSIT', 'B', 6, 5, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0793', 'POTLA RAVI', '24b91a0793@srkrec.edu.in', '24B91A0793', 'CSIT', 'B', 6, NULL, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0794', 'PULAPARTHI KALYAN VENKATA SAI', '24b91a0794@srkrec.edu.in', '24B91A0794', 'CSIT', 'B', 6, 3, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0795', 'PULI MYTHILI', '24b91a0795@srkrec.edu.in', '24B91A0795', 'CSIT', 'B', 6, 4, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0796', 'PUVVALA SANJANA GAYATHRI', '24b91a0796@srkrec.edu.in', '24B91A0796', 'CSIT', 'B', 6, 2, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0797', 'RANGISETTI SAI PAVAN KUMAR', '24b91a0797@srkrec.edu.in', '24B91A0797', 'CSIT', 'B', 6, NULL, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0798', 'REDDEM LEELA MEGHANA', '24b91a0798@srkrec.edu.in', '24B91A0798', 'CSIT', 'B', 6, 4, 0, NULL, '2025-08-22 16:19:03'),
('24B91A0799', 'REDDY VENKATA SATYA SRAVANI', '24b91a0799@srkrec.edu.in', '24B91A0799', 'CSIT', 'B', 6, 1, 0, NULL, '2025-08-22 16:19:03'),
('24B91A07A0', 'ROMPILLI SATEESH', '24b91a07a0@srkrec.edu.in', '24B91A07A0', 'CSIT', 'B', 6, NULL, 0, NULL, '2025-08-22 16:19:03'),
('24B91A07A1', 'RONGALA SRINIVAS', '24b91a07a1@srkrec.edu.in', '24B91A07A1', 'CSIT', 'B', 6, 2, 0, NULL, '2025-08-22 16:19:03'),
('24B91A07A2', 'ROTTE SUSHANTH', '24b91a07a2@srkrec.edu.in', '24B91A07A2', 'CSIT', 'B', 6, 3, 0, NULL, '2025-08-22 16:19:03'),
('24B91A07A3', 'SAKHINETIPALLI CHAKRI ADITYA PAVAN KUMAR', '24b91a07a3@srkrec.edu.in', '24B91A07A3', 'CSIT', 'B', 6, 3, 0, NULL, '2025-08-22 16:19:03'),
('24B91A07A4', 'SAMUDRALA JESRAVAN MANIKANTA', '24b91a07a4@srkrec.edu.in', '24B91A07A4', 'CSIT', 'B', 6, 2, 0, NULL, '2025-08-22 16:19:03'),
('24B91A07A5', 'SANA SHANMUKHA DURGA', '24b91a07a5@srkrec.edu.in', '24B91A07A5', 'CSIT', 'B', 6, 3, 0, NULL, '2025-08-22 16:19:03'),
('24B91A07A6', 'SEELABOYINA JEEVANA', '24b91a07a6@srkrec.edu.in', '24B91A07A6', 'CSIT', 'B', 6, 2, 0, NULL, '2025-08-22 16:19:03'),
('24B91A07A7', 'SEELABOYINA JEEVIKA', '24b91a07a7@srkrec.edu.in', '24B91A07A7', 'CSIT', 'B', 6, 4, 0, NULL, '2025-08-22 16:19:03'),
('24B91A07A8', 'SHAIK ABDUL GAFOOR', '24b91a07a8@srkrec.edu.in', '24B91A07A8', 'CSIT', 'B', 6, NULL, 0, NULL, '2025-08-22 16:19:03'),
('24B91A07A9', 'SHAIK AMEENA', '24b91a07a9@srkrec.edu.in', '24B91A07A9', 'CSIT', 'B', 6, 5, 0, NULL, '2025-08-22 16:19:03'),
('24B91A07B0', 'SHAIK NAGUR MADEENA BEGAM', '24b91a07b0@srkrec.edu.in', '24B91A07B0', 'CSIT', 'B', 6, 3, 0, NULL, '2025-08-22 16:19:03'),
('24B91A07B1', 'SIDAGAM ABHIRAM', '24b91a07b1@srkrec.edu.in', '24B91A07B1', 'CSIT', 'B', 6, NULL, 0, NULL, '2025-08-22 16:19:03'),
('24B91A07B2', 'SIDDAMSETTI VIVEK SAI', '24b91a07b2@srkrec.edu.in', '24B91A07B2', 'CSIT', 'B', 6, 3, 0, NULL, '2025-08-22 16:19:03'),
('24B91A07B3', 'SIRRA DURGA RANI', '24b91a07b3@srkrec.edu.in', '24B91A07B3', 'CSIT', 'B', 6, 1, 0, NULL, '2025-08-22 16:19:03'),
('24B91A07B4', 'SWARNA GOWTHAMI', '24b91a07b4@srkrec.edu.in', '24B91A07B4', 'CSIT', 'B', 6, 3, 0, NULL, '2025-08-22 16:19:03'),
('24B91A07B5', 'SWARNA SAHITHI', '24b91a07b5@srkrec.edu.in', '24B91A07B5', 'CSIT', 'B', 6, 1, 0, NULL, '2025-08-22 16:19:03'),
('24B91A07B6', 'TALARI JYOTHI', '24b91a07b6@srkrec.edu.in', '24B91A07B6', 'CSIT', 'B', 6, NULL, 0, NULL, '2025-08-22 16:19:03'),
('24B91A07B7', 'THOTA JOHAN BENEDICT', '24b91a07b7@srkrec.edu.in', '24B91A07B7', 'CSIT', 'B', 6, 5, 0, NULL, '2025-08-22 16:19:03'),
('24B91A07B8', 'TIRUMALASETTY SIDDARDHA', '24b91a07b8@srkrec.edu.in', '24B91A07B8', 'CSIT', 'B', 6, NULL, 0, NULL, '2025-08-22 16:19:03'),
('24B91A07B9', 'UPPALA ABHINAYA SREE', '24b91a07b9@srkrec.edu.in', '24B91A07B9', 'CSIT', 'B', 6, 4, 0, NULL, '2025-08-22 16:19:03'),
('24B91A07BO', 'Shaik madeena', '24b91a07bo@srkrec.edu.in', '24B91A07BO', 'CSIT', 'A', 5, 1, 0, NULL, '2025-09-26 10:16:33'),
('24B91A07C0', 'VADREVU LAHARI DEVI', '24b91a07c0@srkrec.edu.in', '24B91A07C0', 'CSIT', 'B', 6, 3, 0, NULL, '2025-08-22 16:19:03'),
('24B91A07C1', 'VALAVALA RAMA LAKSHMI ANJANA', '24b91a07c1@srkrec.edu.in', '24B91A07C1', 'CSIT', 'B', 6, 2, 0, NULL, '2025-08-22 16:19:03'),
('24B91A07C2', 'VANAPARTHI ASMITHA VYSHNAVI', '24b91a07c2@srkrec.edu.in', '24B91A07C2', 'CSIT', 'B', 6, 5, 0, NULL, '2025-08-22 16:19:03'),
('24B91A07C3', 'VASE ASHITHA', '24b91a07c3@srkrec.edu.in', '24B91A07C3', 'CSIT', 'B', 6, NULL, 0, NULL, '2025-08-22 16:19:03'),
('24B91A07C4', 'VASKA JYOTHI', '24b91a07c4@srkrec.edu.in', '24B91A07C4', 'CSIT', 'B', 6, 5, 0, NULL, '2025-08-22 16:19:03'),
('24B91A07C5', 'VATHADI NAGAVINAY', '24b91a07c5@srkrec.edu.in', '24B91A07C5', 'CSIT', 'B', 6, NULL, 0, NULL, '2025-08-22 16:19:03'),
('24B91A07C6', 'VATTIVELLA RAMKI', '24b91a07c6@srkrec.edu.in', '24B91A07C6', 'CSIT', 'B', 6, 4, 0, NULL, '2025-08-22 16:19:03'),
('24B91A07C7', 'VENKATA NISHITHA REDDY DATLA', '24b91a07c7@srkrec.edu.in', '24B91A07C7', 'CSIT', 'B', 6, 5, 0, NULL, '2025-08-22 16:19:03'),
('24B91A07C8', 'YALLAPU TANUJA', '24b91a07c8@srkrec.edu.in', '24B91A07C8', 'CSIT', 'B', 6, 5, 0, NULL, '2025-08-22 16:19:03'),
('24B91A07C9', 'YARLAGADDA TAMOGHNA', '24b91a07c9@srkrec.edu.in', '24B91A07C9', 'CSIT', 'B', 6, 2, 0, NULL, '2025-08-22 16:19:03'),
('24B91A07D0', 'YENDA RASHMIKA', '24b91a07d0@srkrec.edu.in', '24B91A07D0', 'CSIT', 'B', 6, 4, 0, NULL, '2025-08-22 16:19:03'),
('24B91A07D1', 'YERRA YASVASI SATYA KAVERI', '24b91a07d1@srkrec.edu.in', '24B91A07D1', 'CSIT', 'B', 6, 4, 0, NULL, '2025-08-22 16:19:03'),
('24B91A6201', 'ALLURI BHUVAN SAI TEJA MANI VARMA', '24b91a6201@srkrec.edu.in', '24B91A6201', 'CSD', 'A', 4, 4, 0, NULL, '2025-08-22 16:19:03'),
('24B91A6202', 'ASILETI JAHNAVI', '24b91a6202@srkrec.edu.in', '24B91A6202', 'CSD', 'A', 4, 4, 0, NULL, '2025-08-22 16:19:03'),
('24B91A6203', 'BOGA NISHANTH', '24b91a6203@srkrec.edu.in', '24B91A6203', 'CSD', 'A', 4, 4, 0, NULL, '2025-08-22 16:19:03'),
('24B91A6204', 'BOKINALA MANJUSHA', '24b91a6204@srkrec.edu.in', '24B91A6204', 'CSD', 'A', 4, 3, 0, NULL, '2025-08-22 16:19:03'),
('24B91A6205', 'BOMMI VENKATA SAI', '24b91a6205@srkrec.edu.in', '24B91A6205', 'CSD', 'A', 4, 3, 0, NULL, '2025-08-22 16:19:03'),
('24B91A6206', 'BONAM ADI LAKSHAMMA', '24b91a6206@srkrec.edu.in', '24B91A6206', 'CSD', 'A', 4, 5, 0, NULL, '2025-08-22 16:19:03'),
('24B91A6207', 'BURRA MANI CHANDU KUTA RAO', '24b91a6207@srkrec.edu.in', '24B91A6207', 'CSD', 'A', 4, 3, 0, NULL, '2025-08-22 16:19:03'),
('24B91A6208', 'CHINNAM LAKSHMI SANTHOSHI', '24b91a6208@srkrec.edu.in', '24B91A6208', 'CSD', 'A', 4, 4, 0, NULL, '2025-08-22 16:19:03'),
('24B91A6209', 'CHINNAM NIKHILESH', '24b91a6209@srkrec.edu.in', '24B91A6209', 'CSD', 'A', 4, 3, 0, NULL, '2025-08-22 16:19:03'),
('24B91A6210', 'CHOKKA ARYAN SANTHOSH', '24b91a6210@srkrec.edu.in', '24B91A6210', 'CSD', 'A', 4, 1, 0, NULL, '2025-08-22 16:19:03'),
('24B91A6211', 'DIRISIMILLI MAHI AVINASH', '24b91a6211@srkrec.edu.in', '24B91A6211', 'CSD', 'A', 4, 4, 0, NULL, '2025-08-22 16:19:03'),
('24B91A6212', 'DODDIPATLA DANA VENKATA SIVASANKAR', '24b91a6212@srkrec.edu.in', '24B91A6212', 'CSD', 'A', 4, 5, 0, NULL, '2025-08-22 16:19:03'),
('24B91A6213', 'EDA PRASANTH', '24b91a6213@srkrec.edu.in', '24B91A6213', 'CSD', 'A', 4, 3, 0, NULL, '2025-08-22 16:19:03'),
('24B91A6214', 'EDIMUDI SURIBABU', '24b91a6214@srkrec.edu.in', '24B91A6214', 'CSD', 'A', 4, NULL, 0, NULL, '2025-08-22 16:19:03'),
('24B91A6215', 'EVANA CHANDU VENKATA SAI GANESH', '24b91a6215@srkrec.edu.in', '24B91A6215', 'CSD', 'A', 4, 2, 0, NULL, '2025-08-22 16:19:03'),
('24B91A6216', 'GUNTAMUKKALA SHAILESH', '24b91a6216@srkrec.edu.in', '24B91A6216', 'CSD', 'A', 4, 4, 0, NULL, '2025-08-22 16:19:03'),
('24B91A6217', 'GURRAM VIKAS', '24b91a6217@srkrec.edu.in', '24B91A6217', 'CSD', 'A', 4, 4, 0, NULL, '2025-08-22 16:19:03'),
('24B91A6218', 'GUTTULA CHAITANYA AKSHAY', '24b91a6218@srkrec.edu.in', '24B91A6218', 'CSD', 'A', 4, 4, 0, NULL, '2025-08-22 16:19:03'),
('24B91A6219', 'JAKKAMPUDI JAHNAVI', '24b91a6219@srkrec.edu.in', '24B91A6219', 'CSD', 'A', 4, 4, 0, NULL, '2025-08-22 16:19:03'),
('24B91A6220', 'KACHETTI RUCHITA LAKSHMI', '24b91a6220@srkrec.edu.in', '24B91A6220', 'CSD', 'A', 4, 5, 0, NULL, '2025-08-22 16:19:03'),
('24B91A6221', 'KADALI BHANU', '24b91a6221@srkrec.edu.in', '24B91A6221', 'CSD', 'A', 4, 1, 0, NULL, '2025-08-22 16:19:03'),
('24B91A6222', 'KILLADA DAVID ENOSH', '24b91a6222@srkrec.edu.in', '24B91A6222', 'CSD', 'A', 4, 3, 0, NULL, '2025-08-22 16:19:03'),
('24B91A6223', 'KOLLATI SAGAR', '24b91a6223@srkrec.edu.in', '24B91A6223', 'CSD', 'A', 4, NULL, 0, NULL, '2025-08-22 16:19:03'),
('24B91A6224', 'KOPPARTHI DURGA BHAVANI', '24b91a6224@srkrec.edu.in', '24B91A6224', 'CSD', 'A', 4, 4, 0, NULL, '2025-08-22 16:19:03'),
('24B91A6225', 'KUNCHE SRI NAGA GANESH', '24b91a6225@srkrec.edu.in', '24B91A6225', 'CSD', 'A', 4, 5, 0, NULL, '2025-08-22 16:19:03'),
('24B91A6226', 'KUTIKUPPALA CHARAN TEJA', '24b91a6226@srkrec.edu.in', '24B91A6226', 'CSD', 'A', 4, 1, 0, NULL, '2025-08-22 16:19:03'),
('24B91A6227', 'LALITHA MANOJNA VELIVELA', '24b91a6227@srkrec.edu.in', '24B91A6227', 'CSD', 'A', 4, 3, 0, NULL, '2025-08-22 16:19:03'),
('24B91A6228', 'MALLABATTULA SIVA KRISHNA', '24b91a6228@srkrec.edu.in', '24B91A6228', 'CSD', 'A', 4, 5, 0, NULL, '2025-08-22 16:19:03'),
('24B91A6229', 'MOHAMMAD ROOFIYA TASNEEM', '24b91a6229@srkrec.edu.in', '24B91A6229', 'CSD', 'A', 4, 1, 0, NULL, '2025-08-22 16:19:03'),
('24B91A6230', 'MORTHA ANUSRI', '24b91a6230@srkrec.edu.in', '24B91A6230', 'CSD', 'A', 4, 1, 0, NULL, '2025-08-22 16:19:03'),
('24B91A6231', 'MUNDRI RAKESH', '24b91a6231@srkrec.edu.in', '24B91A6231', 'CSD', 'A', 4, 3, 0, NULL, '2025-08-22 16:19:03'),
('24B91A6232', 'NADIMPALLI BABAJI AMRUTHA VARMA', '24b91a6232@srkrec.edu.in', '24B91A6232', 'CSD', 'A', 4, 4, 0, NULL, '2025-08-22 16:19:03'),
('24B91A6233', 'NAGISETTY VISHNUVARDHAN', '24b91a6233@srkrec.edu.in', '24B91A6233', 'CSD', 'A', 4, 5, 0, NULL, '2025-08-22 16:19:03'),
('24B91A6234', 'NALLA TANOJ SITHARAM', '24b91a6234@srkrec.edu.in', '24B91A6234', 'CSD', 'A', 4, NULL, 0, NULL, '2025-08-22 16:19:03'),
('24B91A6235', 'NAMUDURI MAHESH', '24b91a6235@srkrec.edu.in', '24B91A6235', 'CSD', 'A', 4, 1, 0, NULL, '2025-08-22 16:19:03'),
('24B91A6236', 'NANDIKA LIKHITHA', '24b91a6236@srkrec.edu.in', '24B91A6236', 'CSD', 'A', 4, 3, 0, NULL, '2025-08-22 16:19:03'),
('24B91A6237', 'NANDURI SURYA NAGA VENKATA SAI VIGNESH', '24b91a6237@srkrec.edu.in', '24B91A6237', 'CSD', 'A', 4, 1, 0, NULL, '2025-08-22 16:19:03'),
('24B91A6238', 'NARISETTY AKSHAYA NAIDU', '24b91a6238@srkrec.edu.in', '24B91A6238', 'CSD', 'A', 4, 3, 0, NULL, '2025-08-22 16:19:03'),
('24B91A6239', 'NELAPOGULA SRI POSI LAKSHMI', '24b91a6239@srkrec.edu.in', '24B91A6239', 'CSD', 'A', 4, 5, 0, NULL, '2025-08-22 16:19:03'),
('24B91A6240', 'PABBINEEDI SRI RAMA SATYA MAHESH', '24b91a6240@srkrec.edu.in', '24B91A6240', 'CSD', 'A', 4, 2, 0, NULL, '2025-08-22 16:19:03'),
('24B91A6241', 'PADAVALA GANIF RAJU', '24b91a6241@srkrec.edu.in', '24B91A6241', 'CSD', 'A', 4, NULL, 0, NULL, '2025-08-22 16:19:03'),
('24B91A6242', 'PAIDI TANUJA', '24b91a6242@srkrec.edu.in', '24B91A6242', 'CSD', 'A', 4, 1, 0, NULL, '2025-08-22 16:19:03'),
('24B91A6243', 'PALIVELA BALA BHASKARA PRADEEP', '24b91a6243@srkrec.edu.in', '24B91A6243', 'CSD', 'A', 4, 1, 0, NULL, '2025-08-22 16:19:03'),
('24B91A6244', 'PANKAJ NARAYAN TYADA', '24b91a6244@srkrec.edu.in', '24B91A6244', 'CSD', 'A', 4, 5, 0, NULL, '2025-08-22 16:19:03'),
('24B91A6245', 'PENAPOTHU JOHARIKA', '24b91a6245@srkrec.edu.in', '24B91A6245', 'CSD', 'A', 4, 2, 0, NULL, '2025-08-22 16:19:03'),
('24B91A6246', 'PENMETSA PUJITH NAGA SANJAY VARMA', '24b91a6246@srkrec.edu.in', '24B91A6246', 'CSD', 'A', 4, 2, 0, NULL, '2025-08-22 16:19:03'),
('24B91A6247', 'PERICHERLA VIGNESH VARMA', '24b91a6247@srkrec.edu.in', '24B91A6247', 'CSD', 'A', 4, 1, 0, NULL, '2025-08-22 16:19:03'),
('24B91A6248', 'PERURI V V S L VINAY', '24b91a6248@srkrec.edu.in', '24B91A6248', 'CSD', 'A', 4, 2, 0, NULL, '2025-08-22 16:19:03'),
('24B91A6249', 'PILLI MEGHANA', '24b91a6249@srkrec.edu.in', '24B91A6249', 'CSD', 'A', 4, 1, 0, NULL, '2025-08-22 16:19:03'),
('24B91A6250', 'PONNALA VAISHNAVI PRIYADARSHINI', '24b91a6250@srkrec.edu.in', '24B91A6250', 'CSD', 'A', 4, 2, 0, NULL, '2025-08-22 16:19:03'),
('24B91A6251', 'POTHINEEDI TEJA NAGA VENKATA SAI PAVAN', '24b91a6251@srkrec.edu.in', '24B91A6251', 'CSD', 'A', 4, 2, 0, NULL, '2025-08-22 16:19:03'),
('24B91A6252', 'PULIDINDI BLOOMY CHRIS ANGEL', '24b91a6252@srkrec.edu.in', '24B91A6252', 'CSD', 'A', 4, 4, 0, NULL, '2025-08-22 16:19:03'),
('24B91A6253', 'SALUMURI JYOTHI', '24b91a6253@srkrec.edu.in', '24B91A6253', 'CSD', 'A', 4, 2, 0, NULL, '2025-08-22 16:19:03'),
('24B91A6254', 'SANKU VEERA VENKATA SANTOSH', '24b91a6254@srkrec.edu.in', '24B91A6254', 'CSD', 'A', 4, NULL, 0, NULL, '2025-08-22 16:19:03');
INSERT INTO `students` (`student_id`, `name`, `email`, `password`, `branch`, `section`, `class_id`, `hid`, `is_alumni`, `profile_picture`, `created_at`) VALUES
('24B91A6255', 'SAYED AMEENA FIRDOUS', '24b91a6255@srkrec.edu.in', '24B91A6255', 'CSD', 'A', 4, 2, 0, NULL, '2025-08-22 16:19:03'),
('24B91A6256', 'SHAIK SANIYA BEGUM', '24b91a6256@srkrec.edu.in', '24B91A6256', 'CSD', 'A', 4, 2, 0, NULL, '2025-08-22 16:19:03'),
('24B91A6257', 'SIDDA MAHESH', '24b91a6257@srkrec.edu.in', '24B91A6257', 'CSD', 'A', 4, 5, 0, NULL, '2025-08-22 16:19:03'),
('24B91A6258', 'SINGAMSETTI SAI SHANKAR', '24b91a6258@srkrec.edu.in', '24B91A6258', 'CSD', 'A', 4, 2, 0, NULL, '2025-08-22 16:19:03'),
('24B91A6259', 'VASIMTHA SATYA SAI KALYANI MALLAPAREDY', '24b91a6259@srkrec.edu.in', '24B91A6259', 'CSD', 'A', 4, 2, 0, NULL, '2025-08-22 16:19:03'),
('24B91A6260', 'VEERAVALLI LEELA NAGA BABU', '24b91a6260@srkrec.edu.in', '24B91A6260', 'CSD', 'A', 4, 1, 0, NULL, '2025-08-22 16:19:03'),
('24B91A6261', 'VUNNAM RAVINDRA BABU', '24b91a6261@srkrec.edu.in', '24B91A6261', 'CSD', 'A', 4, NULL, 0, NULL, '2025-08-22 16:19:03'),
('24B91A6262', 'YENUGAPALLI DIVYA MADHURI', '24b91a6262@srkrec.edu.in', '24B91A6262', 'CSD', 'A', 4, 2, 0, NULL, '2025-08-22 16:19:03'),
('24B95A0701', 'BANDARU MANOGNA NAGAVALLI', '24b95a0701@srkrec.edu.in', '24B95A0701', 'CSIT', 'A', 3, 1, 0, NULL, '2025-08-22 16:19:03'),
('24B95A0702', 'BOLLEDDU GIRIDHARA VENKATA SAI', '24b95a0702@srkrec.edu.in', '24B95A0702', 'CSIT', 'A', 3, 2, 0, NULL, '2025-08-22 16:19:03'),
('24B95A0703', 'CHINIMILLI SAJEEVUDU', '24b95a0703@srkrec.edu.in', '24B95A0703', 'CSIT', 'A', 3, 3, 0, NULL, '2025-08-22 16:19:03'),
('24B95A0704', 'CHIRAPA ESWAR VENKATA SATYA NARAYANA', '24b95a0704@srkrec.edu.in', '24B95A0704', 'CSIT', 'A', 3, 3, 0, NULL, '2025-08-22 16:19:03'),
('24B95A0705', 'DANDUBOYINA VENKATA PRABHAS', '24b95a0705@srkrec.edu.in', '24B95A0705', 'CSIT', 'A', 3, 2, 0, NULL, '2025-08-22 16:19:03'),
('24B95A0706', 'GIDUGU NEHANTH SRIHARSHA NAVADEEP', '24b95a0706@srkrec.edu.in', '24B95A0706', 'CSIT', 'A', 3, 3, 0, NULL, '2025-08-22 16:19:03'),
('24B95A0707', 'GONAPALA SRI GOWTHAM', '24b95a0707@srkrec.edu.in', '24B95A0707', 'CSIT', 'A', 3, 2, 0, NULL, '2025-08-22 16:19:03'),
('24B95A0708', 'INDUKURI YASWANTH ACHYUTA VARMA', '24b95a0708@srkrec.edu.in', '24B95A0708', 'CSIT', 'A', 3, 4, 0, NULL, '2025-08-22 16:19:03'),
('24B95A0709', 'ITTA VASAVI', '24b95a0709@srkrec.edu.in', '24B95A0709', 'CSIT', 'A', 3, 1, 0, NULL, '2025-08-22 16:19:03'),
('24B95A0710', 'LINGAMPALLI VIJAY VARDHAN', '24b95a0710@srkrec.edu.in', '24B95A0710', 'CSIT', 'A', 3, 2, 0, NULL, '2025-08-22 16:19:03'),
('24B95A0711', 'MANAPARAPU DEEPIKA', '24b95a0711@srkrec.edu.in', '24B95A0711', 'CSIT', 'A', 3, 1, 0, NULL, '2025-08-22 16:19:03'),
('24B95A0712', 'PEETHANI UDAYA SRI', '24b95a0712@srkrec.edu.in', '24B95A0712', 'CSIT', 'A', 3, 1, 0, NULL, '2025-08-22 16:19:03'),
('24B95A0713', 'PENMETSA SAI ANVESH VARMA', '24b95a0713@srkrec.edu.in', '24B95A0713', 'CSIT', 'A', 3, 2, 0, NULL, '2025-08-22 16:19:03'),
('24B95A0714', 'POTHURI SIVA SAI KRISHNA VARMA', '24b95a0714@srkrec.edu.in', '24B95A0714', 'CSIT', 'A', 3, 4, 0, NULL, '2025-08-22 16:19:03'),
('24B95A6201', 'BOMMIDI JAHNAVI', '24b95a6201@srkrec.edu.in', '24B95A6201', 'CSD', 'A', 2, 1, 0, NULL, '2025-08-22 16:19:03'),
('24B95A6202', 'DONGA JHANSI', '24b95a6202@srkrec.edu.in', '24B95A6202', 'CSD', 'A', 2, 3, 0, NULL, '2025-08-22 16:19:03'),
('24B95A6203', 'KOMARADA KIRAN KISHORE', '24b95a6203@srkrec.edu.in', '24B95A6203', 'CSD', 'A', 2, 3, 0, NULL, '2025-08-22 16:19:03'),
('24B95A6204', 'NADIKUPPALA THANUSH', '24b95a6204@srkrec.edu.in', '24B95A6204', 'CSD', 'A', 2, 4, 0, NULL, '2025-08-22 16:19:03'),
('24B95A6205', 'PENTAKOTA LEELA SRI', '24b95a6205@srkrec.edu.in', '24B95A6205', 'CSD', 'A', 2, 3, 0, NULL, '2025-08-22 16:19:03'),
('24B95A6206', 'PONAMANDI PRASHANTH', '24b95a6206@srkrec.edu.in', '24B95A6206', 'CSD', 'A', 2, 3, 0, NULL, '2025-08-22 16:19:03'),
('24B95A6207', 'TANUKULA UMA SAI PAVAN', '24b95a6207@srkrec.edu.in', '24B95A6207', 'CSD', 'A', 2, 4, 0, NULL, '2025-08-22 16:19:03'),
('24B95A6208', 'THOTA MOHAN SIVA', '24b95a6208@srkrec.edu.in', '24B95A6208', 'CSD', 'A', 2, 4, 0, NULL, '2025-08-22 16:19:03'),
('25B95A0701', 'BOLEM PRAVALIKA', '25b95a0701@srkrec.edu.in', '25B95A0701', 'CSIT', 'B', 6, NULL, 0, NULL, '2025-11-12 05:09:41'),
('25B95A0702', 'CHEYYETI VENKATA SINDHU', '25b95a0702@srkrec.edu.in', '25B95A0702', 'CSIT', 'B', 6, NULL, 0, NULL, '2025-11-12 05:09:41'),
('25B95A0703', 'DONGA MAHESH', '25b95a0703@srkrec.edu.in', '25B95A0703', 'CSIT', 'B', 6, NULL, 0, NULL, '2025-11-12 05:09:41'),
('25B95A0704', 'GANJI JYOTHSNA', '25b95a0704@srkrec.edu.in', '25B95A0704', 'CSIT', 'B', 6, NULL, 0, NULL, '2025-11-12 05:09:41'),
('25B95A0705', 'MUTHYALAPALLI', '25b95a0705@srkrec.edu.in', '25B95A0705', 'CSIT', 'B', 6, NULL, 0, NULL, '2025-11-12 05:09:41'),
('25B95A0706', 'NIMMANA NARENDRA', '25b95a0706@srkrec.edu.in', '25B95A0706', 'CSIT', 'B', 6, NULL, 0, NULL, '2025-11-12 05:09:41'),
('25B95A0707', 'PANDA SUJAN PRASAD', '25b95a0707@srkrec.edu.in', '25B95A0707', 'CSIT', 'B', 6, NULL, 0, NULL, '2025-11-12 05:09:41'),
('25B95A0708', 'PATAN ABDUL RASHEED KHAN', '25b95a0708@srkrec.edu.in', '25B95A0708', 'CSIT', 'B', 6, NULL, 0, NULL, '2025-11-12 05:09:41'),
('25B95A0709', 'REBBA RAJESH', '25b95a0709@srkrec.edu.in', '25B95A0709', 'CSIT', 'B', 6, NULL, 0, NULL, '2025-11-12 05:09:41'),
('25B95A0710', 'SARIPALLI GNANESWAR', '25b95a0710@srkrec.edu.in', '25B95A0710', 'CSIT', 'B', 6, NULL, 0, NULL, '2025-11-12 05:09:41'),
('25B95A0711', 'TUMMA NAGA DURGA', '25b95a0711@srkrec.edu.in', '25B95A0711', 'CSIT', 'B', 6, NULL, 0, NULL, '2025-11-12 05:09:41'),
('25B95A0712', 'TUMMALAGUNTA SAHITHI LAKSHMI', '25b95a0712@srkrec.edu.in', '25B95A0712', 'CSIT', 'B', 6, NULL, 0, NULL, '2025-11-12 05:09:41'),
('25B95A0713', 'UNDRAJAVARAPU NAGA VENKATA RAGHU', '25b95a0713@srkrec.edu.in', '25B95A0713', 'CSIT', 'B', 6, NULL, 0, NULL, '2025-11-12 05:09:41'),
('25B95A6201', 'GEDDAM NIHAR', '25b95a6201@srkrec.edu.in', '25B95A6201', 'CSD', 'A', 4, NULL, 0, NULL, '2025-11-12 05:09:41'),
('25B95A6202', 'JOGA GOWRI DEEPIKA', '25b95a6202@srkrec.edu.in', '25B95A6202', 'CSD', 'A', 4, NULL, 0, NULL, '2025-11-12 05:09:41'),
('25B95A6203', 'KAMUJU RAMYADEEPTHI', '25b95a6203@srkrec.edu.in', '25B95A6203', 'CSD', 'A', 4, NULL, 0, NULL, '2025-11-12 05:09:41'),
('25B95A6204', 'MAMIDIPALLI NAGA VIGNESH', '25b95a6204@srkrec.edu.in', '25B95A6204', 'CSD', 'A', 4, NULL, 0, NULL, '2025-11-12 05:09:41'),
('25B95A6205', 'MERLA ASRITHA RAM', '25b95a6205@srkrec.edu.in', '25B95A6205', 'CSD', 'A', 4, NULL, 0, NULL, '2025-11-12 05:09:41'),
('25B95A6206', 'MULLU SRINU', '25b95a6206@srkrec.edu.in', '25B95A6206', 'CSD', 'A', 4, NULL, 0, NULL, '2025-11-12 05:09:41'),
('25B95A6207', 'PEPETI THARUN', '25b95a6207@srkrec.edu.in', '25B95A6207', 'CSD', 'A', 4, NULL, 0, NULL, '2025-11-12 05:09:41'),
('25B95A6208', 'PULAVARTHI MOHANA MADHU LASYA SRI', '25b95a6208@srkrec.edu.in', '25B95A6208', 'CSD', 'A', 4, NULL, 0, NULL, '2025-11-12 05:09:41'),
('25B95A6209', 'TELUKULA SAGAR', '25b95a6209@srkrec.edu.in', '25B95A6209', 'CSD', 'A', 4, NULL, 0, NULL, '2025-11-12 05:09:41'),
('25B95A6210', 'YALAKAPATI SURESH', '25b95a6210@srkrec.edu.in', '25B95A6210', 'CSD', 'A', 4, NULL, 0, NULL, '2025-11-12 05:09:41');

-- --------------------------------------------------------

--
-- Table structure for table `student_attendance`
--

CREATE TABLE `student_attendance` (
  `id` int(11) NOT NULL,
  `attendance_date` date NOT NULL,
  `session` enum('Forenoon','Afternoon') NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `status` enum('Present','Absent') NOT NULL DEFAULT 'Present',
  `faculty_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `modification_reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_attendance`
--

INSERT INTO `student_attendance` (`id`, `attendance_date`, `session`, `student_id`, `status`, `faculty_id`, `created_at`, `updated_at`, `modification_reason`) VALUES
(0, '2025-12-01', 'Forenoon', '23B91A0701', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '23B91A0702', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '23B91A0703', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '23B91A0704', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '23B91A0705', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '23B91A0706', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '23B91A0707', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '23B91A0708', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '23B91A0709', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '23B91A0710', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '23B91A0711', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '23B91A0712', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '23B91A0713', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '23B91A0714', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '23B91A0715', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '23B91A0716', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '23B91A0717', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '23B91A0718', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '23B91A0719', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '23B91A0720', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '23B91A0721', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '23B91A0722', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '23B91A0723', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '23B91A0724', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '23B91A0725', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '23B91A0726', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '23B91A0727', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '23B91A0728', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '23B91A0729', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '23B91A0730', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '23B91A0731', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '23B91A0732', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '23B91A0733', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '23B91A0734', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '23B91A0735', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '23B91A0736', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '23B91A0737', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '23B91A0738', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '23B91A0739', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '23B91A0740', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '23B91A0741', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '23B91A0742', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '23B91A0743', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '23B91A0744', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '23B91A0745', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '23B91A0746', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '23B91A0747', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '23B91A0748', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '23B91A0749', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '23B91A0750', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '23B91A0751', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '23B91A0752', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '23B91A0753', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '23B91A0754', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '23B91A0755', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '23B91A0756', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '23B91A0757', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '23B91A0758', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '24B95A0701', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '24B95A0702', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '24B95A0703', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '24B95A0704', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '24B95A0705', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '24B95A0706', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '24B95A0707', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '24B95A0708', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '24B95A0709', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '24B95A0710', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '24B95A0711', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '24B95A0712', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '24B95A0713', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-01', 'Forenoon', '24B95A0714', 'Present', 8, '2025-12-01 04:00:37', '2025-12-01 04:00:42', NULL),
(0, '2025-12-15', 'Afternoon', '23B91A0701', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '23B91A0702', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '23B91A0703', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '23B91A0704', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '23B91A0705', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '23B91A0706', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '23B91A0707', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '23B91A0708', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '23B91A0709', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '23B91A0710', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '23B91A0711', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '23B91A0712', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '23B91A0713', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '23B91A0714', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '23B91A0715', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '23B91A0716', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '23B91A0717', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '23B91A0718', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '23B91A0719', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '23B91A0720', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '23B91A0721', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '23B91A0722', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '23B91A0723', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '23B91A0724', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '23B91A0725', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '23B91A0726', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '23B91A0727', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '23B91A0728', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '23B91A0729', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '23B91A0730', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '23B91A0731', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '23B91A0732', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '23B91A0733', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '23B91A0734', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '23B91A0735', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '23B91A0736', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '23B91A0737', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '23B91A0738', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '23B91A0739', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '23B91A0740', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '23B91A0741', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '23B91A0742', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '23B91A0743', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '23B91A0744', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '23B91A0745', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '23B91A0746', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '23B91A0747', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '23B91A0748', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '23B91A0749', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '23B91A0750', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '23B91A0751', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '23B91A0752', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '23B91A0753', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '23B91A0754', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '23B91A0755', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '23B91A0756', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '23B91A0757', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '23B91A0758', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '24B95A0701', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '24B95A0702', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '24B95A0703', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '24B95A0704', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '24B95A0705', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '24B95A0706', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '24B95A0707', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '24B95A0708', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '24B95A0709', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '24B95A0710', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '24B95A0711', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '24B95A0712', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '24B95A0713', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL),
(0, '2025-12-15', 'Afternoon', '24B95A0714', 'Present', 8, '2025-12-15 04:53:59', '2025-12-15 05:06:30', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `student_class_history`
--

CREATE TABLE `student_class_history` (
  `id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `class_id` int(11) NOT NULL,
  `enrolled_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_personal`
--

CREATE TABLE `student_personal` (
  `student_id` varchar(20) NOT NULL,
  `parent_number` varchar(15) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `blood_group` varchar(10) DEFAULT NULL,
  `dob` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `personal_number` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_personal`
--

INSERT INTO `student_personal` (`student_id`, `parent_number`, `address`, `blood_group`, `dob`, `created_at`, `updated_at`, `personal_number`) VALUES
('23B91A0749', NULL, '', 'O+', '2005-08-13', '2025-09-10 11:22:58', '2025-09-10 11:22:58', '8639081207');

-- --------------------------------------------------------

--
-- Table structure for table `student_profile`
--

CREATE TABLE `student_profile` (
  `student_id` varchar(20) NOT NULL,
  `summary` text DEFAULT NULL,
  `skills` text DEFAULT NULL,
  `social_links` text DEFAULT NULL,
  `projects` text DEFAULT NULL,
  `experience` text DEFAULT NULL,
  `education` text DEFAULT NULL,
  `certifications` text DEFAULT NULL,
  `achievements` text DEFAULT NULL,
  `cgpa` decimal(4,2) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_profile`
--

INSERT INTO `student_profile` (`student_id`, `summary`, `skills`, `social_links`, `projects`, `experience`, `education`, `certifications`, `achievements`, `cgpa`, `profile_picture`, `created_at`, `updated_at`) VALUES
('21B91A6216', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('22B91A6201', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('22B91A6202', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('22B91A6203', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('22B91A6204', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('22B91A6205', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('22B91A6206', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('22B91A6207', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('22B91A6208', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('22B91A6209', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('22B91A6210', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('22B91A6211', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('22B91A6212', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('22B91A6213', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('22B91A6214', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('22B91A6215', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('22B91A6216', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('22B91A6217', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('22B91A6218', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('22B91A6219', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('22B91A6220', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('22B91A6221', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('22B91A6222', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('22B91A6223', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('22B91A6224', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('22B91A6225', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('22B91A6226', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('22B91A6227', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('22B91A6228', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('22B91A6229', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('22B91A6230', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('22B91A6231', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('22B91A6232', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('22B91A6233', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('22B91A6234', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('22B91A6235', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('22B91A6236', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('22B91A6237', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('22B91A6238', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('22B91A6239', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('22B91A6240', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('22B91A6241', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('22B91A6242', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('22B91A6243', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('22B91A6244', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('22B91A6245', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('22B91A6246', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('22B91A6247', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('22B91A6248', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('22B91A6249', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('22B91A6250', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('22B91A6251', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('22B91A6252', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('22B91A6253', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('22B91A6254', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('22B91A6255', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('22B91A6256', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('22B91A6257', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('22B91A6259', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A0701', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A0702', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A0703', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A0704', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A0705', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A0706', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A0707', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A0708', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A0709', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A0710', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A0711', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A0712', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A0713', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A0714', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A0715', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A0716', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A0717', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A0718', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A0719', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A0720', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A0721', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A0722', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A0723', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A0724', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A0725', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A0726', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A0727', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A0728', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A0729', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A0730', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A0731', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A0732', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A0733', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A0734', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A0735', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A0736', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A0737', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A0738', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A0739', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A0740', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A0741', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A0742', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A0743', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A0744', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A0745', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A0746', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A0747', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A0748', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A0749', '', '[\"C\",\"Python\",\"Java\",\"JavaScript\",\"React JS\",\"PHP\",\"MySQL\",\"Tailwind CSS\",\"Bootstrap\",\"MERN\",\"MongoDB\"]', '[\"https:\\/\\/github.com\\/ssantoshhhhh\\/\",\"https:\\/\\/www.linkedin.com\\/in\\/santosh-seelaboina-56b5492b8\\/\",\"https:\\/\\/www.instagram.com\\/ssantoshhhhh\\/\"]', NULL, '[\"Designer and Developer for College Website\"]', NULL, NULL, NULL, 8.32, NULL, '2025-09-04 08:38:09', '2025-10-14 04:45:19'),
('23B91A0750', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A0751', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A0752', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A0753', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A0754', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A0755', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A0756', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A0757', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A0758', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A6201', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A6202', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A6203', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A6204', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A6206', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A6207', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A6208', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A6209', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A6210', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A6211', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A6212', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A6213', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A6214', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A6215', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A6216', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A6217', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A6218', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A6219', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A6220', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A6221', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A6222', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A6223', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A6224', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A6225', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A6226', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A6227', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A6228', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A6229', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A6230', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A6231', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A6232', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A6233', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A6234', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A6235', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A6236', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A6237', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A6238', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A6239', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A6240', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A6241', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A6242', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A6243', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A6244', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A6245', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A6246', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A6247', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A6248', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A6249', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A6250', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A6251', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A6252', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A6253', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A6254', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A6255', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A6256', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A6257', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A6258', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A6259', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A6260', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A6261', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A6262', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A6263', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B91A6264', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B95A6201', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B95A6202', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B95A6203', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B95A6204', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B95A6205', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B95A6206', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B95A6207', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('23B95A6208', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0701', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0702', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0703', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0704', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0705', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0706', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0707', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0708', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0709', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0710', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0711', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0712', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0713', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0714', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0715', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0716', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0717', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0718', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0719', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0720', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0721', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0722', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0723', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0724', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0725', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0726', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0727', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0728', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0729', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0730', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0731', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0732', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0733', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0734', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0735', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0736', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0737', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0738', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0739', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0740', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0741', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0742', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0743', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0744', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0745', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0746', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0747', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0748', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0749', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0750', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0751', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0752', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0753', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0754', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0755', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0756', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0757', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0758', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0759', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0760', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0761', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0762', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0763', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0764', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0765', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0766', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0767', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0768', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0769', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0770', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0771', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0772', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0773', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0774', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0775', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0776', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0777', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0778', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0779', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0780', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0781', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0782', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0783', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0784', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0785', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0786', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0787', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0788', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0789', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0790', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0791', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0792', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0793', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0794', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0795', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0796', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0797', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0798', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A0799', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A07A0', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A07A1', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A07A2', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A07A3', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A07A4', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A07A5', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A07A6', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A07A7', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A07A8', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A07A9', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A07B0', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A07B1', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A07B2', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A07B3', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A07B4', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A07B5', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A07B6', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A07B7', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A07B8', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A07B9', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A07C0', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A07C1', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A07C2', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A07C3', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A07C4', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A07C5', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A07C6', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A07C7', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A07C8', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A07C9', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A07D0', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A07D1', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A6201', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A6202', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A6203', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A6204', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A6205', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A6206', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A6207', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A6208', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A6209', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A6210', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A6211', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A6212', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A6213', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A6214', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A6215', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A6216', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A6217', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A6218', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A6219', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A6220', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A6221', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A6222', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A6223', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A6224', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A6225', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A6226', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A6227', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A6228', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A6229', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A6230', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A6231', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A6232', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A6233', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A6234', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A6235', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A6236', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A6237', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A6238', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A6239', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A6240', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A6241', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A6242', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A6243', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A6244', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A6245', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A6246', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A6247', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A6248', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A6249', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A6250', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A6251', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A6252', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A6253', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A6254', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A6255', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A6256', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A6257', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A6258', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A6259', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A6260', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A6261', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B91A6262', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B95A0701', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B95A0702', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B95A0703', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B95A0704', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B95A0705', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B95A0706', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B95A0707', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B95A0708', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B95A0709', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B95A0710', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B95A0711', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B95A0712', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B95A0713', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B95A0714', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B95A6201', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B95A6202', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B95A6203', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B95A6204', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B95A6205', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B95A6206', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B95A6207', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('24B95A6208', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 08:38:09', '2025-09-04 08:38:09'),
('25B95A0701', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-12 05:09:41', '2025-11-12 05:09:41'),
('25B95A0702', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-12 05:09:41', '2025-11-12 05:09:41'),
('25B95A0703', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-12 05:09:41', '2025-11-12 05:09:41'),
('25B95A0704', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-12 05:09:41', '2025-11-12 05:09:41'),
('25B95A0705', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-12 05:09:41', '2025-11-12 05:09:41'),
('25B95A0706', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-12 05:09:41', '2025-11-12 05:09:41'),
('25B95A0707', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-12 05:09:41', '2025-11-12 05:09:41'),
('25B95A0708', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-12 05:09:41', '2025-11-12 05:09:41'),
('25B95A0709', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-12 05:09:41', '2025-11-12 05:09:41'),
('25B95A0710', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-12 05:09:41', '2025-11-12 05:09:41'),
('25B95A0711', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-12 05:09:41', '2025-11-12 05:09:41'),
('25B95A0712', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-12 05:09:41', '2025-11-12 05:09:41'),
('25B95A0713', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-12 05:09:41', '2025-11-12 05:09:41'),
('25B95A6201', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-12 05:09:41', '2025-11-12 05:09:41'),
('25B95A6202', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-12 05:09:41', '2025-11-12 05:09:41'),
('25B95A6203', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-12 05:09:41', '2025-11-12 05:09:41');
INSERT INTO `student_profile` (`student_id`, `summary`, `skills`, `social_links`, `projects`, `experience`, `education`, `certifications`, `achievements`, `cgpa`, `profile_picture`, `created_at`, `updated_at`) VALUES
('25B95A6204', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-12 05:09:41', '2025-11-12 05:09:41'),
('25B95A6205', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-12 05:09:41', '2025-11-12 05:09:41'),
('25B95A6206', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-12 05:09:41', '2025-11-12 05:09:41'),
('25B95A6207', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-12 05:09:41', '2025-11-12 05:09:41'),
('25B95A6208', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-12 05:09:41', '2025-11-12 05:09:41'),
('25B95A6209', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-12 05:09:41', '2025-11-12 05:09:41'),
('25B95A6210', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-12 05:09:41', '2025-11-12 05:09:41');

-- --------------------------------------------------------

--
-- Table structure for table `winners`
--

CREATE TABLE `winners` (
  `winner_id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `event_id` int(11) NOT NULL,
  `position` tinyint(1) NOT NULL,
  `points` int(11) DEFAULT 0,
  `announced_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `uk_admin_username` (`username`),
  ADD KEY `idx_admins_hid` (`hid`);

--
-- Indexes for table `alumni_employment_history`
--
ALTER TABLE `alumni_employment_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_employment_student` (`student_id`);

--
-- Indexes for table `appreciations`
--
ALTER TABLE `appreciations`
  ADD PRIMARY KEY (`appreciation_id`),
  ADD KEY `idx_appreciations_student` (`student_id`),
  ADD KEY `idx_appreciations_event` (`event_id`),
  ADD KEY `idx_appreciations_created_by` (`created_by`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`class_id`),
  ADD UNIQUE KEY `uk_class_unique` (`academic_year`,`year`,`branch`,`section`,`semester`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`),
  ADD KEY `idx_events_admin` (`admin_id`),
  ADD KEY `idx_events_hid` (`hid`);

--
-- Indexes for table `event_feedback`
--
ALTER TABLE `event_feedback`
  ADD PRIMARY KEY (`feedback_id`),
  ADD UNIQUE KEY `uk_feedback_unique` (`event_id`,`student_id`),
  ADD KEY `idx_feedback_student` (`student_id`);

--
-- Indexes for table `faculties`
--
ALTER TABLE `faculties`
  ADD PRIMARY KEY (`faculty_id`),
  ADD UNIQUE KEY `uk_faculty_email` (`email`),
  ADD KEY `idx_faculties_class` (`class_id`);

--
-- Indexes for table `houses`
--
ALTER TABLE `houses`
  ADD PRIMARY KEY (`hid`),
  ADD UNIQUE KEY `uk_house_name` (`name`);

--
-- Indexes for table `house_admins`
--
ALTER TABLE `house_admins`
  ADD PRIMARY KEY (`house_admin_id`),
  ADD UNIQUE KEY `uk_house_admin` (`hid`);

--
-- Indexes for table `leave_applications`
--
ALTER TABLE `leave_applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_leave_student` (`student_id`),
  ADD KEY `idx_leave_processed_by` (`processed_by`);

--
-- Indexes for table `organizers`
--
ALTER TABLE `organizers`
  ADD PRIMARY KEY (`organizer_id`),
  ADD UNIQUE KEY `uk_organizer_unique` (`student_id`,`event_id`),
  ADD KEY `idx_organizers_event` (`event_id`);

--
-- Indexes for table `participants`
--
ALTER TABLE `participants`
  ADD PRIMARY KEY (`participant_id`),
  ADD UNIQUE KEY `uk_participant_unique` (`student_id`,`event_id`),
  ADD KEY `idx_participants_event` (`event_id`);

--
-- Indexes for table `penalties`
--
ALTER TABLE `penalties`
  ADD PRIMARY KEY (`penalty_id`),
  ADD KEY `idx_penalties_student` (`student_id`),
  ADD KEY `idx_penalties_event` (`event_id`),
  ADD KEY `idx_penalties_created_by` (`created_by`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appreciations`
--
ALTER TABLE `appreciations`
  MODIFY `appreciation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `organizers`
--
ALTER TABLE `organizers`
  MODIFY `organizer_id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
