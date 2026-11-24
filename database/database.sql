CREATE DATABASE IF NOT EXISTS db_penilaian_karyawan_native CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_penilaian_karyawan_native;

-- Aman dijalankan ulang: tidak menghapus tabel atau data yang sudah ada.
CREATE TABLE IF NOT EXISTS users (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  email_verified_at TIMESTAMP NULL,
  password VARCHAR(255) NOT NULL,
  role VARCHAR(20) NOT NULL DEFAULT 'employee',
  employee_number VARCHAR(50) NULL UNIQUE,
  position VARCHAR(100) NULL,
  department VARCHAR(100) NULL,
  phone VARCHAR(30) NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'active',
  remember_token VARCHAR(100) NULL,
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_users_role (role)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS periods (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  month TINYINT UNSIGNED NOT NULL,
  year SMALLINT UNSIGNED NOT NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'open',
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY unique_period (month,year)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS kpis (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  employee_id BIGINT UNSIGNED NOT NULL,
  period_id BIGINT UNSIGNED NOT NULL,
  name VARCHAR(255) NOT NULL,
  target DECIMAL(12,2) NOT NULL,
  unit VARCHAR(50) NOT NULL,
  weight DECIMAL(5,2) NOT NULL,
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_kpis_employee_period (employee_id,period_id),
  CONSTRAINT fk_kpis_employee FOREIGN KEY (employee_id) REFERENCES users(id) ON DELETE RESTRICT,
  CONSTRAINT fk_kpis_period FOREIGN KEY (period_id) REFERENCES periods(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS performance_records (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  kpi_id BIGINT UNSIGNED NOT NULL UNIQUE,
  actual DECIMAL(12,2) NULL,
  description TEXT NULL,
  evidence_path VARCHAR(255) NULL,
  rating TINYINT UNSIGNED NULL,
  admin_notes TEXT NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'draft',
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_records_kpi FOREIGN KEY (kpi_id) REFERENCES kpis(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Akun demo. Password seluruh akun: password123. INSERT IGNORE tidak menimpa akun yang sudah ada.
INSERT IGNORE INTO users (name,email,password,role,employee_number,position,department,phone,status) VALUES
('Administrator','admin@company.com','$2y$12$Yc72JJBypHctEnfx83og7Ogm6lZs.Y5ZrDrS67cqVS3xs.HXdqUhS','admin',NULL,'Administrator','Human Resources','081200000001','active'),
('Andi Pratama','andi@company.com','$2y$12$Yc72JJBypHctEnfx83og7Ogm6lZs.Y5ZrDrS67cqVS3xs.HXdqUhS','employee','EMP001','Staff Operasional','Operasional','081200000002','active'),
('Budi Santoso','budi@company.com','$2y$12$Yc72JJBypHctEnfx83og7Ogm6lZs.Y5ZrDrS67cqVS3xs.HXdqUhS','employee','EMP002','Staff Administrasi','Administrasi','081200000003','active');

INSERT IGNORE INTO periods (month,year,status) VALUES (11,2026,'open');

-- KPI demo dibuat hanya jika pasangan karyawan-periode-nama KPI tersebut belum tersedia.
INSERT INTO kpis (employee_id,period_id,name,target,unit,weight)
SELECT u.id,p.id,'Penyelesaian Tugas Tepat Waktu',20,'Tugas',50.00
FROM users u JOIN periods p ON p.month=11 AND p.year=2026
WHERE u.email='andi@company.com' AND NOT EXISTS (SELECT 1 FROM kpis k WHERE k.employee_id=u.id AND k.period_id=p.id AND k.name='Penyelesaian Tugas Tepat Waktu');
INSERT INTO kpis (employee_id,period_id,name,target,unit,weight)
SELECT u.id,p.id,'Kehadiran',22,'Hari',30.00
FROM users u JOIN periods p ON p.month=11 AND p.year=2026
WHERE u.email='andi@company.com' AND NOT EXISTS (SELECT 1 FROM kpis k WHERE k.employee_id=u.id AND k.period_id=p.id AND k.name='Kehadiran');
INSERT INTO kpis (employee_id,period_id,name,target,unit,weight)
SELECT u.id,p.id,'Kualitas Pekerjaan',90,'Persen',20.00
FROM users u JOIN periods p ON p.month=11 AND p.year=2026
WHERE u.email='andi@company.com' AND NOT EXISTS (SELECT 1 FROM kpis k WHERE k.employee_id=u.id AND k.period_id=p.id AND k.name='Kualitas Pekerjaan');
INSERT INTO kpis (employee_id,period_id,name,target,unit,weight)
SELECT u.id,p.id,'Penyelesaian Dokumen',30,'Dokumen',50.00
FROM users u JOIN periods p ON p.month=11 AND p.year=2026
WHERE u.email='budi@company.com' AND NOT EXISTS (SELECT 1 FROM kpis k WHERE k.employee_id=u.id AND k.period_id=p.id AND k.name='Penyelesaian Dokumen');
INSERT INTO kpis (employee_id,period_id,name,target,unit,weight)
SELECT u.id,p.id,'Kehadiran',22,'Hari',30.00
FROM users u JOIN periods p ON p.month=11 AND p.year=2026
WHERE u.email='budi@company.com' AND NOT EXISTS (SELECT 1 FROM kpis k WHERE k.employee_id=u.id AND k.period_id=p.id AND k.name='Kehadiran');
INSERT INTO kpis (employee_id,period_id,name,target,unit,weight)
SELECT u.id,p.id,'Akurasi Data',95,'Persen',20.00
FROM users u JOIN periods p ON p.month=11 AND p.year=2026
WHERE u.email='budi@company.com' AND NOT EXISTS (SELECT 1 FROM kpis k WHERE k.employee_id=u.id AND k.period_id=p.id AND k.name='Akurasi Data');

-- Buat record draft hanya untuk KPI demo yang belum memiliki record.
INSERT INTO performance_records (kpi_id,status)
SELECT k.id,'draft' FROM kpis k JOIN users u ON u.id=k.employee_id
WHERE u.email IN ('andi@company.com','budi@company.com')
AND k.period_id=(SELECT id FROM periods WHERE month=11 AND year=2026 LIMIT 1)
AND k.name IN ('Penyelesaian Tugas Tepat Waktu','Kehadiran','Kualitas Pekerjaan','Penyelesaian Dokumen','Akurasi Data')
AND NOT EXISTS (SELECT 1 FROM performance_records r WHERE r.kpi_id=k.id);
