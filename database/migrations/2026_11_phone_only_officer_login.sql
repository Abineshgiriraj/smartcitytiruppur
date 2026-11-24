-- Smart City Tiruppur: allow REAL phone-only employee accounts.
-- REVIEW IN phpMyAdmin BEFORE EXECUTING. Take a backup first.
-- Run on LOCAL smartcitytiruppur only after assessing the results.
-- Read-only preflight: existing blank emails, duplicated login phones, invalid phones.
SELECT COUNT(*) AS existing_blank_emails FROM users WHERE email='' OR email IS NULL;
SELECT mobile_no, COUNT(*) AS duplicate_accounts
FROM users WHERE mobile_no IS NOT NULL AND mobile_no <> ''
GROUP BY mobile_no HAVING COUNT(*) > 1;
SELECT mobile_no, COUNT(*) AS duplicate_officer_master_phones
FROM officers
WHERE is_active=1 AND mobile_no IS NOT NULL AND mobile_no <> ''
GROUP BY mobile_no HAVING COUNT(*)>1;
SELECT id, officer_name, mobile_no
FROM officers
WHERE is_active=1 AND (mobile_no IS NULL OR mobile_no NOT REGEXP '^[6-9][0-9]{9}$')
ORDER BY id LIMIT 100;
-- Approved schema change: preserve the existing unique-email index.
-- Multiple NULL values are valid under MySQL/MariaDB unique indexes.
ALTER TABLE users MODIFY COLUMN email VARCHAR(250) NULL;
-- Optional strong DB constraint AFTER manually correcting existing duplicate
-- users.mobile_no values. Do not run if duplicate_accounts query returns rows.
-- CREATE UNIQUE INDEX uk_user_mobile ON users (mobile_no);
