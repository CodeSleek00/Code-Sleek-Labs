<?php

include 'db.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

$student_id = $data['student_id'];

$date = date("Y-m-d");

$time = date("h:i A");

/*
CHECK ALREADY MARKED
*/

$check = $conn->query("
SELECT * FROM attendance
WHERE student_id='$student_id'
AND attendance_date='$date'
");

if($check->num_rows == 0){

    $conn->query("
    INSERT INTO attendance(
        student_id,
        attendance_date,
        attendance_time
    )
    VALUES(
        '$student_id',
        '$date',
        '$time'
    )
    ");

}

/*
GET STUDENT DETAILS
*/

$student = $conn->query("
SELECT * FROM students26
WHERE id='$student_id'
");

$row = $student->fetch_assoc();

/*
RETURN JSON
*/

echo json_encode([

    'success' => true,

    'name' => $row['name'],

    'enrollment_id' => $row['enrollment_id'],

    'time' => $time

]);

?>