<?php
date_default_timezone_set('Asia/Kolkata');

include 'db.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

$student_id = $data['student_id'];
$distance = isset($data['distance']) ? floatval($data['distance']) : 0;

// اگر distance 0.6 سے زیادہ ہے تو reject کریں
if($distance > 0.6){
    echo json_encode([
        'success' => false,
        'error' => 'Face match confidence too low',
        'distance' => $distance
    ]);
    exit;
}

$date = date("Y-m-d");

$time = date("h:i A");

$alreadyMarked = false;

/*
CHECK ATTENDANCE
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

}else{

    $alreadyMarked = true;

}

/*
GET STUDENT
*/

$student = $conn->query("
SELECT * FROM students26
WHERE id='$student_id'
");

$row = $student->fetch_assoc();

/*
RETURN RESPONSE
*/

echo json_encode([

    'success' => true,

    'already_marked' => $alreadyMarked,

    'name' => $row['name'],

    'enrollment_id' => $row['enrollment_id'],

    'time' => $time

]);

?>