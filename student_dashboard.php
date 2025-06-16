<?php
require 'config.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: student_login.php");
    exit;
}

// Fetch student data
$stmt = $conn->prepare("SELECT s.first_name, s.last_name, s.class 
                       FROM students s 
                       WHERE s.student_id = ?");
$stmt->bind_param("s", $_SESSION['student_id']);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

// Fetch attendance summary
$stmt = $conn->prepare("SELECT 
                            COUNT(*) as total_days,
                            SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present_days
                        FROM attendance 
                        WHERE student_id = ?");
$stmt->bind_param("s", $_SESSION['student_id']);
$stmt->execute();
$attendance = $stmt->get_result()->fetch_assoc();
$attendance_percentage = $attendance['total_days'] > 0 
    ? round(($attendance['present_days'] / $attendance['total_days']) * 100, 2) 
    : 0;

// Fetch payment status
$stmt = $conn->prepare("SELECT COUNT(*) as pending_payments 
                        FROM payments 
                        WHERE student_id = ? AND status = 'pending'");
$stmt->bind_param("s", $_SESSION['student_id']);
$stmt->execute();
$pending_payments = $stmt->get_result()->fetch_assoc()['pending_payments'];

// Fetch upcoming events
$today = date('Y-m-d');
$events = $conn->query("SELECT * FROM events WHERE event_date >= '$today' ORDER BY event_date LIMIT 5")
    ->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
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
                    <a class="nav-link active" href="student_dashboard.php"><i class="bi bi-house-door"></i> Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="student_attendance.php"><i class="bi bi-calendar-check"></i> Attendance</a>
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
                <h2 class="welcome-header mb-4">Welcome, <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?>!</h2>
                <div class="row g-4">
                    <div class="col-md-4 col-sm-6">
                        <div class="card dashboard-card">
                            <div class="card-body text-center">
                                <i class="bi bi-calendar-check card-icon"></i>
                                <h5 class="card-title mt-3">Attendance</h5>
                                <p class="card-text">Attendance: <?php echo $attendance_percentage; ?>%</p>
                                <div class="progress" style="height: 10px;">
                                    <div class="progress-bar bg-success" role="progressbar" 
                                         style="width: <?php echo $attendance_percentage; ?>%;" 
                                         aria-valuenow="<?php echo $attendance_percentage; ?>" 
                                         aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="card dashboard-card">
                            <div class="card-body text-center">
                                <i class="bi bi-wallet2 card-icon"></i>
                                <h5 class="card-title mt-3">Fee Status</h5>
                                <p class="card-text"><?php echo $pending_payments > 0 ? "$pending_payments Pending Payment(s)" : "All Fees Paid"; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12">
                        <div class="card dashboard-card">
                            <div class="card-body">
                                <h5 class="card-title">Upcoming Events</h5>
                                <ul class="list-unstyled activity-list">
                                    <?php foreach ($events as $event): ?>
                                        <li class="activity-item">
                                            <strong><?php echo htmlspecialchars($event['title']); ?></strong><br>
                                            <small><?php echo htmlspecialchars($event['event_date']); ?></small><br>
                                            <?php echo htmlspecialchars(substr($event['description'], 0, 50)) . '...'; ?>
                                        </li>
                                    <?php endforeach; ?>
                                    <?php if (empty($events)): ?>
                                        <li>No upcoming events.</li>
                                    <?php endif; ?>
                                </ul>
                            </div>
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