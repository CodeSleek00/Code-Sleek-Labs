
<?php
include 'db.php';

date_default_timezone_set("Asia/Kolkata");

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
DATE FILTERS
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
SELECT *
FROM attendance
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

/*
--------------------------------------
LAST ATTENDANCE
--------------------------------------
*/

$lastAttendance = $conn->query("
SELECT *
FROM attendance
WHERE student_id='$studentId'
ORDER BY id DESC
LIMIT 1
");

$lastRecord = null;

if($lastAttendance->num_rows > 0){
    $lastRecord = $lastAttendance->fetch_assoc();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Student Attendance History</title>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

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
    max-width:1400px;
    margin:auto;
}

/*
--------------------------------------
CARDS
--------------------------------------
*/

.card{
    background:#0f172a;
    border:1px solid #1e293b;
    border-radius:20px;
    padding:25px;
    margin-bottom:25px;
}

/*
--------------------------------------
PROFILE
--------------------------------------
*/

.profile{
    display:flex;
    justify-content:space-between;
    gap:20px;
    flex-wrap:wrap;
    align-items:flex-start;
}

.profile-left h1{
    font-size:34px;
    margin-bottom:15px;
}

.profile-left p{
    color:#cbd5e1;
    margin-bottom:10px;
    font-size:15px;
}

.highlight{
    color:#38bdf8;
    font-weight:bold;
}

/*
--------------------------------------
BUTTONS
--------------------------------------
*/

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
    transition:0.3s;
}

.back-btn:hover{
    background:#0284c7;
}

/*
--------------------------------------
STATS
--------------------------------------
*/

.stats{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
    gap:20px;
    margin-bottom:25px;
}

.stat-box h2{
    font-size:38px;
    margin-bottom:10px;
}

.stat-box p{
    color:#94a3b8;
}

/*
--------------------------------------
FILTERS
--------------------------------------
*/

.filters{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
}

.filters input{
    padding:12px;
    border:none;
    border-radius:10px;
    background:#020617;
    color:white;
    border:1px solid #334155;
}

button{
    padding:12px 18px;
    border:none;
    border-radius:10px;
    background:#22c55e;
    color:white;
    font-weight:bold;
    cursor:pointer;
    transition:0.3s;
}

button:hover{
    background:#16a34a;
}

/*
--------------------------------------
TABLE
--------------------------------------
*/

.table-box{
    overflow:auto;
}

table{
    width:100%;
    border-collapse:collapse;
    min-width:900px;
}

th{
    background:#1e293b;
    padding:15px;
    text-align:left;
    font-size:14px;
}

td{
    padding:15px;
    border-bottom:1px solid #1e293b;
    font-size:14px;
}

tr:hover{
    background:#111827;
}

/*
--------------------------------------
BADGES
--------------------------------------
*/

.badge{
    background:#14532d;
    color:#4ade80;
    padding:8px 15px;
    border-radius:20px;
    font-size:13px;
    font-weight:bold;
}

.empty{
    text-align:center;
    color:#94a3b8;
    padding:40px;
    font-size:18px;
}

/*
--------------------------------------
SECTION TITLE
--------------------------------------
*/

.section-title{
    margin-bottom:20px;
    color:#38bdf8;
    display:flex;
    align-items:center;
    gap:10px;
}

/*
--------------------------------------
RESPONSIVE
--------------------------------------
*/

@media(max-width:768px){

    .profile{
        flex-direction:column;
    }

    .filters{
        flex-direction:column;
    }

    .filters input,
    button{
        width:100%;
    }

    .profile-left h1{
        font-size:28px;
    }

}

</style>

</head>

<body>

<div class="container">

<!-- PROFILE -->

<div class="card profile">

<div class="profile-left">

<h1>
    <i class="fa-solid fa-user-graduate"></i>
    <?php echo htmlspecialchars($student['name']); ?>
</h1>

<p>
    <span class="highlight">Enrollment ID:</span>
    <?php echo htmlspecialchars($student['enrollment_id']); ?>
</p>

<?php if(isset($student['course'])){ ?>

<p>
    <span class="highlight">Course:</span>
    <?php echo htmlspecialchars($student['course']); ?>
</p>

<?php } ?>

<?php if(isset($student['contact_number'])){ ?>

<p>
    <span class="highlight">Contact Number:</span>
    <?php echo htmlspecialchars($student['contact_number']); ?>
</p>

<?php } ?>

<?php if($lastRecord){ ?>

<p>
    <span class="highlight">Last Attendance:</span>

    <?php
    echo date(
        'd M Y',
        strtotime($lastRecord['attendance_date'])
    );

    echo " at ";

    echo date(
        'h:i A',
        strtotime($lastRecord['attendance_time'])
    );
    ?>
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

<!-- STATS -->

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

<!-- FILTER -->

<div class="card">

<form method="GET" class="filters">

<input
type="hidden"
name="id"
value="<?php echo $studentId; ?>"
>

<input
type="date"
name="from"
value="<?php echo $from; ?>"
>

<input
type="date"
name="to"
value="<?php echo $to; ?>"
>

<button type="submit">
    <i class="fa-solid fa-filter"></i>
    Filter Records
</button>

</form>

</div>

<!-- ATTENDANCE TABLE -->

<div class="card">

<h2 class="section-title">
    <i class="fa-solid fa-clock-rotate-left"></i>
    Complete Attendance History
</h2>

<div class="table-box">

<table>

<tr>

<th>#</th>
<th>Date</th>
<th>Day</th>
<th>Time</th>
<th>Status</th>

</tr>

<?php

if($attendanceQuery->num_rows > 0){

    $sr = 1;

    while($row = $attendanceQuery->fetch_assoc()){

?>

<tr>

<td>
    <?php echo $sr++; ?>
</td>

<td>
    <?php
    echo date(
        'd M Y',
        strtotime($row['attendance_date'])
    );
    ?>
</td>

<td>
    <?php
    echo date(
        'l',
        strtotime($row['attendance_date'])
    );
    ?>
</td>

<td>

<?php

/*
--------------------------------------
CORRECT TIME FORMAT
--------------------------------------
*/

echo date(
    'h:i:s A',
    strtotime($row['attendance_time'])
);

?>

</td>

<td>

<span class="badge">
    Present
</span>

</td>

</tr>

<?php

    }

}else{

?>

<tr>

<td colspan="5" class="empty">
    No attendance records found
</td>

</tr>

<?php } ?>

</table>

</div>

</div>

</div>

</body>
</html>