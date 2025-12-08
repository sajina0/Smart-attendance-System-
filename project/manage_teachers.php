<?php
session_start();
include 'db_connect.php';

// Allow only admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Fetch teachers
$sql = "SELECT user_id, fullname, email FROM users WHERE role = 'teacher'";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Teachers</title>

   <style>
    body {
        font-family: Arial, sans-serif;
        background: #F3E5F5; /* soft lavender background */
        padding: 20px;
        color: #4B5563; /* neutral dark text */
    }

    h2 {
        text-align: center;
        margin-bottom: 20px;
        color: #6A1B9A; /* dashboard primary color */
    }

    .table-container {
        width: 90%;
        margin: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background: #FFFFFF;
        box-shadow: 0px 4px 12px rgba(106, 27, 154, 0.1);
        border-radius: 10px;
        overflow: hidden;
    }

    th, td {
        padding: 12px;
        text-align: center;
        border: 1px solid #ddd;
    }

    th {
        background: #6A1B9A; /* deep purple header */
        color: white;
        font-weight: 600;
    }

    tr:nth-child(even) {
        background: #FAF5FF; /* light purple even rows */
    }

    tr:hover {
        background: #E9D5FF; /* light hover effect */
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
        background: #8E24AA; /* primary dark purple */
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
        background: #6A1B9A; /* matches dashboard */
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

<h2>Manage Teachers</h2>

<div class="table-container">

    <a href="add_teacher.php" class="add-btn">+ Add Teacher</a>

    <table>
        <tr>
            <th>Teacher ID</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>

        <?php
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td>" . $row['user_id'] . "</td>
                    <td>" . $row['fullname'] . "</td>
                    <td>" . $row['email'] . "</td>
                    <td>
                        <a href='edit_teacher.php?user_id=" . $row['user_id'] . "'>
                            <button class='btn edit'>Edit</button>
                        </a>
                        <a href='delete_teacher.php?user_id=" . $row['user_id'] . "' onclick='return confirm(\"Are you sure?\")'>
                            <button class='btn delete'>Delete</button>
                        </a>
                    </td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No teachers found</td></tr>";
        }
        ?>

    </table>
</div>

</body>
</html>

<?php $conn->close(); ?>
