<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "smart_attendance");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to get students with attendance count
$sql = "SELECT 
    s.user_id, 
    s.student_name,
    SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) AS present,
    SUM(CASE WHEN a.status = 'absent' THEN 1 ELSE 0 END) AS absent
FROM student s
LEFT JOIN attendance a ON s.user_id = a.student_id
GROUP BY s.user_id, s.student_name
ORDER BY s.student_name";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Attendance Report</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: #f5f7ff;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 80%;
            margin: auto;
            border-collapse: collapse;
            background: white;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }
        th {
            background: #4CAF50;
            color: white;
        }
        tr:nth-child(even){
            background: #f9f9f9;
        }
    </style>

</head>
<body>

<h2>Student Attendance Report</h2>

<table>
    <tr>
        <th>Student ID</th>
        <th>Name</th>
        <th>Present</th>
        <th>Absent</th>
    </tr>

    <?php
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                <td>" . $row['user_id'] . "</td>
                <td>" . $row['student_name'] . "</td>
                <td>" . $row['present'] . "</td>
                <td>" . $row['absent'] . "</td>
            </tr>";
        }
    } else {
        echo "<tr><td colspan='4'>No students found</td></tr>";
    }
    ?>

</table>

</body>
</html>

<?php
$conn->close();
?>
