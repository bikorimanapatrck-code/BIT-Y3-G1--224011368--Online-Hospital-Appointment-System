USE hospital_db;

-- Run once on an existing database created before patient accounts were linked.
ALTER TABLE patients
  ADD COLUMN user_id INT NULL UNIQUE AFTER patient_id,
  ADD CONSTRAINT fk_patient_user FOREIGN KEY (user_id)
    REFERENCES users(user_id) ON DELETE SET NULL;

-- Associate the demo patient login with the first existing patient record.
-- Change this mapping if that account should belong to another patient.
UPDATE patients
SET user_id = (SELECT user_id FROM users WHERE username = 'patient1' LIMIT 1)
WHERE patient_id = (
  SELECT patient_id FROM (SELECT MIN(patient_id) AS patient_id FROM patients) AS first_patient
) AND EXISTS (SELECT user_id FROM users WHERE username = 'patient1');
