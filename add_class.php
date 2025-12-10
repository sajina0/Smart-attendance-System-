<?php
session_start();
include 'db_connect.php';

// Only admin can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$error = "";
$success = "";

// When form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $class_name  = trim($_POST['class_name']);

    // Check duplicate class
    $check = $conn->prepare("SELECT * FROM class WHERE class_name = ?");
    $check->bind_param("s", $class_name);
    $check->execute();
    $res = $check->get_result();

    if ($res->num_rows > 0) {
        $error = "Class already exists!";
    } else {
        $stmt = $conn->prepare("INSERT INTO class (class_name) VALUES (?)");
        $stmt->bind_param("s", $class_name);

        if ($stmt->execute()) {
            $success = "Class added successfully!";
        } else {
            $error = "Error adding class!";
        }

        $stmt->close();
    }

    $check->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Class</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #F3E5F5;
            padding: 20px;
            color: #4B5563;
        }
        form {
            width: 400px;
            margin: auto;
            background: #FFFFFF;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0px 4px 12px rgba(106, 27, 154, 0.15);
        }
        input {
            width: 100%;
            padding: 12px;
            margin: 12px 0;
            border: 1px solid #D1C4E9;
            border-radius: 6px;
            outline: none;
        }
        input:focus {
            border-color: #6A1B9A;
            box-shadow: 0px 0px 6px rgba(106, 27, 154, 0.3);
        }
        button {
            width: 100%;
            padding: 12px;
            background: #6A1B9A;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }
        button:hover {
            background: #8E24AA;
        }
        .success { color: #4CAF50; text-align:center; font-weight:bold; }
        .error   { color: #E53935; text-align:center; font-weight:bold; }
        a { text-decoration: none; display: block; text-align: center; margin-top: 10px; color:#6A1B9A; }
        a:hover { color:#8E24AA; text-decoration: underline; }
    </style>
</head>
<body>

<h2 style="text-align:center;">Add New Class</h2>

<?php
if ($error) echo "<p class='error'>$error</p>";
if ($success) echo "<p class='success'>$success</p>";
?>

<form method="POST">
    <label>Class Name</label>
    <input type="text" name="class_name" required>
    <label>Class Id</label>
    <input type="text" name="" id="">

    <button type="submit">Add Class</button>

    <a href="manage_classes.php">← Back to Class List</a>
</form>

</body>
</html>
