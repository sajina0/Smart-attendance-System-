<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
// FETCH COUNT
$teachers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM teacher"))['total'];
$students = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM student"))['total'];
$subjects = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM subject"))['total'];
$classes  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM class"))['total'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
    *{
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Courier New', Courier, monospace;
    }
    body{
        display: flex;
        min-height: 100vh;
        background-color:#F3E5F5; /* soft purple background */
    }
    /* Sidebar */
    .left{
        width: 230px;
        background:#FFFFFF;
        box-shadow: 2px 0 15px rgba(106, 27, 154, 0.1);
        left:0;
        top: 0;
        padding-top: 20px;
        position: fixed;
        height: 100vh;
        border-radius: 0 20px 20px 0;
        display: flex;
        flex-direction: column;
    }
    .logo{
        padding: 20px;
        border-bottom: 1px solid #EDE7F6;
        text-align: center;
    }
    .logo h1 {
        color:  #6A1B9A;
        font-size: 24px;
        font-weight: bold;
    }
    /* Navigation */
    .nav {
        margin-top: 20px;
    }
    .navbar{
        display: block;
        font-size: 16px;
        padding: 14px 20px;
        color: #4B5563; /* dark neutral */
        text-decoration: none;
        font-weight: 500;
        border-radius: 10px;
        margin: 5px 15px;
        transition: 0.2s;
    }
    .navbar:hover,
    .navbar.active {
        background: #E9D5FF;  /* light purple hover */
        color:#6A1B9A;
    }
    /* Main Content */
    .mainbox {
        margin-left: 239px;
        width: calc(100% - 230px);
        padding: 20px;
    }

    .header h1 {
        color: #8E24AA; /* bright purple for heading */
        font-size: 28px;
        margin-bottom: 20px;
    }
    .boxes {
        background: #FFFFFF;
        padding: 30px;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(106, 27, 154, 0.08);
        display: inline-block;
        margin: 10px;
        width: 240px;
        text-align: center;
        transition: 0.2s;
    }

    .boxes:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 16px rgba(106, 27, 154, 0.12);
    }
    .boxes h2 {
        font-size: 32px;
        font-weight: bold;
        color: #6A1B9A;
    }
    .boxes p {
        margin-top: 7px;
        font-size: 15px;
        color: #8E24AA;
        font-weight: 500;
    }
</style>

</head>
<body>

<div class="left">
    <div class="logo"><h1>Admin</h1></div>
    <div class="nav">
        <a class="navbar" href="manage_teachers.php">Teachers</a>
        <a class="navbar" href="manage_students.php">Students</a>
        <a class="navbar" href="manage_subjects.php">Subjects</a>
        <a class="navbar" href="manage_classes.php">Classes</a>
        <a class="navbar" href="verify_accounts.php">Verify Accounts</a>
        <a class="navbar" href="view_Attendance.php">Attendance Reports</a>
        <a  class ="navbar"href="logout.php" class="logout">Logout</a>
    </div>
</div>

<div class="mainbox">
    <div class="header">
        <h1>Dashboard Overview</h1>
    </div>

    <div class="boxes">
        <h2><?php echo $teachers; ?></h2>
        <p>Total Teachers</p>
    </div>

    <div class="boxes">
        <h2><?php echo $students; ?></h2>
        <p>Total Students</p>
    </div>

    <div class="boxes">
        <h2><?php echo $subjects; ?></h2>
        <p>Total Subjects</p>
    </div>

    <div class="boxes">
        <h2><?php echo $classes; ?></h2>
        <p>Total Classes</p>
    </div>
    <table>
        <th></th>
    </table>
</div>

</body>
</html>
