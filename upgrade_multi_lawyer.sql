-- Run this once on an existing Lawyer's Diary database to keep current hearings.
USE lawyers_diary;

CREATE TABLE IF NOT EXISTS lawyers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT IGNORE INTO lawyers (name) VALUES ('Adv. A. Khanna');
SET @default_lawyer_id = (SELECT id FROM lawyers WHERE name = 'Adv. A. Khanna' LIMIT 1);

ALTER TABLE diary_entries ADD COLUMN lawyer_id INT NULL AFTER id;
UPDATE diary_entries SET lawyer_id = @default_lawyer_id WHERE lawyer_id IS NULL;
ALTER TABLE diary_entries MODIFY lawyer_id INT NOT NULL;
ALTER TABLE diary_entries ADD CONSTRAINT fk_diary_entries_lawyer FOREIGN KEY (lawyer_id) REFERENCES lawyers(id) ON DELETE CASCADE;
