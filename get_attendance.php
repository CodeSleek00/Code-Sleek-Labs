<?php

include 'db.php';

$date = date("Y-m-d");

$query = "
SELECT
attendance.*,
students26.name,
students26.enrollment_id
FROM attendance
JOIN students26
ON students26.id = attendance.student_id
WHERE attendance.attendance_date='$date'
ORDER BY attendance.id DESC
";

$result = $conn->query($query);

$data = [];

while($row = $result->fetch_assoc()){

    $data[] = $row;

}

echo json_encode($data);

?>