<?php
require 'config.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: student_login.php");
    exit;
}

// Fetch attendance records with optional date filter
$filter_date = filter_input(INPUT_GET, 'filter_date', FILTER_SANITIZE_STRING);
$query = "SELECT date, status FROM attendance WHERE student_id = ?";
$params = [$_SESSION['student_id']];
$types = "s";

if ($filter_date && preg_match("/^\d{4}-\d{2}-\d{2}$/", $filter_date)) {
    $query .= " AND date = ?";
    $params[] = $filter_date;
    $types .= "s";
}
$query .= " ORDER BY date DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$attendance_records = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Attendance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h3 class="fs-4">Student Portal</h3>
            </div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="student_dashboard.php"><i class="bi bi-house-door"></i> Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="student_attendance.php"><i class="bi bi-calendar-check"></i> Attendance</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="student_payments.php"><i class="bi bi-wallet2"></i> Payments</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="student_results.php"><i class="bi bi-bar-chart"></i> Results</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="student_logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
                </li>
            </ul>
        </div>
        <!-- Main Content -->
        <div class="main-content">
            <div class="container-fluid mt-4">
                <h2 class="welcome-header mb-4">Your Attendance</h2>
                <div class="card dashboard-card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Filter Attendance</h5>
                        <form action="student_attendance.php" method="GET">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="filter_date" class="form-label">Date</label>
                                    <input type="date" class="form-control" id="filter_date" name="filter_date" 
                                           value="<?php echo htmlspecialchars($filter_date ?? ''); ?>">
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary mt-4">Apply Filter</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card dashboard-card">
                    <div class="card-body">
                        <h5 class="card-title">Attendance Records</h5>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($attendance_records as $record): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($record['date']); ?></td>
                                            <td>
                                                <span class="badge <?php echo $record['status'] === 'present' ? 'bg-success' : 'bg-danger'; ?>">
                                                    <?php echo ucfirst(htmlspecialchars($record['status'])); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($attendance_records)): ?>
                                        <tr><td colspan="2" class="text-center">No attendance records found.</td></tr>
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