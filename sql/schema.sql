-- FaceAttend backend schema (MySQL)
-- Assumes you already have `students26` table.

CREATE TABLE IF NOT EXISTS attendance (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  student_id VARCHAR(64) NOT NULL,
  att_date DATE NOT NULL,
  att_time VARCHAR(16) NOT NULL,
  session VARCHAR(120) NULL,
  status ENUM('present','absent') NOT NULL DEFAULT 'present',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_student_day (student_id, att_date),
  KEY idx_date (att_date),
  CONSTRAINT fk_att_student FOREIGN KEY (student_id) REFERENCES students26(id)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

