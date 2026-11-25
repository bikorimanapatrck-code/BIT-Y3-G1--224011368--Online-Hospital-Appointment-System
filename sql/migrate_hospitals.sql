USE hospital_db;

-- Safe to run on an existing database. It creates the admin-managed directory.
CREATE TABLE IF NOT EXISTS hospitals (
    hospital_id   INT AUTO_INCREMENT PRIMARY KEY,
    hospital_name VARCHAR(150) NOT NULL,
    location      VARCHAR(255) NOT NULL,
    category      VARCHAR(80) NOT NULL,
    services      TEXT NOT NULL,
    phone         VARCHAR(25) NULL,
    is_available  TINYINT(1) NOT NULL DEFAULT 1,
    created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_hospitals_available_name (is_available, hospital_name)
) ENGINE=InnoDB;
