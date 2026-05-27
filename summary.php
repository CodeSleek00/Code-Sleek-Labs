
<?php
include 'db.php';

$today = date("Y-m-d");

/*
----------------------------------
SEARCH + FILTERS
----------------------------------
*/

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$from   = isset($_GET['from']) ? trim($_GET['from']) : '';
$to     = isset($_GET['to']) ? trim($_GET['to']) : '';
$status = isset($_GET['status']) ? trim($_GET['status']) : '';

/*
----------------------------------
TOTAL STUDENTS
----------------------------------
*/

$totalStudents = $conn->query("
SELECT COUNT(*) as total 
FROM students26
")->fetch_assoc()['total'];

/*
----------------------------------
TODAY PRESENT
----------------------------------
*/

$todayPresent = $conn->query("
SELECT COUNT(DISTINCT student_id) as total
FROM attendance
WHERE attendance_date='$today'
")->fetch_assoc()['total'];

$todayAbsent = $totalStudents - $todayPresent;

/*
----------------------------------
STUDENT QUERY
----------------------------------
*/

$studentsQuery = "
SELECT * FROM students26
WHERE 1=1
";

if (!empty($search)) {

    $search = mysqli_real_escape_string($conn, $search);

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

if (!$students) {
    die("Query Failed : " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Advanced Attendance Dashboard</title>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    background:#f1f5f9;
    font-family:'Poppins',sans-serif;
    color:#0f172a;
    padding:24px;
}

.container{
    max-width:1600px;
    margin:auto;
}

.topbar{
    display:flex;
    justify-content:space-between;
    align-items:flex-end;
    gap:20px;
    margin-bottom:30px;
    flex-wrap:wrap;
}

.heading h1{
    font-size:30px;
    font-weight:700;
    display:flex;
    align-items:center;
    gap:12px;
}

.heading h1 i{
    color:#2563eb;
}

.heading p{
    margin-top:5px;
    color:#64748b;
}

.filters{
    display:flex;
    gap:12px;
    flex-wrap:wrap;
    background:#fff;
    padding:18px;
    border-radius:20px;
    border:1px solid #e2e8f0;
}

.filters input,
.filters select{
    padding:12px 14px;
    border:1px solid #cbd5e1;
    border-radius:12px;
    min-width:180px;
    font-size:14px;
    outline:none;
}

.filters input:focus,
.filters select:focus{
    border-color:#2563eb;
    box-shadow:0 0 0 3px rgba(37,99,235,0.1);
}

button{
    background:#2563eb;
    color:white;
    border:none;
    padding:12px 18px;
    border-radius:12px;
    cursor:pointer;
    font-weight:600;
    display:flex;
    align-items:center;
    gap:8px;
}

button:hover{
    background:#1d4ed8;
}

.reset-btn{
    background:#f1f5f9;
    color:#334155;
    text-decoration:none;
    padding:12px 18px;
    border-radius:12px;
    border:1px solid #e2e8f0;
    font-weight:600;
}

.reset-btn:hover{
    background:#e2e8f0;
}

.stats{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(240px,1fr));
    gap:20px;
    margin-bottom:30px;
}

.card{
    background:white;
    border-radius:24px;
    padding:24px;
    border:1px solid #e2e8f0;
}

.stat-box h2{
    font-size:42px;
    margin-bottom:10px;
}

.stat-box p{
    color:#64748b;
    font-size:14px;
    font-weight:600;
    text-transform:uppercase;
}

.section-title{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}

.section-title h2{
    font-size:22px;
    display:flex;
    align-items:center;
    gap:10px;
}

.section-title h2 i{
    color:#2563eb;
}

.table-box{
    overflow-x:auto;
}

table{
    width:100%;
    border-collapse:collapse;
}

th{
    background:#f8fafc;
    padding:15px;
    text-align:left;
    border-bottom:1px solid #e2e8f0;
    font-size:14px;
}

td{
    padding:15px;
    border-bottom:1px solid #f1f5f9;
    font-size:14px;
}

tr:hover td{
    background:#fafcff;
}

.badge{
    padding:5px 12px;
    border-radius:30px;
    font-size:12px;
    font-weight:600;
    display:inline-block;
}

.green{
    background:#ecfdf5;
    color:#059669;
}

.red{
    background:#fef2f2;
    color:#dc2626;
}

.orange{
    background:#fff7ed;
    color:#d97706;
}

.present-text{
    color:#059669;
    font-weight:700;
}

.absent-text{
    color:#dc2626;
    font-weight:700;
}

.view-btn{
    background:#eff6ff;
    color:#2563eb;
    text-decoration:none;
    padding:8px 14px;
    border-radius:30px;
    display:inline-flex;
    align-items:center;
    gap:6px;
    font-size:13px;
    font-weight:600;
}

.view-btn:hover{
    background:#dbeafe;
}

@media(max-width:768px){

    body{
        padding:16px;
    }

    .topbar{
        flex-direction:column;
        align-items:stretch;
    }

    .filters{
        flex-direction:column;
    }

    .filters input,
    .filters select,
    button,
    .reset-btn{
        width:100%;
    }

    .stats{
        grid-template-columns:1fr;
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

    <p>
        Manage and monitor complete student attendance records
    </p>
</div>
<!--
<form method="GET" action="" class="filters">

<input
type="text"
name="search"
placeholder="Search Name / Enrollment / Contact"
value="<?php echo htmlspecialchars($search); ?>"
>

<input
type="date"
name="from"
value="<?php echo htmlspecialchars($from); ?>"
>

<input
type="date"
name="to"
value="<?php echo htmlspecialchars($to); ?>"
>

<select name="status">

<option value="">All Status</option>

<option value="present"
<?php if($status=='present') echo 'selected'; ?>>
Present
</option>

<option value="absent"
<?php if($status=='absent') echo 'selected'; ?>>
Absent
</option>

</select>

<button type="submit">
<i class="fa-solid fa-magnifying-glass"></i>
Search
</button>

<a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="reset-btn">
Reset
</a>

</form>

</div>-->

<div class="stats">

<div class="card stat-box">
    <h2><?php echo $totalStudents; ?></h2>
    <p>Total Students</p>
</div>

<div class="card stat-box">
    <h2 style="color:#10b981">
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
    <h2 style="font-size:24px;color:#0ea5e9">
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
    SELECT id
    FROM attendance
    WHERE student_id='$studentId'
    AND attendance_date='$today'
    LIMIT 1
    ");

    $isPresent = $todayCheck->num_rows > 0;

    /*
    ----------------------------------
    STATUS FILTER
    ----------------------------------
    */

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

    if(!empty($from) && !empty($to)){

        $dateCondition = "
        AND attendance_date
        BETWEEN '$from' AND '$to'
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

    if(!empty($from) && !empty($to)){

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
    ATTENDANCE %
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
        ." at ".
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

<?php if($sr == 1){ ?>

<tr>
<td colspan="9" style="text-align:center;padding:40px;">
    No Students Found
</td>
</tr>

<?php } ?>

</table>

</div>

</div>

</div>

</body>
</html>
