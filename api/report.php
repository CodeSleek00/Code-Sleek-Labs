<?php
declare(strict_types=1);
require_once __DIR__ . '/db.php';

require_method('GET');

$from = (string)($_GET['from'] ?? '');
$to = (string)($_GET['to'] ?? '');
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $from) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $to)) {
  json_out(['ok' => false, 'error' => 'Invalid date range'], 400);
}

try {
  $studentsRes = db()->query("SELECT id, name, enrollment_id, course FROM students26 ORDER BY id ASC");
  $students = [];
  while ($s = $studentsRes->fetch_assoc()) {
    $students[] = [
      'id' => (string)$s['id'],
      'name' => (string)($s['name'] ?? ''),
      'roll' => (string)($s['enrollment_id'] ?? ''),
      'cls' => (string)($s['course'] ?? ''),
    ];
  }

  $stmt = db()->prepare(
    "SELECT a.student_id, a.att_date, a.att_time, a.session, a.status,
            s.name, s.enrollment_id, s.course
     FROM attendance a
     JOIN students26 s ON s.id = a.student_id
     WHERE a.att_date BETWEEN ? AND ?
     ORDER BY a.att_date DESC, a.att_time DESC"
  );
  $stmt->bind_param('ss', $from, $to);
  $stmt->execute();
  $res = $stmt->get_result();
  $records = [];
  while ($row = $res->fetch_assoc()) {
    $records[] = [
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

  json_out(['ok' => true, 'students' => $students, 'records' => $records]);
} catch (Throwable $e) {
  json_out(['ok' => false, 'error' => 'Failed to generate report'], 500);
}

