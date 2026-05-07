<?php
declare(strict_types=1);
require_once __DIR__ . '/db.php';

require_method('GET');

$date = (string)($_GET['date'] ?? '');
if ($date === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
  json_out(['ok' => false, 'error' => 'Invalid date'], 400);
}

try {
  $stmt = db()->prepare(
    "SELECT a.student_id, a.att_date, a.att_time, a.session, a.status,
            s.name, s.enrollment_id, s.course
     FROM attendance a
     JOIN students26 s ON s.id = a.student_id
     WHERE a.att_date = ?
     ORDER BY a.att_time ASC"
  );
  $stmt->bind_param('s', $date);
  $stmt->execute();
  $res = $stmt->get_result();
  $rows = [];
  while ($row = $res->fetch_assoc()) {
    $rows[] = [
      'student_id' => (string)$row['student_id'],
      'date' => (string)$row['att_date'],
      'time' => (string)$row['att_time'],
      'session' => (string)($row['session'] ?? ''),
      'status' => (string)($row['status'] ?? 'present'),
      'name' => (string)($row['name'] ?? ''),
      'roll' => (string)($row['enrollment_id'] ?? ''),
      'cls' => (string)($row['course'] ?? ''),
    ];
  }
  json_out(['ok' => true, 'records' => $rows]);
} catch (Throwable $e) {
  json_out(['ok' => false, 'error' => 'Failed to load attendance'], 500);
}

