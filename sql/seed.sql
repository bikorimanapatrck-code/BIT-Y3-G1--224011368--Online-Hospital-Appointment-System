USE hospital_db;
-- All seeded accounts use the password: password
INSERT INTO users (username,email,password_hash,role) VALUES
('admin','admin@hospital.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','admin'),
('staff1','staff1@hospital.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','staff'),
('patient1','patient1@hospital.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','patient');

INSERT INTO patients (user_id,full_name,dob,gender,phone) VALUES
((SELECT user_id FROM users WHERE username='patient1'),'Alice Uwase','1995-03-12','Female','0788000001'),
(NULL,'Jean Habimana','1988-07-30','Male','0788000002'),
(NULL,'Grace Mukamana','2001-11-05','Female','0788000003');

INSERT INTO doctors (full_name,specialization,phone,department,experience_years) VALUES
('Dr. Eric Nkurunziza','Cardiology','0788100001','Heart Centre',15),
('Dr. Diane Ishimwe','Pediatrics','0788100002','Children Ward',12),
('Dr. Paul Mugisha','General Medicine','0788100003','Outpatient',8);

INSERT INTO appointments (patient_id,doctor_id,appointment_date,status,notes) VALUES
(1,1,'2026-12-01 09:00:00','confirmed','Routine heart check'),
(2,3,'2026-12-01 10:30:00','pending','Persistent cough'),
(3,2,'2026-12-02 14:00:00','pending','Vaccination follow-up');
