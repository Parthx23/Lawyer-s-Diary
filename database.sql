CREATE DATABASE IF NOT EXISTS lawyers_diary CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE lawyers_diary;

CREATE TABLE IF NOT EXISTS lawyers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT IGNORE INTO lawyers (name) VALUES ('Adv. A. Khanna');
SET @default_lawyer_id = (SELECT id FROM lawyers WHERE name = 'Adv. A. Khanna' LIMIT 1);

CREATE TABLE IF NOT EXISTS diary_entries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lawyer_id INT NOT NULL,
    client_name VARCHAR(120) NOT NULL,
    case_title VARCHAR(180) NOT NULL,
    court_name VARCHAR(160) NOT NULL,
    hearing_date DATE NOT NULL,
    hearing_time TIME NOT NULL,
    status ENUM('Upcoming', 'Completed', 'Adjourned') NOT NULL DEFAULT 'Upcoming',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_diary_entries_lawyer FOREIGN KEY (lawyer_id) REFERENCES lawyers(id) ON DELETE CASCADE
);

INSERT INTO diary_entries (lawyer_id, client_name, case_title, court_name, hearing_date, hearing_time, status, notes) VALUES
(@default_lawyer_id, 'Aarav Mehta', 'Mehta vs Sharma', 'City Civil Court - Court 4', DATE_ADD(CURDATE(), INTERVAL 1 DAY), '10:30:00', 'Upcoming', 'Bring the signed property documents.'),
(@default_lawyer_id, 'Nisha Kapoor', 'Kapoor tenancy petition', 'District Court - Court 2', DATE_ADD(CURDATE(), INTERVAL 3 DAY), '12:00:00', 'Upcoming', 'Review the latest rent receipts.'),
(@default_lawyer_id, 'Rohan Das', 'Das vs State', 'High Court - Court 7', DATE_SUB(CURDATE(), INTERVAL 2 DAY), '11:15:00', 'Completed', 'Arguments concluded; order reserved.');
