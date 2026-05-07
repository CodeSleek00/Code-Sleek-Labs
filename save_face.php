<?php

include 'db.php';

$data = json_decode(file_get_contents("php://input"), true);

$student_id = $data['student_id'];
$descriptor = json_encode($data['descriptor']);

$stmt = $conn->prepare("
INSERT INTO face_data(student_id, descriptor)
VALUES (?, ?)
");

$stmt->bind_param("is", $student_id, $descriptor);
$stmt->execute();

echo json_encode([
    "status" => "success"
]);
?>