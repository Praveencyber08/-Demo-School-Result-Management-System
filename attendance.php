<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit;
}

$role = $_SESSION['role'];
$error = null;
$success = null;

// Handle attendance submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_attendance'])) {
    $student_id = filter_input(INPUT_POST, 'student_id', FILTER_SANITIZE_STRING);
    $date = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_STRING);
    $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);

    if (empty($student_id) || empty($date) || empty($status) || !in_array($status, ['present', 'absent'])) {
        $error = "Please fill in all fields correctly.";
    } elseif (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $date)) {
        $error = "Invalid date format.";
    } else {
        $stmt = $conn->prepare("INSERT INTO attendance (student_id, date, status) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $student_id, $date, $status);
        if ($stmt->execute()) {
            $success = "Attendance recorded successfully.";
            $stmt = $conn->prepare("INSERT INTO logs (user_id, action) VALUES (?, ?)");
            $action = "Marked attendance for student: $student_id on $date";
            $stmt->bind_param("is", $_SESSION['user_id'], $action);
            $stmt->execute();
        } else {
            $error = "Failed to record attendance. Possibly already recorded for this date.";
        }
    }
}

// Fetch attendance records with optional filters
$filter_date = filter_input(INPUT_GET, 'filter_date', FILTER_SANITIZE_STRING);
$filter_class = filter_input(INPUT_GET, 'filter_class', FILTER_SANITIZE_STRING);
$query = "SELECT a.*, s.first_name, s.last_name, s.class 
          FROM attendance a 
          JOIN students s ON a.student_id = s.student_id";
$conditions = [];
$params = [];
$types = "";

if ($filter_date && preg_match("/^\d{4}-\d{2}-\d{2}$/", $filter_date)) {
    $conditions[] = "a.date = ?";
    $params[] = $filter_date;
    $types .= "s";
}
if ($filter_class) {
    $conditions[] = "s.class = ?";
    $params[] = $filter_class;
    $types .= "s";
}

if (!empty($conditions)) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}
$query .= " ORDER BY a.date DESC, s.first_name";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$attendance_records = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch distinct classes for filter
$classes = $conn->query("SELECT DISTINCT class FROM students")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h3 class="fs-4">SRMS</h3>
            </div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php"><i class="bi bi-house-door"></i> Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="manage_students.php"><i class="bi bi-person-fill"></i> Manage Students</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="enter_results.php"><i class="bi bi-pencil-square"></i> Enter Results</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="view_reports.php"><i class="bi bi-bar-chart"></i> View Reports</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="attendance.php"><i class="bi bi-calendar-check"></i> Attendance</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
                </li>
            </ul>
        </div>
        <!-- Main Content -->
        <div class="main-content">
            <div class="container-fluid mt-4">
                <h2 class="welcome-header mb-4">Attendance Management</h2>
                <?php if ($success): ?>
                    <div class="

alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <!-- Attendance Form -->
                <div class="card dashboard-card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Mark Attendance</h5>
                        <form action="attendance.php" method="POST">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="student_id" class="form-label">Student</label>
                                    <select class="form-control" id="student_id" name="student_id" required>
                                        <option value="">Select Student</option>
                                        <?php
                                        $result = $conn->query("SELECT student_id, first_name, last_name FROM students ORDER BY first_name");
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<option value='" . htmlspecialchars($row['student_id']) . "'>" . 
                                                 htmlspecialchars($row['first_name'] . " " . $row['last_name']) . 
                                                 " (" . htmlspecialchars($row['student_id']) . ")</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="date" class="form-label">Date</label>
                                    <input type="date" class="form-control" id="date" name="date" required 
                                           value="<?php echo date('Y-m-d'); ?>">
                                </div>
                                <div class="col-md-4">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-control" id="status" name="status" required>
                                        <option value="present">Present</option>
                                        <option value="absent">Absent</option>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" name="mark_attendance" class="btn btn-primary mt-3">Mark Attendance</button>
                        </form>
                    </div>
                </div>
                <!-- Attendance Filter -->
                <div class="card dashboard-card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Filter Attendance</h5>
                        <form action="attendance.php" method="GET" class="row g-3">
                            <div class="col-md-4">
                                <label for="filter_date" class="form-label">Date</label>
                                <input type="date" class="form-control" id="filter_date" name="filter_date" 
                                       value="<?php echo htmlspecialchars($filter_date ?? ''); ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="filter_class" class="form-label">Class</label>
                                <select class="form-control" id="filter_class" name="filter_class">
                                    <option value="">All Classes</option>
                                    <?php foreach ($classes as $class): ?>
                                        <option value="<?php echo htmlspecialchars($class['class']); ?>" 
                                                <?php echo ($filter_class === $class['class']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($class['class']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4 align-self-end">
                                <button type="submit" class="btn btn-primary w-100">Apply Filter</button>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- Attendance Records -->
                <div class="card dashboard-card">
                    <div class="card-body">
                        <h5 class="card-title">Attendance Records</h5>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Student ID</th>
                                        <th>Name</th>
                                        <th>Class</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($attendance_records as $record): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($record['student_id']); ?></td>
                                            <td><?php echo htmlspecialchars($record['first_name'] . ' ' . $record['last_name']); ?></td>
                                            <td><?php echo htmlspecialchars($record['class']); ?></td>
                                            <td><?php echo htmlspecialchars($record['date']); ?></td>
                                            <td>
                                                <span class="badge <?php echo $record['status'] === 'present' ? 'bg-success' : 'bg-danger'; ?>">
                                                    <?php echo ucfirst(htmlspecialchars($record['status'])); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($attendance_records)): ?>
                                        <tr><td colspan="5" class="text-center">No records found.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.js"></script>
</body>
</html>