<?php
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json; charset=utf-8');

function respond(array $data, int $status = 200): never {
    http_response_code($status);
    echo json_encode($data);
    exit;
}

try {
    $pdo = db();
    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'GET') {
        $lawyerId = (int)($_GET['lawyer_id'] ?? 0);
        if ($lawyerId < 1) respond(['error' => 'Choose a lawyer first.'], 422);
        $statement = $pdo->prepare('SELECT id, client_name, case_title, court_name, hearing_date, hearing_time, status, notes FROM diary_entries WHERE lawyer_id = ? ORDER BY hearing_date ASC, hearing_time ASC');
        $statement->execute([$lawyerId]);
        respond(['entries' => $statement->fetchAll()]);
    }

    if ($method !== 'POST') respond(['error' => 'Method not allowed.'], 405);
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
    $lawyerId = (int)($input['lawyer_id'] ?? 0);
    if ($lawyerId < 1) respond(['error' => 'Choose a lawyer first.'], 422);

    $action = $input['action'] ?? '';
    if ($action === 'delete') {
        $statement = $pdo->prepare('DELETE FROM diary_entries WHERE id = ? AND lawyer_id = ?');
        $statement->execute([(int)($input['id'] ?? 0), $lawyerId]);
        respond(['message' => 'Hearing deleted.']);
    }

    $required = ['client_name', 'case_title', 'court_name', 'hearing_date', 'hearing_time', 'status'];
    foreach ($required as $field) {
        if (trim((string)($input[$field] ?? '')) === '') respond(['error' => 'Please fill in all required fields.'], 422);
    }
    if (!in_array($input['status'], ['Upcoming', 'Completed', 'Adjourned'], true)) {
        respond(['error' => 'Choose a valid status.'], 422);
    }
    $values = [
        trim($input['client_name']), trim($input['case_title']), trim($input['court_name']),
        $input['hearing_date'], $input['hearing_time'], $input['status'], trim($input['notes'] ?? '')
    ];
    if ($action === 'update') {
        $values[] = (int)($input['id'] ?? 0);
        $values[] = $lawyerId;
        $statement = $pdo->prepare('UPDATE diary_entries SET client_name=?, case_title=?, court_name=?, hearing_date=?, hearing_time=?, status=?, notes=? WHERE id=? AND lawyer_id=?');
        $statement->execute($values);
        respond(['message' => 'Hearing updated.']);
    }
    $values[] = $lawyerId;
    $statement = $pdo->prepare('INSERT INTO diary_entries (client_name, case_title, court_name, hearing_date, hearing_time, status, notes, lawyer_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
    $statement->execute($values);
    respond(['message' => 'Hearing added.']);
} catch (Throwable $error) {
    respond(['error' => 'Could not complete the request. Check the database connection.'], 500);
}
