<?php
session_start();
include 'db_connect.php';


if (!isset($_SESSION['role']) ||($_SESSION['role']) !== 'teacher') {
    echo "Unauthorized Access";
    exit;
}

// Total students

$total_students_query = $conn->query("SELECT COUNT(*) AS total FROM student");
$total_students = $total_students_query->fetch_assoc()['total'];

// Active students (assuming you have a column `status='active'`)
$active_students_query = $conn->query("SELECT COUNT(*) AS active FROM student WHERE status='active'");
$active_students = $active_students_query->fetch_assoc()['active'];

// Average attendance (count of present / total * 100)
$attendance_query = $conn->query("
    SELECT 
        (SUM(CASE WHEN status='present' THEN 1 ELSE 0 END) / COUNT(*)) * 100 AS avg_attendance
    FROM attendance");
$average_attendance = round($attendance_query->fetch_assoc()['avg_attendance'] ?? 0, 2);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Dashboard</title>
    <style>
   * {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
    font-family: "Segoe UI", Tahoma, sans-serif;
}
body {
    background: #FAFAFA;
    color: #212121;
}
.box {
    display: flex;
    max-width: 1400px;
    margin: 30px auto;
    gap: 24px;
    padding: 0 18px;
}

.sidebar {
    width: 240px;
    background: #fff;
    border-radius: 20px;
    padding: 24px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
}
.big {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 30px;
}
.small {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #6A1B9A, #BA68C8);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    border-radius: 12px;
}
.big h1 {
    font-size: 18px;
    color: #6A1B9A;
}
.nav a {
    display: flex;
    align-items: center;
    padding: 12px;
    text-decoration: none;
    border-radius: 10px;
    color: #212121;
    gap:10px;
    font-weight: 500;
}
.nav a:hover {
    background: #F3E5F5;
}
.above {
    flex: 1;
}
.right {
    background: #fff;
    border-radius: 16px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.right h2 {
    color: #6A1B9A;
    font-size: 22px;
}
.search {
    background: #F3E5F5;
    border-radius: 10px;
    padding: 10px 16px;
}
.search input {
    border: none;
    outline: none;
    background: transparent;
    font-size: 14px;
    color: #212121;
}
.mainbox {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
}
.boxes {
    background: #fff;
    border-radius: 16px;
    padding: 20px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}
.boxes .label {
    color: #6b7280;
    font-size: 14px;
}
.boxes .value {
    font-size: 26px;
    font-weight: 700;
    color: #6A1B9A;
}
.qr{
    background: white;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
} 
.qr h2{
    color: #6A1B9A;
    margin-bottom: 9px;
}
.qr-box{
    margin-top: 14px;
    height: 200px;
    width: 200px;
    background: #BA68C8;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-weight: bold;
}
</style>
</head>
<body>
<div class="box">   
        <div class="sidebar">
        <div class="big">
          <div class="small">D</div>
            <h1>Dashboard</h1>
          </div>
    <nav class="nav">
        <a href="Teacher_dashboard.php" class="active" ><i class="fa fa-home" style="font-size: 24px;color:purple"></i>Teacher</a>
        <a href="student.php" ><i class="fa fa-graduation-cap" style="font-size:22px;color:purple"></i>Students</a>
        <a href="generate_qr.php"><i class="fa fa-qrcode" style="font-size:24px;color:purple"></i>Generate QR</a>
        <a href="view_Attendance.php"><i class="fa fa-file-o" style="font-size:22px;color:purple"></i>View Attendance</a>
        <a href="export_monthly.php"><i class="fa fa-download" style="font-size:22px;color:purple"></i> Export Monthly</a>
        <a href="logout.php"><i class="fa fa-sign-out" style="font-size:22px;color:purple"></i>Logout</a>
    </nav>
    </div>
    <div class="above">
        <div class="right">
            <h2>Hello Teacher
                !</h2>
              <div class="search"> <input type="text" placeholder="Search...">
       </div>  
    </div>
      
        <div class="mainbox">
            <div class="boxes">
                    <div class="label">Total Student</div>
                     <div class="value"><?php echo $total_students; ?></div>
            </div>
            <div class="boxes">
                    <div class="label">Active Student</div>
                    <div class="value"><?php echo $active_students; ?></div>   
            </div>
            <div class="boxes">           
                  <div class="label">Average Attendance</div>
                  <div class="value"><?php echo $average_attendance; ?>%</div>
                 
            </div>
        </div>
        </div>
    </div>
</div>
</body>
</html>