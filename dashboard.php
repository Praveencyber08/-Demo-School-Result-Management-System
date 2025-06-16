<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit;
}

$role = $_SESSION['role'];

// Fetch dashboard data
$stmt = $conn->prepare("SELECT COUNT(*) as student_count FROM students");
$stmt->execute();
$student_count = $stmt->get_result()->fetch_assoc()['student_count'];

$stmt = $conn->prepare("SELECT subject, AVG(marks) as avg_marks, MAX(marks) as max_marks FROM results GROUP BY subject");
$stmt->execute();
$result = $stmt->get_result();
$subjects = [];
$avg_marks = [];
$max_marks = [];
while ($row = $result->fetch_assoc()) {
    $subjects[] = $row['subject'];
    $avg_marks[] = $row['avg_marks'];
    $max_marks[] = $row['max_marks'];
}

$stmt = $conn->prepare("SELECT s.student_id, s.first_name, s.last_name, AVG(r.marks) as avg_score 
                       FROM students s 
                       JOIN results r ON s.student_id = r.student_id 
                       GROUP BY s.student_id 
                       ORDER BY avg_score DESC 
                       LIMIT 3");
$stmt->execute();
$top_students = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$stmt = $conn->prepare("SELECT action, created_at FROM logs WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$recent_activities = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                    <a class="nav-link active" href="dashboard.php"><i class="bi bi-house-door"></i> Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="attendance.php"><i class="bi bi-box-arrow-right"></i> Attendance</a>
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
                    <a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
                </li>

            </ul>
        </div>
        <!-- Main Content -->
        <div class="main-content">
            <div class="container-fluid mt-4">
                <h2 class="welcome-header mb-4">Welcome, <?php echo htmlspecialchars($role); ?>!</h2>
                <div class="row g-4">
                    <div class="col-md-4 col-sm-6">
                        <div class="card dashboard-card">
                            <div class="card-body text-center">
                                <i class="bi bi-person-fill card-icon"></i>
                                <h5 class="card-title mt-3">Total Students</h5>
                                <p class="card-text display-6"><?php echo $student_count; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="card dashboard-card">
                            <div class="card-body text-center">
                                <i class="bi bi-book-fill card-icon"></i>
                                <h5 class="card-title mt-3">Subjects Tracked</h5>
                                <p class="card-text display-6"><?php echo count($subjects); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12">
                        <div class="card dashboard-card">
                            <div class="card-body">
                                <h5 class="card-title">Top Performers</h5>
                                <ul class="list-unstyled">
                                    <?php foreach ($top_students as $student): ?>
                                        <li>
                                            <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?>
                                            <div class="progress mt-2" style="height: 10px;">
                                                <div class="progress-bar bg-success" role="progressbar" 
                                                     style="width: <?php echo $student['avg_score']; ?>%;" 
                                                     aria-valuenow="<?php echo $student['avg_score']; ?>" 
                                                     aria-valuemin="0" aria-valuemax="100">
                                                </div>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card dashboard-card chart-container">
                            <div class="card-body">
                                <h5 class="card-title">Class Performance Overview</h5>
                                <canvas id="performanceChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card dashboard-card">
                            <div class="card-body">
                                <h5 class="card-title">Recent Activity</h5>
                                <ul class="list-unstyled activity-list">
                                    <?php foreach ($recent_activities as $activity): ?>
                                        <li class="activity-item">
                                            <?php echo htmlspecialchars($activity['action']) . " - " . date('M d, H:i', strtotime($activity['created_at'])); ?>
                                        </li>
                                    <?php endforeach; ?>
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
    <script>
        const ctx = document.getElementById('performanceChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($subjects); ?>,
                datasets: [
                    {
                        label: 'Average Marks',
                        data: <?php echo json_encode($avg_marks); ?>,
                        backgroundColor: 'rgba(0, 123, 255, 0.6)',
                        borderColor: 'rgba(0, 123, 255, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Highest Marks',
                        data: <?php echo json_encode($max_marks); ?>,
                        backgroundColor: 'rgba(255, 99, 132, 0.6)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top', labels: { font: { family: 'Poppins', size: 14 } } },
                    title: { 
                        display: true, 
                        text: 'Subject Performance Metrics', 
                        font: { family: 'Poppins', size: 18, weight: 'bold' } 
                    },
                    tooltip: { 
                        backgroundColor: '#343a40', 
                        titleFont: { family: 'Poppins' }, 
                        bodyFont: { family: 'Poppins' } 
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        title: { 
                            display: true, 
                            text: 'Marks', 
                            font: { family: 'Poppins', size: 14 } 
                        },
                        grid: { color: 'rgba(0,0,0,0.1)' }
                    },
                    x: {
                        title: { 
                            display: true, 
                            text: 'Subjects', 
                            font: { family: 'Poppins', size: 14 } 
                        },
                        grid: { display: false }
                    }
                },
                animation: {
                    duration: 1500,
                    easing: 'easeOutQuart'
                }
            }
        });
    </script>
</body>
</html>