<?php
session_start();
include 'db_connect.php';

// Only admin can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Fetch classes
$sql = "SELECT * FROM class ORDER BY class_name";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Classes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #F3E5F5;
            padding: 20px;
            color: #4B5563;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #6A1B9A;
        }

        .tablebox {
            width: 80%;
            margin: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #FFFFFF;
            box-shadow: 0px 4px 12px rgba(106, 27, 154, 0.15);
            border-radius: 10px;
            overflow: hidden;
        }

        th, td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ddd;
        }

        th {
            background: #6A1B9A;
            color: white;
            font-weight: 600;
        }

        tr:nth-child(even) {
            background: #FAF5FF;
        }

        tr:hover {
            background: #E9D5FF;
            transition: 0.2s;
        }

        /* Buttons */
        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            color: white;
            font-size: 14px;
            transition: 0.2s;
        }

        .edit { background: #8E24AA; }
        .edit:hover { background: #6A1B9A; }

        .delete { background: #E53935; }
        .delete:hover { background: #D32F2F; }

        .btn {
            background: #6A1B9A;
            margin-bottom: 15px;
            display: inline-block;
            text-decoration: none;
            padding: 10px 15px;
            color: white;
            border-radius: 5px;
            transition: 0.2s;
        }

        .add-btn:hover { background: #8E24AA; }
    </style>
</head>
<body>

<h2>Manage Classes</h2>

<div class="tablebox">

    <a href="add_class.php" class="btn">+ Add Class</a>

    <table>
        <tr>
            <th>Class ID</th>
            <th>Class Name</th>
            <th>Actions</th>
        </tr>

        <?php
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td>" . $row['class_id'] . "</td>
                    <td>" . $row['class_name'] . "</td>
                    <td>
                        <a href='edit_class.php?id=" . $row['class_id'] . "'>
                            <button class='btn edit'>Edit</button>
                        </a>
                        <a href='delete_class.php?id=" . $row['class_id'] . "' onclick='return confirm(\"Delete this class?\")'>
                            <button class='btn delete'>Delete</button>
                        </a>
                    </td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='3'>No classes found</td></tr>";
        }
        ?>
    </table>
</div>

</body>
</html>

<?php $conn->close(); ?>
