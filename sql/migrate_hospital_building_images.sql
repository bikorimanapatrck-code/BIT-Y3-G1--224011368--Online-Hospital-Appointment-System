USE hospital_db;

-- Run once after the hospitals table exists.
ALTER TABLE hospitals
  ADD COLUMN building_image VARCHAR(255) NULL AFTER phone;
