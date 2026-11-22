# Briefcase — Lawyer's Diary

A small lawyer's hearing diary built with PHP, MySQL, HTML5, CSS3, and vanilla JavaScript. JavaScript uses the Fetch API for Ajax requests, so changes appear without reloading the page. The project avoids a framework so the database and CRUD flow stay easy to explain.

## Features

- Dashboard counts for upcoming hearings, clients this week, and completed matters
- Add, edit, and delete hearing diary entries
- Search by client, matter, court, or notes
- Filter by status and hearing date
- Prepared SQL statements through PDO

## Run locally

1. Install a local PHP and MySQL bundle such as XAMPP, or install PHP and MySQL separately.
2. Start Apache/PHP and MySQL.
3. Open phpMyAdmin or a MySQL console and import `database.sql`.
4. If needed, edit the database username and password in `config.php`.
5. Put this folder under your local web server's document root (for XAMPP, `htdocs`).
6. Visit `http://localhost/lawyers-diary/`.

The sample records use relative dates so they remain useful whenever the database is imported.

## Project map

- `index.php` — dashboard layout and entry form
- `config.php` — one PDO connection to MySQL
- `database.sql` — database, table, and sample records
- `api/entries.php` — JSON endpoint for listing and CRUD actions
- `assets/app.js` — Ajax calls, filters, and table rendering
- `assets/style.css` — responsive page styles

## Database design

`diary_entries` is the main table. Each row represents one hearing scheduled for a client matter. Its primary key is `id`; required fields describe the client, matter, court, date, time, and status. `notes` stores optional reminders, and `created_at` records when the entry was added.

## How to explain the Ajax flow

1. The browser loads `index.php` and `assets/app.js`.
2. JavaScript requests `api/entries.php` with `GET` to load rows as JSON.
3. When the form is submitted, JavaScript sends JSON using `POST` and an action (`create`, `update`, or `delete`).
4. PHP validates required values and runs a prepared SQL statement through PDO.
5. PHP returns a JSON message; JavaScript refreshes the table without a page reload.

## Suggested viva points

- **DBMS:** MySQL stores structured records in the `diary_entries` table.
- **Primary key:** `id` uniquely identifies each diary entry.
- **CRUD:** Create, read, update, and delete operations are implemented in the API.
- **Prepared statements:** PDO binds values separately from SQL to reduce SQL injection risk.
- **Ajax:** Fetch sends and receives JSON asynchronously, avoiding full-page reloads.
