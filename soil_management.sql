-- Soil Management System: MySQL database schema
-- Run this file in phpMyAdmin or MySQL Workbench.

CREATE DATABASE IF NOT EXISTS soil_management
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE soil_management;

CREATE TABLE IF NOT EXISTS soil_records (
    soil_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    sample_code VARCHAR(30) NOT NULL,
    farmer_name VARCHAR(100) NOT NULL,
    location VARCHAR(150) NOT NULL,
    soil_type ENUM('Alluvial','Black','Red','Laterite','Sandy','Clayey','Loamy','Other')
        NOT NULL DEFAULT 'Other',
    ph_value DECIMAL(3,1) NOT NULL,
    nitrogen_mg_kg DECIMAL(8,2) NULL,
    phosphorus_mg_kg DECIMAL(8,2) NULL,
    potassium_mg_kg DECIMAL(8,2) NULL,
    moisture_percent DECIMAL(5,2) NULL,
    recommended_crop VARCHAR(120) NULL,
    sample_date DATE NOT NULL,
    notes TEXT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (soil_id),
    UNIQUE KEY uq_sample_code (sample_code),
    KEY idx_location (location),
    KEY idx_sample_date (sample_date),
    CONSTRAINT chk_ph CHECK (ph_value >= 0 AND ph_value <= 14),
    CONSTRAINT chk_moisture CHECK (moisture_percent IS NULL OR (moisture_percent >= 0 AND moisture_percent <= 100)),
    CONSTRAINT chk_nitrogen CHECK (nitrogen_mg_kg IS NULL OR nitrogen_mg_kg >= 0),
    CONSTRAINT chk_phosphorus CHECK (phosphorus_mg_kg IS NULL OR phosphorus_mg_kg >= 0),
    CONSTRAINT chk_potassium CHECK (potassium_mg_kg IS NULL OR potassium_mg_kg >= 0)
);

-- Optional sample data for demonstration. Remove these INSERTs if a clean database is preferred.
INSERT IGNORE INTO soil_records
(sample_code, farmer_name, location, soil_type, ph_value, nitrogen_mg_kg,
 phosphorus_mg_kg, potassium_mg_kg, moisture_percent, recommended_crop, sample_date, notes)
VALUES
('SM-2026-001','Harpreet Singh','Mohali, Punjab','Alluvial',7.2,240,32,180,21.50,'Wheat','2026-10-01','Demonstration record'),
('SM-2026-002','Simran Kaur','Patiala, Punjab','Loamy',6.8,210,28,165,24.00,'Maize','2026-10-05','Demonstration record'),
('SM-2026-003','Raj Kumar','Ludhiana, Punjab','Sandy',6.4,155,20,120,15.00,'Groundnut','2026-10-10','Demonstration record');

-- Useful SQL examples:
-- READ:
-- SELECT * FROM soil_records ORDER BY sample_date DESC;
-- SEARCH:
-- SELECT * FROM soil_records WHERE location LIKE '%Mohali%';
-- UPDATE:
-- UPDATE soil_records SET ph_value = 7.0 WHERE soil_id = 1;
-- DELETE:
-- DELETE FROM soil_records WHERE soil_id = 1;
