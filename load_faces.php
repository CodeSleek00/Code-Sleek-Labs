<?php

header('Content-Type: application/json');

include 'db.php';

$query = "
SELECT *
FROM face_data
";

$result = $conn->query($query);

$data = [];

while($row = $result->fetch_assoc()){

    $data[] = $row;

}

echo json_encode($data);

?>