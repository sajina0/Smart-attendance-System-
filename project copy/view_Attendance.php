<?php
session_start();
include 'db_connect.php';

// Ensure only admin can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  echo "Unauthorized Access";
  exit;
}

// Fetch classes for this admin
$admin_id = $_SESSION['user_id'];
$classes = $conn->query("SELECT * FROM class WHERE admin_id = $admin_id");

// Handle filter
$class_id = $_GET['class_id'] ?? '';
$date = $_GET['date'] ?? '';

$query = "SELECT s.session_date, s.name AS student_name, a.status 
          FROM attendance a
          JOIN sessions s ON a.session_id = s.id
          JOIN students st ON a.student_id = st.id
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

$records = [];
if ($class_id) {
  $stmt->execute();
  $result = $stmt->get_result();
  $records = $result->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View Attendance</title>
  <style>
    body { 
        font-family: "Segoe UI"; 
        background:#f9f9f9; padding:30px;
    }
    h2 { 
        color:#6A1B9A; 
    }
    table { 
        border-collapse: collapse;
        width: 100%;
        margin-top: 20px; 
        background:#fff; 
    }
    th, td {
        border: 1px solid #ddd;
        padding: 10px;
        text-align: left; 
    }
    th { 
        background:#6A1B9A;
        color:white; 
    }
    tr:nth-child(even) {
        background:#f3e5f5; 
    }
    select, input[type=date], button {
      padding: 8px;
      border-radius:5px;
      border:1px solid #ccc;
      margin-right:10px;
    }
    button { 
        background:#6A1B9A;
        color:white;
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
      <?php while ($c = $classes->fetch_assoc()) {
        $selected = ($class_id == $c['id']) ? 'selected' : '';
        echo "<option value='{$c['id']}' $selected>{$c['class_name']}</option>";
      } ?>
    </select>

    <label>Date:</label>
    <input type="date" name="date" value="<?php echo htmlspecialchars($date); ?>">

    <button type="submit">Filter</button>
  </form>

  <?php if ($records): ?>
  <table>
    <tr><th>Date</th><th>Student Name</th><th>Status</th></tr>
    <?php foreach ($records as $r): ?>
    <tr>
      <td><?php echo htmlspecialchars($r['session_date']); ?></td>
      <td><?php echo htmlspecialchars($r['student_name']); ?></td>
      <td><?php echo htmlspecialchars($r['status']); ?></td>
    </tr>
    <?php endforeach; ?>
  </table>
  <?php elseif ($class_id): ?>
  <p>No attendance data found.</p>
  <?php endif; ?>
</body>
</html>
