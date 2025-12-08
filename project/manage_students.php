<?php
session_start();
include 'db_connect.php';

// Only admin can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Handle Approve / Reject / Delete actions
if (isset($_GET['action'], $_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];

    if ($action === 'approve') {
        $conn->query("UPDATE users SET status='approved' WHERE user_id=$id");
    } elseif ($action === 'reject') {
        $conn->query("UPDATE users SET status='rejected' WHERE user_id=$id");
    } elseif ($action === 'delete') {
        $conn->query("DELETE FROM users WHERE user_id=$id");
    }

    header("Location: manage_student.php");
    exit;
}

// Fetch students
$sql = "SELECT * FROM users WHERE role='student'";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Students</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background: #F3E5F5; /* soft lavender background */
        color: #4B5563; /* neutral dark text */
    }

    table {
        width: 90%;
        margin: 20px auto;
        border-collapse: collapse;
        background: #FFFFFF;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(106, 27, 154, 0.1);
    }

    th, td {
        padding: 12px 15px;
        text-align: left;
    }

    th {
        background: #6A1B9A; /* deep purple */
        color: white;
        font-weight: 600;
    }

    tr:nth-child(even) {
        background: #FAF5FF; /* very light purple for rows */
    }

    tr:hover {
        background: #E9D5FF; /* light hover effect */
    }

    a.button {
        padding: 6px 12px;
        text-decoration: none;
        color: white;
        border-radius: 6px;
        margin-right: 5px;
        font-size: 14px;
        transition: 0.2s;
    }

    a.approve { 
        background: #4CAF50;
    }
    a.approve:hover {
        background: #45A049;
    }

    a.reject { 
        background: #E53935; 
    }
    a.reject:hover {
        background: #D32F2F;
    }

    a.delete { 
        background: #6A1B9A; 
    }
    a.delete:hover {
        background: #8E24AA; 
    }
</style>

</head>
<body>

<h2 style="text-align:center;">Manage Students</h2>

<table>
    <tr>
        <th>ID</th>
        <th>Full Name</th>
        <th>Email</th>
        <th>Batch</th>
        <th>Department</th>
        <th>Status</th>
        <th>Action</th>
    </tr>

    <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?= $row['user_id'] ?></td>
                <td><?= $row['fullname'] ?></td>
                <td><?= $row['email'] ?></td>
                <td><?= $row['batch'] ?></td>
                <td><?= $row['department'] ?></td>
                <td><?= ucfirst($row['status']) ?></td>
                <td>

                    <?php if ($row['status'] !== 'approved'): ?>
                        <a class="button approve" href="manage_student.php?action=approve&id=<?= $row['user_id'] ?>">Approve</a>
                    <?php endif; ?>

                    <?php if ($row['status'] !== 'rejected'): ?>
                        <a class="button reject" href="manage_student.php?action=reject&id=<?= $row['user_id'] ?>">Reject</a>
                    <?php endif; ?>

                    <a class="button delete" href="manage_student.php?action=delete&id=<?= $row['user_id'] ?>" onclick="return confirm('Delete this student?')">Delete</a>

                </td>
            </tr>
        <?php endwhile; ?>

    <?php else: ?>
        <tr>
            <td colspan="7" style="text-align:center;">No students found</td>
        </tr>
    <?php endif; ?>
</table>

</body>
</html>
