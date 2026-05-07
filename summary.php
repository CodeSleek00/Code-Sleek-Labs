<?php

include 'db.php';

/*
TODAY DATE
*/

$today = date("Y-m-d");

/*
SEARCH
*/

$search = "";

if(isset($_GET['search'])){

    $search = trim($_GET['search']);

}

/*
FILTER DATES
*/

$from = isset($_GET['from']) ? $_GET['from'] : '';
$to   = isset($_GET['to']) ? $_GET['to'] : '';

/*
ALL STUDENTS
*/

$studentsQuery = "
SELECT * FROM students26
";

if($search != ''){

    $studentsQuery .= "
    WHERE name LIKE '%$search%'
    OR enrollment_id LIKE '%$search%'
    ";

}

$students = $conn->query($studentsQuery);

?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Attendance Summary</title>

<style>

body{
    margin:0;
    padding:20px;
    background:#020617;
    font-family:Arial;
    color:white;
}

.container{
    max-width:1400px;
    margin:auto;
}

.topbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:25px;
    flex-wrap:wrap;
    gap:15px;
}

h1{
    margin:0;
}

.filters{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
}

input{
    padding:12px;
    border:none;
    border-radius:10px;
    background:#0f172a;
    color:white;
    border:1px solid #1e293b;
}

button{
    padding:12px 18px;
    border:none;
    border-radius:10px;
    background:#38bdf8;
    color:white;
    cursor:pointer;
    font-weight:bold;
}

button:hover{
    background:#0ea5e9;
}

.stats{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:20px;
    margin-bottom:25px;
}

.card{
    background:#0f172a;
    padding:25px;
    border-radius:20px;
    border:1px solid #1e293b;
}

.card h2{
    margin:0;
    font-size:40px;
}

.card p{
    margin-top:10px;
    color:#94a3b8;
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

.present{
    color:#22c55e;
    font-weight:bold;
}

.absent{
    color:#ef4444;
    font-weight:bold;
}

.badge{
    padding:6px 12px;
    border-radius:20px;
    font-size:13px;
    font-weight:bold;
}

.green{
    background:#14532d;
    color:#22c55e;
}

.red{
    background:#7f1d1d;
    color:#ef4444;
}

.orange{
    background:#78350f;
    color:#f59e0b;
}

.search-title{
    margin-bottom:20px;
    color:#38bdf8;
}

</style>

</head>

<body>

<div class="container">

<div class="topbar">

<h1>Attendance Summary Dashboard</h1>

<form method="GET" class="filters">

<input
type="text"
name="search"
placeholder="Search Student"
value="<?php echo $search; ?>"
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
Search
</button>

</form>

</div>

<?php

/*
TOTAL STUDENTS
*/

$totalStudents = $conn->query("
SELECT COUNT(*) as total
FROM students26
")->fetch_assoc()['total'];

/*
TODAY PRESENT
*/

$todayPresent = $conn->query("
SELECT COUNT(*) as total
FROM attendance
WHERE attendance_date='$today'
")->fetch_assoc()['total'];

$todayAbsent = $totalStudents - $todayPresent;

?>

<div class="stats">

<div class="card">

<h2>
<?php echo $totalStudents; ?>
</h2>

<p>Total Students</p>

</div>

<div class="card">

<h2 style="color:#22c55e">
<?php echo $todayPresent; ?>
</h2>

<p>Present Today</p>

</div>

<div class="card">

<h2 style="color:#ef4444">
<?php echo $todayAbsent; ?>
</h2>

<p>Absent Today</p>

</div>

<div class="card">

<h2 style="color:#38bdf8">
<?php echo $today; ?>
</h2>

<p>Current Date</p>

</div>

</div>

<div class="card">

<h2 class="search-title">
Students Attendance Report
</h2>

<div class="table-box">

<table>

<tr>

<th>Student</th>
<th>Enrollment</th>
<th>Today Status</th>
<th>Total Present</th>
<th>Total Absent</th>
<th>Attendance %</th>
<th>Last Attendance</th>

</tr>

<?php

while($student = $students->fetch_assoc()){

    $studentId = $student['id'];

    /*
    TODAY STATUS
    */

    $todayCheck = $conn->query("
    SELECT * FROM attendance
    WHERE student_id='$studentId'
    AND attendance_date='$today'
    ");

    $isPresent = $todayCheck->num_rows > 0;

    /*
    DATE FILTER
    */

    $dateCondition = "";

    if($from != '' && $to != ''){

        $dateCondition = "
        AND attendance_date
        BETWEEN '$from'
        AND '$to'
        ";

    }

    /*
    TOTAL PRESENT
    */

    $presentQuery = $conn->query("
    SELECT COUNT(*) as total
    FROM attendance
    WHERE student_id='$studentId'
    $dateCondition
    ");

    $presentCount =
    $presentQuery->fetch_assoc()['total'];

    /*
    TOTAL DAYS
    */

    if($from != '' && $to != ''){

        $days =
        (strtotime($to) - strtotime($from))
        / (60*60*24) + 1;

    }else{

        $days = 30;

    }

    $absentCount =
    $days - $presentCount;

    if($absentCount < 0){

        $absentCount = 0;

    }

    /*
    PERCENTAGE
    */

    if($days > 0){

        $percentage =
        round(($presentCount/$days)*100);

    }else{

        $percentage = 0;

    }

    /*
    LAST ATTENDANCE
    */

    $lastAttendance = $conn->query("
    SELECT * FROM attendance
    WHERE student_id='$studentId'
    ORDER BY id DESC
    LIMIT 1
    ");

    $lastDate = "No Attendance";

    if($lastAttendance->num_rows > 0){

        $lastRow =
        $lastAttendance->fetch_assoc();

        $lastDate =
        $lastRow['attendance_date']
        ." ".
        $lastRow['attendance_time'];

    }

?>

<tr>

<td>
<?php echo $student['name']; ?>
</td>

<td>
<?php echo $student['enrollment_id']; ?>
</td>

<td>

<?php

if($isPresent){

    echo "
    <span class='badge green'>
    Present
    </span>
    ";

}else{

    echo "
    <span class='badge red'>
    Absent
    </span>
    ";

}

?>

</td>

<td class="present">
<?php echo $presentCount; ?>
</td>

<td class="absent">
<?php echo $absentCount; ?>
</td>

<td>

<?php

if($percentage >= 75){

    echo "
    <span class='badge green'>
    $percentage%
    </span>
    ";

}else if($percentage >= 40){

    echo "
    <span class='badge orange'>
    $percentage%
    </span>
    ";

}else{

    echo "
    <span class='badge red'>
    $percentage%
    </span>
    ";

}

?>

</td>

<td>
<?php echo $lastDate; ?>
</td>

</tr>

<?php } ?>

</table>

</div>

</div>

<?php

/*
DAILY RECORDS
*/

$recordsQuery = "
SELECT attendance.*, students26.name,
students26.enrollment_id
FROM attendance
INNER JOIN students26
ON attendance.student_id = students26.id
";

$where = [];

if($from != '' && $to != ''){

    $where[] = "
    attendance_date
    BETWEEN '$from'
    AND '$to'
    ";

}

if($search != ''){

    $where[] = "
    (
        students26.name LIKE '%$search%'
        OR students26.enrollment_id LIKE '%$search%'
    )
    ";

}

if(count($where) > 0){

    $recordsQuery .= "
    WHERE ".implode(" AND ",$where);

}

$recordsQuery .= "
ORDER BY attendance.id DESC
";

$records = $conn->query($recordsQuery);

?>

<div class="card" style="margin-top:25px;">

<h2 class="search-title">
Daily Attendance Records
</h2>

<div class="table-box">

<table>

<tr>

<th>Date</th>
<th>Time</th>
<th>Name</th>
<th>Enrollment</th>
<th>Status</th>

</tr>

<?php

while($row = $records->fetch_assoc()){

?>

<tr>

<td>
<?php echo $row['attendance_date']; ?>
</td>

<td>
<?php echo $row['attendance_time']; ?>
</td>

<td>
<?php echo $row['name']; ?>
</td>

<td>
<?php echo $row['enrollment_id']; ?>
</td>

<td>

<span class="badge green">
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