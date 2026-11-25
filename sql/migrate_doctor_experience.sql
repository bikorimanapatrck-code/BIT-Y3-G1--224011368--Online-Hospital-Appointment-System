USE hospital_db;

-- Run once on databases created before the doctor experience field was added.
ALTER TABLE doctors
  ADD COLUMN experience_years SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER department;
