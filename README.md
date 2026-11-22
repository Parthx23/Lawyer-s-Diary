# Lawyer's Diary

A mini DBMS project for keeping track of a lawyer's court hearings and client matters. It uses PHP and MySQL on the server, with HTML5, CSS3, and JavaScript in the browser. The JavaScript Fetch API sends Ajax requests so diary changes appear without reloading the page.

## Features

- Dashboard with counts for upcoming hearings, clients with hearings this week, and completed matters
- Add, view, edit, and delete diary entries
- Search by client, matter, court, or notes
- Filter entries by status and hearing date
- Responsive layout for desktop and mobile
- PDO prepared statements for database queries

## Technology

- PHP 8 or later
- MySQL or MariaDB
- HTML5 and CSS3
- JavaScript (Fetch API / Ajax)

## Run with XAMPP on Windows

1. Install XAMPP and start **Apache** and **MySQL** from the XAMPP Control Panel.
2. Copy the project folder into `C:\xampp\htdocs\lawyers-diary`.
3. Open `http://localhost/phpmyadmin` in your browser.
4. Select **Import**, choose this project's `database.sql`, and start the import. It creates the `lawyers_diary` database and inserts sample entries.
5. Check `config.php` and set `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, and `DB_PASS` to match your MySQL setup. XAMPP commonly uses port `3306`; if you changed yours, set `DB_PORT` to that port.
6. Visit `http://localhost/lawyers-diary/`.

For a default XAMPP setup, the database user is often `root` with an empty password. Use the credentials configured on your own machine if they differ.

## Project structure

```text
lawyers-diary/
├── api/
│   └── entries.php       # JSON endpoint for reading and changing diary entries
├── assets/
│   ├── app.js            # Ajax, form behavior, search, filters, and rendering
│   └── style.css         # Responsive styling; Lora headings and DM Sans UI text
├── config.php            # MySQL connection settings (PDO)
├── database.sql          # Database, table, and example data
├── index.php             # Dashboard and diary entry form
└── README.md
```

## Database design

The project uses one table named `diary_entries`. Each row represents a hearing in a client matter.

| Column | Purpose |
| --- | --- |
| `id` | Auto-incrementing primary key |
| `client_name` | Client's name |
| `case_title` | Case or matter name |
| `court_name` | Court and courtroom |
| `hearing_date` | Hearing date |
| `hearing_time` | Hearing time |
| `status` | Upcoming, Completed, or Adjourned |
| `notes` | Optional reminders |
| `created_at` | When the row was created |

## How the Ajax flow works

1. `index.php` loads the dashboard and `assets/app.js`.
2. JavaScript sends a `GET` request to `api/entries.php` to load diary rows as JSON.
3. The entry form sends JSON with `POST` and an action: `create`, `update`, or `delete`.
4. PHP validates the submitted values and uses PDO prepared statements to query MySQL.
5. PHP returns a JSON response, then JavaScript refreshes the list and dashboard counts without a full page reload.

## Viva / presentation points

- **DBMS:** MySQL stores the diary entries in a structured table.
- **Primary key:** `id` uniquely identifies each entry.
- **CRUD:** The application supports create, read, update, and delete operations.
- **Prepared statements:** PDO keeps submitted values separate from SQL commands.
- **Ajax:** Fetch exchanges JSON with PHP asynchronously, so the page does not reload after a change.
