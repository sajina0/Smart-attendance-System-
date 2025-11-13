<!-- <?php
session_start();
include 'db_connect.php';

// Restrict access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: landing.php');
    exit;
}

$student_id = $_SESSION['user_id'];
$fullname = $_SESSION['fullname'] ?? 'Student';

// Fetch enrolled classes
$sql = "SELECT c.* 
        FROM classes c 
        JOIN enrollment e ON c.id = e.class_id 
        WHERE e.student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background-color: #F4F6F7;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 50px auto;
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        h2 {
            color: #4A148C;
        }
        .logout {
            float: right;
            background: #D32F2F;
            color: white;
            padding: 8px 14px;
            text-decoration: none;
            border-radius: 6px;
        }
        .logout:hover {
            background: #B71C1C;
        }
        ul {
            list-style: none;
            padding-left: 0;
        }
        li {
            background: #EDE7F6;
            margin: 5px 0;
            padding: 10px;
            border-radius: 6px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #4A148C;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            transition: 0.3s;
        }
        .btn:hover {
            background: #7B1FA2;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="logout.php" class="logout">Logout</a>
        <h2>Welcome, <?php echo htmlspecialchars($fullname); ?></h2>

        <h3>Your Classes</h3>
        <ul>
            <?php while ($row = $result->fetch_assoc()): ?>
                <li><?php echo htmlspecialchars($row['class_name']); ?></li>
            <?php endwhile; ?>
        </ul>

        <br>
        <a href="scan_qr.php" class="btn">📷 Scan QR to Mark Attendance</a>
         <h3>Your Attendance Records</h3>
        <table>
            <tr>
                <th>Student Name</th>
                <th>Class</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
            </tr>
            <?php
            $att_sql = "SELECT c.class_name, a.attendance_time, a.status 
                        FROM attendance a
                        JOIN classes c ON a.class_id = c.id
                        WHERE a.student_id = ?
                        ORDER BY a.attendance_time DESC";
            $att_stmt = $conn->prepare($att_sql);
            $att_stmt->bind_param("i", $student_id);
            $att_stmt->execute();
            $att_result = $att_stmt->get_result();

            if ($att_result->num_rows > 0) {
                while ($row = $att_result->fetch_assoc()) {
                    $date = date('Y-m-d', strtotime($row['attendance_time']));
                    $time = date('h:i A', strtotime($row['attendance_time']));
                    echo "<tr>
                            <td>" . htmlspecialchars($fullname) . "</td>
                            <td>" . htmlspecialchars($row['class_name']) . "</td>
                            <td>$date</td>
                            <td>$time</td>
                            <td>" . htmlspecialchars($row['status']) . "</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No attendance records found.</td></tr>";
            }
            ?>
        </table>
    </div>
</body>
</html> -->




<?php
// Show all errors for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include 'db_connect.php';

// Restrict access to logged-in students only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: landing.php');
    exit;
}

$student_id = $_SESSION['user_id'];
$fullname = $_SESSION['fullname'] ?? 'Student';

// Fetch enrolled classes
$class_sql = "SELECT c.* 
              FROM classes c 
              JOIN enrollment e ON c.id = e.class_id 
              WHERE e.student_id = ?";
$class_stmt = $conn->prepare($class_sql);
$class_stmt->bind_param("i", $student_id);
$class_stmt->execute();
$class_result = $class_stmt->get_result();

// Fetch attendance records
$att_sql = "SELECT c.class_name, a.time_marked, a.status 
            FROM attendance a
            JOIN classes c ON a.class_id = c.id
            WHERE a.student_id = ?
            ORDER BY a.time_marked DESC";
$att_stmt = $conn->prepare($att_sql);
$att_stmt->bind_param("i", $student_id);
$att_stmt->execute();
$att_result = $att_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background-color: #F4F6F7;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 90%;
            max-width: 1000px;
            margin: 50px auto;
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        h2 { color: #4A148C; }
        .logout {
            float: right;
            background: #D32F2F;
            color: white;
            padding: 8px 14px;
            text-decoration: none;
            border-radius: 6px;
        }
        .logout:hover { background: #B71C1C; }
        ul { list-style: none; padding-left: 0; }
        li { background: #EDE7F6; margin: 5px 0; padding: 10px; border-radius: 6px; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th { background-color: #BA68C8; color: white; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #4A148C;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            transition: 0.3s;
            margin-top: 20px;
        }
        .btn:hover { background: #7B1FA2; }
    </style>
</head>
<body>
    <div class="container">
        <a href="logout.php" class="logout">Logout</a>
        <h2>Welcome, <?php echo htmlspecialchars($fullname); ?></h2>

        <h3>Your Classes</h3>
        <?php if ($class_result->num_rows > 0): ?>
            <ul>
                <?php while ($row = $class_result->fetch_assoc()): ?>
                    <li><?php echo htmlspecialchars($row['class_name']); ?></li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>You are not enrolled in any classes.</p>
        <?php endif; ?>

        <a href="scan_qr.php" class="btn">📷 Scan QR to Mark Attendance</a>

        <h3>Your Attendance Records</h3>
        <table>
            <tr>
                <th>Student Name</th>
                <th>Class</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
            </tr>
            <?php if ($att_result->num_rows > 0): ?>
                <?php while ($row = $att_result->fetch_assoc()): ?>
                    <?php
                        $date = date('Y-m-d', strtotime($row['time_marked']));
                        $time = date('h:i A', strtotime($row['time_marked']));
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($fullname); ?></td>
                        <td><?php echo htmlspecialchars($row['class_name']); ?></td>
                        <td><?php echo $date; ?></td>
                        <td><?php echo $time; ?></td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5">No attendance records found.</td></tr>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>

<?php
$class_stmt->close();
$att_stmt->close();
$conn->close();
?>
