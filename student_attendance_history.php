<?php
include 'db.php';

if(!isset($_GET['id'])){
    die("Student ID Missing");
}

$studentId = $_GET['id'];

/*
--------------------------------------
GET STUDENT DETAILS
--------------------------------------
*/

$studentQuery = $conn->query("
SELECT * FROM students26
WHERE id='$studentId'
LIMIT 1
");

if($studentQuery->num_rows == 0){
    die("Student Not Found");
}

$student = $studentQuery->fetch_assoc();

/*
--------------------------------------
SEARCH DATE FILTER
--------------------------------------
*/

$from = isset($_GET['from']) ? $_GET['from'] : '';
$to   = isset($_GET['to']) ? $_GET['to'] : '';

$where = "WHERE student_id='$studentId'";

if($from != '' && $to != ''){
    $where .= "
    AND attendance_date BETWEEN '$from' AND '$to'
    ";
}

/*
--------------------------------------
ATTENDANCE RECORDS
--------------------------------------
*/

$attendanceQuery = $conn->query("
SELECT * FROM attendance
$where
ORDER BY attendance_date DESC, attendance_time DESC
");

/*
--------------------------------------
TOTAL PRESENT
--------------------------------------
*/

$totalPresent = $conn->query("
SELECT COUNT(*) as total
FROM attendance
WHERE student_id='$studentId'
")->fetch_assoc()['total'];

?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Student Attendance History</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    background:#020617;
    color:white;
    font-family:Arial, Helvetica, sans-serif;
    padding:20px;
}

.container{
    max-width:1300px;
    margin:auto;
}

.card{
    background:#0f172a;
    border:1px solid #1e293b;
    border-radius:20px;
    padding:25px;
    margin-bottom:25px;
}

.profile{
    display:flex;
    justify-content:space-between;
    gap:20px;
    flex-wrap:wrap;
}

.profile-left h1{
    margin-bottom:10px;
}

.profile-left p{
    margin-bottom:8px;
    color:#cbd5e1;
}

.back-btn{
    background:#0ea5e9;
    color:white;
    text-decoration:none;
    padding:12px 18px;
    border-radius:10px;
    display:inline-flex;
    align-items:center;
    gap:10px;
    font-weight:bold;
}

.back-btn:hover{
    background:#0284c7;
}

.filters{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
}

.filters input{
    padding:12px;
    background:#020617;
    border:1px solid #334155;
    border-radius:10px;
    color:white;
}

button{
    padding:12px 18px;
    border:none;
    border-radius:10px;
    background:#22c55e;
    color:white;
    font-weight:bold;
    cursor:pointer;
}

button:hover{
    background:#16a34a;
}

.table-box{
    overflow:auto;
}

table{
    width:100%;
    border-collapse:collapse;
}

th{
    background:#1e293b;
    padding:15px;
    text-align:left;
}

td{
    padding:15px;
    border-bottom:1px solid #1e293b;
}

tr:hover{
    background:#111827;
}

.badge{
    background:#14532d;
    color:#4ade80;
    padding:8px 15px;
    border-radius:20px;
    font-size:13px;
    font-weight:bold;
}

.stats{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
    gap:20px;
}

.stat-box h2{
    font-size:38px;
    margin-bottom:10px;
}

.stat-box p{
    color:#94a3b8;
}

</style>

</head>
<body>

<div class="container">

<div class="card profile">

<div class="profile-left">

<h1>
    <i class="fa-solid fa-user"></i>
    <?php echo htmlspecialchars($student['name']); ?>
</h1>

<p>
    <strong>Enrollment ID:</strong>
    <?php echo htmlspecialchars($student['enrollment_id']); ?>
</p>

<?php if(isset($student['course'])){ ?>
<p>
    <strong>Course:</strong>
    <?php echo htmlspecialchars($student['course']); ?>
</p>
<?php } ?>

<?php if(isset($student['contact_number'])){ ?>
<p>
    <strong>Contact:</strong>
    <?php echo htmlspecialchars($student['contact_number']); ?>
</p>
<?php } ?>

</div>

<div>
    <a href="attendance_dashboard.php" class="back-btn">
        <i class="fa-solid fa-arrow-left"></i>
        Back Dashboard
    </a>
</div>

</div>

<div class="stats">

<div class="card stat-box">
    <h2 style="color:#22c55e">
        <?php echo $totalPresent; ?>
    </h2>
    <p>Total Present Days</p>
</div>

<div class="card stat-box">
    <h2 style="color:#38bdf8">
        <?php echo date('d M Y'); ?>
    </h2>
    <p>Today's Date</p>
</div>

</div>

<div class="card">

<form method="GET" class="filters">

<input type="hidden" name="id"
value="<?php echo $studentId; ?>">

<input type="date" name="from"
value="<?php echo $from; ?>">

<input type="date" name="to"
value="<?php echo $to; ?>">

<button type="submit">
    Filter Records
</button>

</form>

</div>

<div class="card">

<h2 style="margin-bottom:20px;color:#38bdf8;">
    Complete Attendance History
</h2>

<div class="table-box">

<table>

<tr>
    <th>#</th>
    <th>Date</th>
    <th>Time</th>
    <th>Status</th>
</tr>

<?php

$sr = 1;

while($row = $attendanceQuery->fetch_assoc()){
?>

<tr>

<td><?php echo $sr++; ?></td>

<td>
    <?php echo date('d M Y', strtotime($row['attendance_date'])); ?>
</td>

<td>
    <?php echo date('h:i:s A', strtotime($row['attendance_time'])); ?>
</td>

<td>
    <span class="badge">
        Present
    </span>
</td>

</tr>

<?php } ?>

</table>

</div>

</div>

</div>

</body>
</html>
```
