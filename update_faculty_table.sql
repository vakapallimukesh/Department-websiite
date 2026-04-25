-- SQL Script to Update Faculty Table for Multiple Class Support
-- This script modifies the class_id column to support comma-separated values

-- Modify the class_id column to VARCHAR to store comma-separated class IDs
ALTER TABLE `faculties` 
MODIFY COLUMN `class_id` VARCHAR(255) DEFAULT NULL 
COMMENT 'Comma-separated class IDs';

-- Example: Assign multiple classes to a faculty
-- UPDATE faculties SET class_id = '1,2,3' WHERE faculty_id = 1;
-- This assigns classes with IDs 1, 2, and 3 to faculty with ID 1

-- Example: Assign single class to a faculty
-- UPDATE faculties SET class_id = '3' WHERE faculty_id = 14;

-- Example: Remove all class assignments from a faculty
-- UPDATE faculties SET class_id = NULL WHERE faculty_id = 6;

-- View current faculty assignments
SELECT 
    f.faculty_id,
    f.faculty_name,
    f.email,
    f.class_id,
    GROUP_CONCAT(CONCAT(c.year, '/', c.branch, '-', c.section) SEPARATOR ', ') as class_names
FROM faculties f
LEFT JOIN classes c ON FIND_IN_SET(c.class_id, f.class_id) > 0
GROUP BY f.faculty_id, f.faculty_name, f.email, f.class_id
ORDER BY f.faculty_name;
