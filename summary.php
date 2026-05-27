
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
/*---------------------------------------
  MINIMAL UI - White/Slate Theme
  Light background, subtle shadows, clean typography
---------------------------------------*/

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    background: #f1f5f9;  /* Soft slate background */
     font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
    color: #0f172a;
    padding: 24px;
    line-height: 1.5;
}

.container {
    max-width: 1600px;
    margin: 0 auto;
}

/*---------------------------------------
  TOPBAR + HEADING
---------------------------------------*/
.topbar {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    gap: 24px;
    margin-bottom: 32px;
    flex-wrap: wrap;
}

.heading h1 {
    font-size: 28px;
    font-weight: 600;
    color: #0f172a;
    letter-spacing: -0.3px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.heading h1 i {
    color: #3b82f6;
    font-size: 28px;
}

.heading p {
    color: #475569;
    margin-top: 6px;
    font-size: 15px;
}

/*---------------------------------------
  FILTERS FORM (clean white card)
---------------------------------------*/
.filters {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    background: #ffffff;
    padding: 16px 20px;
    border-radius: 20px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
    align-items: center;
}

.filters input,
.filters select {
    padding: 10px 14px;
    border: 1px solid #cbd5e1;
    border-radius: 12px;
    background: #ffffff;
    color: #0f172a;
    font-size: 14px;
    transition: all 0.2s ease;
    outline: none;
    min-width: 170px;
}

.filters input:focus,
.filters select:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

button {
    padding: 10px 20px;
    border: none;
    border-radius: 12px;
    background: #3b82f6;
    color: white;
    cursor: pointer;
    font-weight: 500;
    font-size: 14px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s;
}

button:hover {
    background: #2563eb;
    transform: translateY(-1px);
}

.reset-btn {
    background: #f1f5f9;
    color: #475569;
    text-decoration: none;
    padding: 10px 20px;
    border-radius: 12px;
    font-weight: 500;
    font-size: 14px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s;
    border: 1px solid #e2e8f0;
}

.reset-btn:hover {
    background: #e2e8f0;
    color: #0f172a;
    transform: translateY(-1px);
}

/*---------------------------------------
  STATS CARDS (clean minimal)
---------------------------------------*/
.stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 20px;
    margin-bottom: 32px;
}

.card {
    background: #ffffff;
    border-radius: 24px;
    padding: 20px 24px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    transition: box-shadow 0.2s;
}

.card:hover {
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
}

.stat-box h2 {
    font-size: 40px;
    font-weight: 700;
    margin-bottom: 8px;
    letter-spacing: -1px;
    color: #0f172a;
}

.stat-box p {
    color: #475569;
    font-size: 14px;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

/* colored numbers for present/absent */
.stat-box h2[style*="color:#22c55e"],
.stat-box h2:has(+ p:contains("Present")) {
    color: #10b981;
}

/*---------------------------------------
  SECTION TITLE
---------------------------------------*/
.section-title {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 12px;
}

.section-title h2 {
    font-size: 20px;
    font-weight: 600;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 10px;
}

.section-title h2 i {
    color: #3b82f6;
    font-size: 20px;
}

/*---------------------------------------
  TABLE (clean, borderless, minimal)
---------------------------------------*/
.table-box {
    overflow-x: auto;
    border-radius: 20px;
}

table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
}

th {
    text-align: left;
    padding: 14px 16px;
    background: #f8fafc;
    color: #334155;
    font-weight: 600;
    font-size: 13px;
    border-bottom: 1px solid #e2e8f0;
}

td {
    padding: 14px 16px;
    border-bottom: 1px solid #f1f5f9;
    color: #1e293b;
    vertical-align: middle;
}

tr:hover td {
    background-color: #fafcff;
}

/* badges */
.badge {
    display: inline-flex;
    align-items: center;
    padding: 4px 12px;
    border-radius: 40px;
    font-size: 12px;
    font-weight: 500;
    white-space: nowrap;
}

.green {
    background: #ecfdf5;
    color: #059669;
}

.red {
    background: #fef2f2;
    color: #dc2626;
}

.orange {
    background: #fffbeb;
    color: #d97706;
}

.present-text {
    color: #059669;
    font-weight: 600;
}

.absent-text {
    color: #dc2626;
    font-weight: 600;
}

/* view button */
.view-btn {
    background: #f1f5f9;
    color: #1e40af;
    padding: 6px 14px;
    border-radius: 30px;
    text-decoration: none;
    font-size: 12px;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s;
    border: 1px solid #e2e8f0;
}

.view-btn:hover {
    background: #e2e8f0;
    color: #1e3a8a;
    transform: translateY(-1px);
}

/* responsive */
@media (max-width: 768px) {
    body {
        padding: 16px;
    }

    .topbar {
        flex-direction: column;
        align-items: stretch;
    }

    .filters {
        width: 100%;
        flex-direction: column;
        align-items: stretch;
    }

    .filters input,
    .filters select,
    button,
    .reset-btn {
        width: 100%;
    }

    .stats {
        grid-template-columns: 1fr;
    }

    th, td {
        padding: 10px 12px;
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