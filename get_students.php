<?php
include 'db.php';

$result = $conn->query("SELECT * FROM students26");

$students = [];

while($row = $result->fetch_assoc()){
    $students[] = $row;
}

echo json_encode($students);
?>