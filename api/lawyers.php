<?php
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json; charset=utf-8');

function respond(array $data, int $status = 200): never {
    http_response_code($status);
    echo json_encode($data);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') respond(['error' => 'Method not allowed.'], 405);
$input = json_decode(file_get_contents('php://input'), true) ?? [];
$name = trim((string)($input['name'] ?? ''));
if ($name === '' || mb_strlen($name) > 120) respond(['error' => 'Enter a name up to 120 characters.'], 422);

try {
    $pdo = db();
    $find = $pdo->prepare('SELECT id, name FROM lawyers WHERE name = ? LIMIT 1');
    $find->execute([$name]);
    $lawyer = $find->fetch();
    if (!$lawyer) {
        $insert = $pdo->prepare('INSERT INTO lawyers (name) VALUES (?)');
        $insert->execute([$name]);
        $lawyer = ['id' => (int)$pdo->lastInsertId(), 'name' => $name];
    }
    respond(['lawyer' => $lawyer]);
} catch (Throwable $error) {
    respond(['error' => 'Could not open the diary. Import upgrade_multi_lawyer.sql in phpMyAdmin, then try again.'], 500);
}
