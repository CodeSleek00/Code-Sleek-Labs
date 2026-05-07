<?php
declare(strict_types=1);
require_once __DIR__ . '/db.php';

require_method('POST');

$raw = file_get_contents('php://input');
$data = json_decode($raw ?: '[]', true);
if (!is_array($data)) $data = [];

$studentId = (string)($data['student_id'] ?? '');
$date = (string)($data['date'] ?? '');
$time = (string)($data['time'] ?? '');
$session = trim((string)($data['session'] ?? ''));
$status = (string)($data['status'] ?? 'present');

if ($studentId === '' || $date === '' || $time === '') {
  json_out(['ok' => false, 'error' => 'Missing required fields'], 400);
}

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
  json_out(['ok' => false, 'error' => 'Invalid date'], 400);
}

try {
  // Ensure student exists.
  $st = db()->prepare('SELECT id FROM students26 WHERE id = ? LIMIT 1');
  $st->bind_param('s', $studentId);
  $st->execute();
  $st->store_result();
  if ($st->num_rows === 0) json_out(['ok' => false, 'error' => 'Student not found'], 404);

  // Upsert attendance for that day (one record per student per date).
  $stmt = db()->prepare(
    "INSERT INTO attendance (student_id, att_date, att_time, session, status)
     VALUES (?, ?, ?, ?, ?)
     ON DUPLICATE KEY UPDATE att_time = VALUES(att_time), session = VALUES(session), status = VALUES(status)"
  );
  $stmt->bind_param('sssss', $studentId, $date, $time, $session, $status);
  $stmt->execute();

  json_out(['ok' => true]);
} catch (Throwable $e) {
  json_out(['ok' => false, 'error' => 'Failed to mark attendance'], 500);
}

