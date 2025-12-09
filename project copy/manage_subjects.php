<?php
session_start();
include 'db_connect.php';

// Only admin can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Fetch subjects
$sql = "SELECT * FROM subject ORDER BY sub_name";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Subjects</title>

   <style>
    body {
        font-family: Arial, sans-serif;
        background: #F3E5F5; /* soft lavender */
        padding: 20px;
        color: #4B5563;
    }

    h2 {
        text-align: center;
        margin-bottom: 20px;
        color: #6A1B9A; /* primary purple */
    }

    .table-container {
        width: 90%;
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
        background: #6A1B9A; /* deep purple */
        color: white;
        font-weight: 600;
    }

    tr:nth-child(even) {
        background: #FAF5FF; /* light purple */
    }

    tr:hover {
        background: #E9D5FF; /* hover highlight */
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

    .edit {
        background: #8E24AA; /* dark purple */
    }

    .edit:hover {
        background: #6A1B9A;
    }

    .delete {
        background: #E53935; /* red for delete */
    }

    .delete:hover {
        background: #D32F2F;
    }

    .add-btn {
        background: #6A1B9A;
        margin-bottom: 15px;
        display: inline-block;
        text-decoration: none;
        padding: 10px 15px;
        color: white;
        border-radius: 5px;
        transition: 0.2s;
    }

    .add-btn:hover {
        background: #8E24AA;
    }
</style>
</head>
<body>

<h2>Manage Subjects</h2>

<div class="table-container">

    <a href="add_subject.php" class="add-btn">+ Add Subject</a>

    <table>
        <tr>
            <th>Subject ID</th>
            <th>Subject Name</th>
            <th>Subject Code</th>
            <th>Class</th>
            <th>Actions</th>
        </tr>

        <?php
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td>" . $row['subject_id'] . "</td>
                    <td>" . $row['sub_name'] . "</td>
                    <td>" . $row['subject_code'] . "</td>
                    <td>" . $row['class_name'] . "</td>
                    <td>
                        <a href='edit_subject.php?id=" . $row['subject_id'] . "'>
                            <button class='btn edit'>Edit</button>
                        </a>
                        <a href='delete_subject.php?id=" . $row['subject_id'] . "' onclick='return confirm(\"Delete this subject?\")'>
                            <button class='btn delete'>Delete</button>
                        </a>
                    </td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No subjects found</td></tr>";
        }
        ?>

    </table>
</div>

</body>
</html>

<?php $conn->close(); ?>
