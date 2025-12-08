<?php
session_start();
include 'db_connect.php';

// Only admin can access this page
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$error = "";
$success = "";

// When form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $subject_name  = trim($_POST['subject_name']);
    $subject_code  = trim($_POST['subject_code']);
    $class_name    = trim($_POST['class_name']);

    // Check duplicate subject code
    $check = $conn->prepare("SELECT * FROM subject WHERE subject_code = ?");
    $check->bind_param("s", $subject_code);
    $check->execute();
    $res = $check->get_result();

    if ($res->num_rows > 0) {
        $error = "Subject code already exists!";
    } else {
        $stmt = $conn->prepare("INSERT INTO subject (subject_name, subject_code, class_name) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $subject_name, $subject_code, $class_name);

        if ($stmt->execute()) {
            $success = "Subject added successfully!";
        } else {
            $error = "Error adding subject!";
        }

        $stmt->close();
    }

    $check->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Subject</title>

    <style>
    body {
        font-family: Arial, sans-serif;
        background: #F3E5F5; /* soft lavender */
        padding: 20px;
        color: #4B5563;
    }

    form {
        width: 450px;
        margin: auto;
        background: #FFFFFF;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0px 4px 12px rgba(106, 27, 154, 0.15);
    }

    input, select {
        width: 100%;
        padding: 12px;
        margin: 12px 0;
        border: 1px solid #D1C4E9; /* soft purple border */
        border-radius: 6px;
        outline: none;
        transition: 0.2s;
    }

    input:focus, select:focus {
        border-color: #6A1B9A; /* deep purple focus */
        box-shadow: 0px 0px 6px rgba(106, 27, 154, 0.3);
    }

    button {
        width: 100%;
        padding: 12px;
        background: #6A1B9A; /* main purple */
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        transition: 0.2s;
    }

    button:hover {
        background: #8E24AA; /* darker purple hover */
    }

    .success {
        color: #4CAF50;
        text-align: center;
        font-weight: bold;
    }

    .error {
        color: #E53935;
        text-align: center;
        font-weight: bold;
    }

    a {
        text-decoration: none;
        color: #6A1B9A; /* purple link */
        display: block;
        text-align: center;
        margin-top: 10px;
        font-weight: 500;
    }

    a:hover {
        color: #8E24AA;
        text-decoration: underline;
    }
</style>

</head>
<body>

<h2 style="text-align:center;">Add New Subject</h2>

<?php
if ($error) echo "<p class='error'>$error</p>";
if ($success) echo "<p class='success'>$success</p>";
?>

<form method="POST">

    <label>Subject Name</label>
    <input type="text" name="subject_name" required>

    <label>Subject Code</label>
    <input type="text" name="subject_code" required>

    <label>Select Class</label>
    <select name="class_name" required>
        <option value="">-- Select Class --</option>
        <option value="1stSem">1stSem</option>
        <option value="3srdSem">3rdSem</option>
        <option value="4thSem">4thSem</option>
        <option value="5thSem">5thSem</option>
        <option value="7thSem">7thSem</option>
    </select>

    <button type="submit">Add Subject</button>

    <a href="manage_subjects.php">← Back to Subject List</a>

</form>

</body>
</html>
