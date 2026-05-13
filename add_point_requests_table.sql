-- Migration: Add Point Requests Table
-- This file creates the point_requests table for the Student Point Request System (Phase 1)
-- Run this migration separately from new_sem.sql to avoid modifying the main dump file

CREATE TABLE IF NOT EXISTS `point_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` varchar(20) NOT NULL,
  `student_name` varchar(100) NOT NULL,
  `event_name` varchar(255) NOT NULL,
  `points_requested` int(11) NOT NULL,
  `proof_file` varchar(255) DEFAULT NULL,
  `description` text,
  `status` enum('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending',
  `faculty_remark` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_student_id` (`student_id`),
  KEY `idx_status` (`status`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
