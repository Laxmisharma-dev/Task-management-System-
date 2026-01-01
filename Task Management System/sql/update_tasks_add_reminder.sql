-- Add reminder field to tasks table
-- Run this if your database already exists and needs the reminder field

ALTER TABLE tasks ADD COLUMN reminder datetime DEFAULT NULL AFTER due_date;

-- Optional: Add index on reminder for better performance
ALTER TABLE tasks ADD INDEX idx_reminder (reminder);
