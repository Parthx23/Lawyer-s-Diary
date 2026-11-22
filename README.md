# Lawyer's Diary

![Lawyer's Diary banner](assets/lawyers-diary-banner.png)

A small PHP and MySQL project for keeping court hearings in a diary. Enter a lawyer's name to open that lawyer's diary. The first time a name is entered, the app creates a separate diary for it.

## Features

- Separate hearing list for each lawyer
- Add, edit, and delete hearings
- Search and filter by status or date
- Simple open-diary animation
- Prepared MySQL queries with PDO

## Technology

PHP 8.1+, MySQL or MariaDB, HTML5, CSS3, and JavaScript Fetch API (Ajax).

## Run with XAMPP

### New installation

1. Start Apache and MySQL in the XAMPP Control Panel.
2. Copy this folder to `C:\xampp\htdocs\lawyers-diary`.
3. In phpMyAdmin, import `database.sql`.
4. Check the database host, port, user, and password in `config.php`.
5. Open `http://localhost/lawyers-diary/`.

### Upgrade an existing installation

To keep existing diary entries, do this once:

1. Copy the updated project files into `C:\xampp\htdocs\lawyers-diary`.
2. Open phpMyAdmin and select the `lawyers_diary` database.
3. Import `upgrade_multi_lawyer.sql`.
4. Open `http://localhost/lawyers-diary/` and enter `Adv. A. Khanna` to see the existing entries. New lawyer names get their own diary.

The upgrade adds a `lawyers` table and connects existing entries to the default lawyer. It does not delete the existing hearing records. Do not run the upgrade file a second time.

If XAMPP MySQL uses a port other than `3306`, set `DB_PORT` in `config.php` to match. A typical XAMPP setup uses `root` with an empty password; keep the values that work on your computer.

## Project files

- `index.php` — closed diary cover and hearing dashboard
- `config.php` — PDO connection settings
- `database.sql` — database schema and sample data for a new installation
- `upgrade_multi_lawyer.sql` — one-time upgrade for an existing installation
- `api/lawyers.php` — find or create a diary by lawyer name
- `api/entries.php` — list and change hearings for one lawyer
- `assets/app.js` — page animation, Ajax, form, search, and filters
- `assets/style.css` — diary look and responsive layout

## Database design

There are two tables:

- `lawyers`: one row for each diary name.
- `diary_entries`: hearing details, linked to a lawyer by `lawyer_id`.

Each diary request filters by `lawyer_id`, so one lawyer's hearing rows are separated from another's. This student project uses names as a simple selector; it does not include passwords or secure user sign-in.

## How it works

1. Enter a lawyer's name on the closed diary cover.
2. JavaScript sends the name to `api/lawyers.php`. PHP opens an existing diary or creates a new one.
3. The cover animation plays and the app loads that lawyer's entries from `api/entries.php`.
4. Add, edit, or delete actions send JSON with Fetch. PHP runs prepared SQL statements and returns the updated result.

## Viva points

- `lawyers.id` and `diary_entries.id` are primary keys.
- `diary_entries.lawyer_id` is a foreign key to `lawyers.id`.
- The app demonstrates CRUD operations with MySQL.
- PDO prepared statements keep user values separate from SQL.
- Fetch API sends Ajax requests without reloading the page.
