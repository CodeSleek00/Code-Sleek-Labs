
<?php
include 'db.php';

$today = date("Y-m-d");

/*
----------------------------------
SEARCH + FILTERS
----------------------------------
*/

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$from   = isset($_GET['from']) ? $_GET['from'] : '';
$to     = isset($_GET['to']) ? $_GET['to'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';

/*
----------------------------------
TOTAL STUDENTS
----------------------------------
*/

$totalStudents = $conn->query("SELECT COUNT(*) as total FROM students26")
->fetch_assoc()['total'];

/*
----------------------------------
TODAY PRESENT
----------------------------------
*/

$todayPresent = $conn->query("
SELECT COUNT(DISTINCT student_id) as total
FROM attendance
WHERE attendance_date='$today'
")
->fetch_assoc()['total'];

$todayAbsent = $totalStudents - $todayPresent;

/*
----------------------------------
STUDENT QUERY
----------------------------------
*/

$studentsQuery = "SELECT * FROM students26 WHERE 1=1";

if($search != ''){
    $studentsQuery .= "
    AND (
        name LIKE '%$search%'
        OR enrollment_id LIKE '%$search%'
        OR contact_number LIKE '%$search%'
    )
    ";
}

$studentsQuery .= " ORDER BY name ASC";

$students = $conn->query($studentsQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Advanced Attendance Dashboard</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    background:#020617;
    font-family:Arial, Helvetica, sans-serif;
    color:white;
    padding:20px;
}

.container{
    max-width:1600px;
    margin:auto;
}

.topbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:20px;
    margin-bottom:25px;
    flex-wrap:wrap;
}

.heading h1{
    font-size:32px;
}

.heading p{
    color:#94a3b8;
    margin-top:8px;
}

.filters{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
    background:#0f172a;
    padding:15px;
    border-radius:16px;
    border:1px solid #1e293b;
}

.filters input,
.filters select{
    padding:12px;
    border:none;
    border-radius:10px;
    background:#020617;
    color:white;
    border:1px solid #334155;
    min-width:170px;
}

button{
    padding:12px 18px;
    border:none;
    border-radius:10px;
    background:#0ea5e9;
    color:white;
    cursor:pointer;
    font-weight:bold;
}

button:hover{
    background:#0284c7;
}

.reset-btn{
    background:#ef4444;
    text-decoration:none;
    color:white;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:12px 18px;
    border-radius:10px;
    font-weight:bold;
}

.reset-btn:hover{
    background:#dc2626;
}

.stats{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
    gap:20px;
    margin-bottom:25px;
}

.card{
    background:#0f172a;
    border:1px solid #1e293b;
    border-radius:20px;
    padding:25px;
}

.stat-box h2{
    font-size:42px;
    margin-bottom:10px;
}

.stat-box p{
    color:#94a3b8;
}

.section-title{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
    flex-wrap:wrap;
    gap:10px;
}

.section-title h2{
    color:#38bdf8;
}

.table-box{
    overflow:auto;
}

table{
    width:100%;
    border-collapse:collapse;
    min-width:1200px;
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

.badge{
    padding:7px 14px;
    border-radius:20px;
    font-size:12px;
    font-weight:bold;
}

.green{
    background:#14532d;
    color:#4ade80;
}

.red{
    background:#7f1d1d;
    color:#f87171;
}

.orange{
    background:#78350f;
    color:#fbbf24;
}

.view-btn{
    background:#22c55e;
    color:white;
    padding:10px 14px;
    border-radius:10px;
    text-decoration:none;
    font-size:13px;
    font-weight:bold;
    display:inline-flex;
    align-items:center;
    gap:8px;
}

.view-btn:hover{
    background:#16a34a;
}

.present-text{
    color:#22c55e;
    font-weight:bold;
}

.absent-text{
    color:#ef4444;
    font-weight:bold;
}

@media(max-width:768px){

    .topbar{
        flex-direction:column;
        align-items:flex-start;
    }

    .filters{
        width:100%;
    }

    .filters input,
    .filters select,
    button,
    .reset-btn{
        width:100%;
    }
}

</style>

</head>
<body>

<div class="container">

<div class="topbar">

<div class="heading">
    <h1>
        <i class="fa-solid fa-chart-column"></i>
        Advanced Attendance Dashboard
    </h1>

    <p>Manage and monitor complete student attendance records</p>
</div>

<form method="GET" class="filters">

<input type="text" name="search" placeholder="Search Name / Enrollment"
value="<?php echo htmlspecialchars($search); ?>">

<input type="date" name="from" value="<?php echo $from; ?>">

<input type="date" name="to" value="<?php echo $to; ?>">

<select name="status">
    <option value="">All Status</option>
    <option value="present" <?php if($status=='present') echo 'selected'; ?>>Present</option>
    <option value="absent" <?php if($status=='absent') echo 'selected'; ?>>Absent</option>
</select>

<button type="submit">
    <i class="fa-solid fa-magnifying-glass"></i>
    Search
</button>

<a href="attendance_dashboard.php" class="reset-btn">
    Reset
</a>

</form>

</div>

<div class="stats">

<div class="card stat-box">
    <h2><?php echo $totalStudents; ?></h2>
    <p>Total Students</p>
</div>

<div class="card stat-box">
    <h2 style="color:#22c55e">
        <?php echo $todayPresent; ?>
    </h2>
    <p>Present Today</p>
</div>

<div class="card stat-box">
    <h2 style="color:#ef4444">
        <?php echo $todayAbsent; ?>
    </h2>
    <p>Absent Today</p>
</div>

<div class="card stat-box">
    <h2 style="color:#38bdf8;font-size:25px;">
        <?php echo date('d M Y'); ?>
    </h2>
    <p>Today's Date</p>
</div>

</div>

<div class="card">

<div class="section-title">
    <h2>
        <i class="fa-solid fa-users"></i>
        All Students Attendance Report
    </h2>
</div>

<div class="table-box">

<table>

<tr>
    <th>#</th>
    <th>Student Name</th>
    <th>Enrollment ID</th>
    <th>Today's Status</th>
    <th>Total Present</th>
    <th>Total Absent</th>
    <th>Attendance %</th>
    <th>Last Attendance</th>
    <th>Action</th>
</tr>

<?php

$sr = 1;

while($student = $students->fetch_assoc()){

    $studentId = $student['id'];

    /*
    ----------------------------------
    TODAY STATUS
    ----------------------------------
    */

    $todayCheck = $conn->query("
    SELECT id FROM attendance
    WHERE student_id='$studentId'
    AND attendance_date='$today'
    ");

    $isPresent = $todayCheck->num_rows > 0;

    if($status == 'present' && !$isPresent){
        continue;
    }

    if($status == 'absent' && $isPresent){
        continue;
    }

    /*
    ----------------------------------
    DATE CONDITION
    ----------------------------------
    */

    $dateCondition = "";

    if($from != '' && $to != ''){
        $dateCondition = "
        AND attendance_date BETWEEN '$from' AND '$to'
        ";
    }

    /*
    ----------------------------------
    TOTAL PRESENT
    ----------------------------------
    */

    $presentQuery = $conn->query("
    SELECT COUNT(*) as total
    FROM attendance
    WHERE student_id='$studentId'
    $dateCondition
    ");

    $presentCount = $presentQuery->fetch_assoc()['total'];

    /*
    ----------------------------------
    TOTAL DAYS
    ----------------------------------
    */

    if($from != '' && $to != ''){

        $days = (
            strtotime($to) - strtotime($from)
        ) / (60*60*24) + 1;

    }else{

        $startDate = $conn->query("
        SELECT attendance_date
        FROM attendance
        ORDER BY attendance_date ASC
        LIMIT 1
        ");

        if($startDate->num_rows > 0){

            $firstDate = $startDate->fetch_assoc()['attendance_date'];

            $days = (
                strtotime($today) - strtotime($firstDate)
            ) / (60*60*24) + 1;

        }else{
            $days = 1;
        }
    }

    $absentCount = $days - $presentCount;

    if($absentCount < 0){
        $absentCount = 0;
    }

    /*
    ----------------------------------
    ATTENDANCE PERCENTAGE
    ----------------------------------
    */

    $percentage = round(($presentCount / $days) * 100);

    /*
    ----------------------------------
    LAST ATTENDANCE
    ----------------------------------
    */

    $lastAttendance = $conn->query("
    SELECT attendance_date, attendance_time
    FROM attendance
    WHERE student_id='$studentId'
    ORDER BY id DESC
    LIMIT 1
    ");

    $lastDate = "No Attendance";

    if($lastAttendance->num_rows > 0){

        $lastRow = $lastAttendance->fetch_assoc();

        $lastDate =
        date('d M Y', strtotime($lastRow['attendance_date']))
        ." at " .
        date('h:i A', strtotime($lastRow['attendance_time']));
    }
?>

<tr>

<td><?php echo $sr++; ?></td>

<td>
    <?php echo htmlspecialchars($student['name']); ?>
</td>

<td>
    <?php echo htmlspecialchars($student['enrollment_id']); ?>
</td>

<td>

<?php
if($isPresent){
    echo '<span class="badge green">Present</span>';
}else{
    echo '<span class="badge red">Absent</span>';
}
?>

</td>

<td class="present-text">
    <?php echo $presentCount; ?>
</td>

<td class="absent-text">
    <?php echo $absentCount; ?>
</td>

<td>

<?php
if($percentage >= 75){
    echo "<span class='badge green'>$percentage%</span>";
}else if($percentage >= 40){
    echo "<span class='badge orange'>$percentage%</span>";
}else{
    echo "<span class='badge red'>$percentage%</span>";
}
?>

</td>

<td>
    <?php echo $lastDate; ?>
</td>

<td>

<a class="view-btn"
href="student_attendance_history.php?id=<?php echo $studentId; ?>">
    <i class="fa-solid fa-eye"></i>
    View Record
</a>

</td>

</tr>

<?php } ?>

</table>

</div>

</div>

</div>

</body>
</html>