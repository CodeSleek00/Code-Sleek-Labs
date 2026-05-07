<?php

include 'db.php';

$query = "
SELECT
face_data.student_id,
face_data.descriptor,
students26.name
FROM face_data
JOIN students26
ON students26.id = face_data.student_id
";

$result = $conn->query($query);

$data = [];

while($row = $result->fetch_assoc()){
    $data[] = $row;
}

echo json_encode($data);

?>