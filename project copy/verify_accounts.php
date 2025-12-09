<?php
session_start();
include 'db_connect.php';

// Only admin access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Handle Approve action
if (isset($_GET['action']) && isset($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']);
    $action = $_GET['action'];

    if ($action == "approve") {
        mysqli_query($conn, "UPDATE users SET is_verified = 1 WHERE user_id = $user_id");
    }
    header("Location: verify_accounts.php");
    exit;
}

// Fetch all teachers & students (exclude admin)
$users = mysqli_query($conn, "SELECT * FROM users WHERE role != 'admin'");
if (!$users) {
    die("Error fetching users: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Account Verification</title>
    <style>
        body{
            background:#F3E5F5;
            font-family: Arial, sans-serif;
            text-align:center;
            padding-top: 80px;
        }
        .box{
            background:#fff;
            width: 450px;
            margin: auto;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }
        h1{
            color:#8E24AA;
            margin-bottom:15px;
            display:flex;
        }
        p{
            font-size:17px;
            color:#4A148C;
        }
        .btn{
            display:inline-block;
            padding:10px 20px;
            background:#8E24AA;
            color:#fff;
            text-decoration:none;
            border-radius:8px;
            margin-top:15px;
            font-size:16px;
        }
        .btn:hover{
            background:#6A1B9A;
        }
        .error{
            color:#C62828;
            font-size:18px;
            font-weight:bold;
        }
        .success{
            color:#2E7D32;
            font-size:18px;
            font-weight:bold;
        }
    </style>
</head>
<body>

<h1>Verify User Accounts</h1>

<table>
    <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Role</th>
        <th>Status</th>
        <th>Action</th>
    </tr>

    <?php while ($row = mysqli_fetch_assoc($users)) : ?>
    <tr>
        <td><?php echo htmlspecialchars($row['fullname']); ?></td>
        <td><?php echo htmlspecialchars($row['email']); ?></td>
        <td><?php echo ucfirst($row['role']); ?></td>
        <td>
            <?php echo ($row['is_verified'] == 1) ? "Approved" : "Pending"; ?>
        </td>
        <td>
            <?php if ($row['is_verified'] == 0): ?>
                <a class="btn approve" href="verify_accounts.php?action=approve&user_id=<?php echo $row['user_id']; ?>">Approve</a>
            <?php else: ?>
                <span class="btn approved">Approved</span>
            <?php endif; ?>
        </td>
    </tr>
    <?php endwhile; ?>

</table>

</body>
</html>
