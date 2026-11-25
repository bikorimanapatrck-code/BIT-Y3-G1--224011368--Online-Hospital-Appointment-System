CREATE DATABASE IF NOT EXISTS hospital_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE hospital_db;

DROP TABLE IF EXISTS appointments;
DROP TABLE IF EXISTS hospitals;
DROP TABLE IF EXISTS patients;
DROP TABLE IF EXISTS doctors;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    user_id       INT AUTO_INCREMENT PRIMARY KEY,
    username      VARCHAR(30)  NOT NULL UNIQUE,
    email         VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role          ENUM('admin','staff','patient') NOT NULL DEFAULT 'patient',
    created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE hospitals (
    hospital_id   INT AUTO_INCREMENT PRIMARY KEY,
    hospital_name VARCHAR(150) NOT NULL,
    location      VARCHAR(255) NOT NULL,
    category      VARCHAR(80) NOT NULL,
    services      TEXT NOT NULL,
    phone         VARCHAR(25) NULL,
    building_image VARCHAR(255) NULL,
    is_available  TINYINT(1) NOT NULL DEFAULT 1,
    created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_hospitals_available_name (is_available, hospital_name)
) ENGINE=InnoDB;

CREATE TABLE patients (
    patient_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id    INT NULL UNIQUE,
    full_name  VARCHAR(100) NOT NULL,
    dob        DATE NOT NULL,
    gender     ENUM('Male','Female','Other') NOT NULL,
    phone      VARCHAR(20) NOT NULL,
    CONSTRAINT fk_patient_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE doctors (
    doctor_id      INT AUTO_INCREMENT PRIMARY KEY,
    full_name      VARCHAR(100) NOT NULL,
    specialization VARCHAR(80)  NOT NULL,
    phone          VARCHAR(20)  NOT NULL,
    department     VARCHAR(80)  NOT NULL,
    experience_years SMALLINT UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE appointments (
    appointment_id   INT AUTO_INCREMENT PRIMARY KEY,
    patient_id       INT NOT NULL,
    doctor_id        INT NOT NULL,
    appointment_date DATETIME NOT NULL,
    status           ENUM('pending','confirmed','completed','cancelled') NOT NULL DEFAULT 'pending',
    notes            VARCHAR(255) NULL,
    CONSTRAINT fk_app_patient FOREIGN KEY (patient_id) REFERENCES patients(patient_id) ON DELETE CASCADE,
    CONSTRAINT fk_app_doctor  FOREIGN KEY (doctor_id)  REFERENCES doctors(doctor_id)  ON DELETE CASCADE,
    CONSTRAINT uq_doctor_slot UNIQUE (doctor_id, appointment_date)
) ENGINE=InnoDB;
