
<?php
session_start();
include 'db_connect.php';

/* =============================
   ACCESS CHECK (ADMIN & TEACHER)
============================= */
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'teacher'])) {
    echo "Unauthorized Access";
    exit;
}

/* =============================
   FETCH CLASSES BASED ON ROLE
============================= */
if ($_SESSION['role'] === 'admin') {
    // Admin sees all classes
    $classes = $conn->query("SELECT class_id, class_name FROM classes");
} else {
    // Teacher sees only own classes
    $teacher_id = $_SESSION['user_id'];
    $classes = $conn->query(
        "SELECT class_id, class_name FROM classes WHERE teacher_id = $teacher_id"
    );
}

/* =============================
   FILTER VALUES
============================= */
$class_id = $_GET['class_id'] ?? '';
$date     = $_GET['date'] ?? '';

/* =============================
   FETCH ATTENDANCE
============================= */
$records = [];

if (!empty($class_id)) {

    $query = "SELECT 
                s.session_date,
                st.fullname AS student_name,
                a.status
              FROM attendance a
              JOIN sessions s ON a.session_id = s.id
              JOIN student st ON a.student_id = st.student_id
              WHERE s.class_id = ?";

    if (!empty($date)) {
        $query .= " AND s.session_date = ?";
    }

    $stmt = $conn->prepare($query);

    if (!empty($date)) {
        $stmt->bind_param("is", $class_id, $date);
    } else {
        $stmt->bind_param("i", $class_id);
    }

    $stmt->execute();
    $result  = $stmt->get_result();
    $records = $result->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>View Attendance</title>
<style>
body{
    font-family: "Segoe UI";
    background:#f9f9f9;
    padding:30px;
}
h2{color:#6A1B9A;}
table{
    width:100%;
    border-collapse:collapse;
    background:#fff;
    margin-top:20px;
}
th,td{
    border:1px solid #ddd;
    padding:10px;
}
th{
    background:#6A1B9A;
    color:#fff;
}
tr:nth-child(even){
    background:#f3e5f5;
}
select,input[type=date],button{
    padding:8px;
    border-radius:5px;
    border:1px solid #ccc;
}
button{
    background:#6A1B9A;
    color:#fff;
    border:none;
    cursor:pointer;
}
</style>
</head>

<body>

<h2>View Attendance</h2>

<form method="GET">
    <label>Select Class:</label>
    <select name="class_id" required>
        <option value="">-- Choose Class --</option>
        <?php while ($c = $classes->fetch_assoc()):
            $selected = ($class_id == $c['class_id']) ? 'selected' : '';
        ?>
            <option value="<?= $c['class_id']; ?>" <?= $selected; ?>>
                <?= htmlspecialchars($c['class_name']); ?>
            </option>
        <?php endwhile; ?>
    </select>

    <label>Date:</label>
    <input type="date" name="date" value="<?= htmlspecialchars($date); ?>">

    <button type="submit">Filter</button>
</form>

<?php if (!empty($records)): ?>
<table>
    <tr>
        <th>Date</th>
        <th>Student Name</th>
        <th>Status</th>
    </tr>
    <?php foreach ($records as $r): ?>
    <tr>
        <td><?= htmlspecialchars($r['session_date']); ?></td>
        <td><?= htmlspecialchars($r['student_name']); ?></td>
        <td><?= htmlspecialchars($r['status']); ?></td>
    </tr>
    <?php endforeach; ?>
</table>

<?php elseif (!empty($class_id)): ?>
<p>No attendance data found.</p>
<?php endif; ?>

</body>
</html>
