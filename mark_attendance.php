<?php

include 'db.php';

$data = json_decode(file_get_contents("php://input"), true);

$student_id = $data['student_id'];
$date = date("Y-m-d");
$time = date("H:i:s");

$check = $conn->query("
SELECT * FROM attendance
WHERE student_id='$student_id'
AND attendance_date='$date'
");

if($check->num_rows == 0){

$stmt = $conn->prepare("
INSERT INTO attendance(
student_id,
attendance_date,
attendance_time
)
VALUES (?, ?, ?)
");

$stmt->bind_param(
"iss",
$student_id,
$date,
$time
);

$stmt->execute();

echo json_encode([
    "status" => "marked"
]);

}else{

echo json_encode([
    "status" => "already_marked"
]);

}
?>