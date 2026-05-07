
<?php
declare(strict_types=1);

// Update these for your MySQL server.
$DB_HOST = '127.0.0.1';
$DB_NAME = 'faceattend';
$DB_USER = 'root';
$DB_PASS = '';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

function db(): mysqli {
  static $conn = null;
  if ($conn instanceof mysqli) return $conn;

  global $DB_HOST, $DB_NAME, $DB_USER, $DB_PASS;
  $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
  $conn->set_charset('utf8mb4');
  return $conn;
}

function json_out(array $payload, int $status = 200): void {
  http_response_code($status);
  header('Content-Type: application/json; charset=utf-8');
  header('Cache-Control: no-store');
  echo json_encode($payload, JSON_UNESCAPED_UNICODE);
  exit;
}

function require_method(string $method): void {
  if (strtoupper($_SERVER['REQUEST_METHOD'] ?? '') !== strtoupper($method)) {
    json_out(['ok' => false, 'error' => 'Method not allowed'], 405);
  }
}

