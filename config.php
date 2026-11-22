<?php
// Edit these values to match your local MySQL installation.
const DB_HOST = '127.0.0.1';
const DB_PORT = '3307';
const DB_NAME = 'lawyers_diary';
const DB_USER = 'root';
const DB_PASS = '';

function db(): PDO {
    static $connection = null;
    if ($connection === null) {
        $connection = new PDO(
            'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER,
            DB_PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
        );
    }
    return $connection;
}
