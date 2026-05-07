<?php
declare(strict_types=1);
require_once __DIR__ . '/db.php';

require_method('GET');

try {
  $res = db()->query("SELECT id, name, photo, contact, address, course, enrollment_id, status, created_at FROM students26 ORDER BY id ASC");
  $rows = [];
  while ($row = $res->fetch_assoc()) {
    $rows[] = [
      'id' => (string)$row['id'],
      'name' => (string)($row['name'] ?? ''),
      'photo' => $row['photo'],
      'contact' => $row['contact'],
      'address' => $row['address'],
      'course' => $row['course'],
      'enrollment_id' => $row['enrollment_id'],
      'status' => $row['status'],
      'created_at' => $row['created_at'],
    ];
  }
  json_out(['ok' => true, 'students' => $rows]);
} catch (Throwable $e) {
  json_out(['ok' => false, 'error' => 'Failed to load students'], 500);
}

