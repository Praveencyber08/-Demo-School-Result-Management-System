<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit;
}

$role = $_SESSION['role'];

// Log report access
$stmt = $conn->prepare("INSERT INTO logs (user_id, action) VALUES (?, 'Accessed Reports')");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();

// Fetch distinct classes
$classes = $conn->query("SELECT DISTINCT class FROM students")->fetch_all(MYSQLI_ASSOC);

// Handle filters
$filter_class = filter_input(INPUT_GET, 'filter_class', FILTER_SANITIZE_STRING);
$filter_semester = filter_input(INPUT_GET, 'filter_semester', FILTER_VALIDATE_INT);
$filter_date_start = filter_input(INPUT_GET, 'filter_date_start', FILTER_SANITIZE_STRING);
$filter_date_end = filter_input(INPUT_GET, 'filter_date_end', FILTER_SANITIZE_STRING);

// Class performance report
$class_query = "SELECT s.class, r.subject, AVG(r.marks) as avg_marks 
                FROM results r 
                JOIN students s ON r.student_id = s.student_id";
$class_conditions = [];
$class_params = [];
$class_types = "";
if ($filter_class) {
    $class_conditions[] = "s.class = ?";
    $class_params[] = $filter_class;
    $class_types .= "s";
}
if ($filter_semester) {
    $class_conditions[] = "r.semester = ?";
    $class_params[] = $filter_semester;
    $class_types .= "i";
}
if (!empty($class_conditions)) {
    $class_query .= " WHERE " . implode(" AND ", $class_conditions);
}
$class_query .= " GROUP BY s.class, r.subject";
$stmt = $conn->prepare($class_query);
if (!empty($class_params)) {
    $stmt->bind_param($class_types, ...$class_params);
}
$stmt->execute();
$class_performance = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Student performance report
$student_query = "SELECT s.student_id, s.first_name, s.last_name, s.class, r.subject, r.marks, r.semester 
                 FROM results r 
                 JOIN students s ON r.student_id = s.student_id";
$student_conditions = $class_conditions;
$student_params = $class_params;
$student_types = $class_types;
if (!empty($student_conditions)) {
    $student_query .= " WHERE " . implode(" AND ", $student_conditions);
}
$student_query .= " ORDER BY s.first_name, r.semester, r.subject";
$stmt = $conn->prepare($student_query);
if (!empty($student_params)) {
    $stmt->bind_param($student_types, ...$student_params);
}
$stmt->execute();
$student_performance = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Attendance summary
$attendance_query = "SELECT s.class, 
                        COUNT(*) as total_days, 
                        SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) as present_days
                     FROM attendance a 
                     JOIN students s ON a.student_id = s.student_id";
$attendance_conditions = [];
$attendance_params = [];
$attendance_types = "";
if ($filter_class) {
    $attendance_conditions[] = "s.class = ?";
    $attendance_params[] = $filter_class;
    $attendance_types .= "s";
}
if ($filter_date_start && preg_match("/^\d{4}-\d{2}-\d{2}$/", $filter_date_start)) {
    $attendance_conditions[] = "a.date >= ?";
    $attendance_params[] = $filter_date_start;
    $attendance_types .= "s";
}
if ($filter_date_end && preg_match("/^\d{4}-\d{2}-\d{2}$/", $filter_date_end)) {
    $attendance_conditions[] = "a.date <= ?";
    $attendance_params[] = $filter_date_end;
    $attendance_types .= "s";
}
if (!empty($attendance_conditions)) {
    $attendance_query .= " WHERE " . implode(" AND ", $attendance_conditions);
}
$attendance_query .= " GROUP BY s.class";
$stmt = $conn->prepare($attendance_query);
if (!empty($attendance_params)) {
    $stmt->bind_param($attendance_types, ...$attendance_params);
}
$stmt->execute();
$attendance_summary = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Prepare chart data
$chart_data = [];
foreach ($class_performance as $row) {
    $chart_data[$row['class']][$row['subject']] = $row['avg_marks'];
}
$all_subjects = array_unique(array_column($class_performance, 'subject'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Reports</title>
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
                    <a class="nav-link" href="dashboard.php"><i class="bi bi-house-door"></i> Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="manage_students.php"><i class="bi bi-person-fill"></i> Manage Students</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="enter_results.php"><i class="bi bi-pencil-square"></i> Enter Results</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="view_reports.php"><i class="bi bi-bar-chart"></i> View Reports</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="attendance.php"><i class="bi bi-calendar-check"></i> Attendance</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
                </li>
            </ul>
        </div>
        <!-- Main Content -->
        <div class="main-content">
            <div class="container-fluid mt-4">
                <h2 class="welcome-header mb-4">Performance Reports</h2>
                <!-- Filter Form -->
                <div class="card dashboard-card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Filter Reports</h5>
                        <form action="view_reports.php" method="GET" class="row g-3">
                            <div class="col-md-3">
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
                            <div class="col-md-3">
                                <label for="filter_semester" class="form-label">Semester</label>
                                <input type="number" class="form-control" id="filter_semester" name="filter_semester" 
                                       value="<?php echo htmlspecialchars($filter_semester ?? ''); ?>" min="1">
                            </div>
                            <div class="col-md-3">
                                <label for="filter_date_start" class="form-label">Attendance Start Date</label>
                                <input type="date" class="form-control" id="filter_date_start" name="filter_date_start" 
                                       value="<?php echo htmlspecialchars($filter_date_start ?? ''); ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="filter_date_end" class="form-label">Attendance End Date</label>
                                <input type="date" class="form-control" id="filter_date_end" name="filter_date_end" 
                                       value="<?php echo htmlspecialchars($filter_date_end ?? ''); ?>">
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary mt-2">Apply Filters</button>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- Class Performance Chart -->
                <div class="card dashboard-card mb-4 chart-container">
                    <div class="card-body">
                        <h5 class="card-title">Class Performance Overview</h5>
                        <canvas id="performanceChart"></canvas>
                    </div>
                </div>
                <!-- Class Performance Table -->
                <div class="card dashboard-card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Class Performance Details</h5>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Class</th>
                                        <th>Subject</th>
                                        <th>Average Marks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($class_performance as $row): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['class']); ?></td>
                                            <td><?php echo htmlspecialchars($row['subject']); ?></td>
                                            <td><?php echo number_format($row['avg_marks'], 2); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($class_performance)): ?>
                                        <tr><td colspan="3" class="text-center">No data available.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- Student Performance Table -->
                <div class="card dashboard-card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Student Performance</h5>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Student ID</th>
                                        <th>Name</th>
                                        <th>Class</th>
                                        <th>Subject</th>
                                        <th>Marks</th>
                                        <th>Semester</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($student_performance as $row): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['student_id']); ?></td>
                                            <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                                            <td><?php echo htmlspecialchars($row['class']); ?></td>
                                            <td><?php echo htmlspecialchars($row['subject']); ?></td>
                                            <td><?php echo htmlspecialchars($row['marks']); ?></td>
                                            <td><?php echo htmlspecialchars($row['semester']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($student_performance)): ?>
                                        <tr><td colspan="6" class="text-center">No data available.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- Attendance Summary Table -->
                <div class="card dashboard-card">
                    <div class="card-body">
                        <h5 class="card-title">Attendance Summary</h5>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Class</th>
                                        <th>Total Days</th>
                                        <th>Present Days</th>
                                        <th>Attendance Percentage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($attendance_summary as $row): ?>
                                        <?php $percentage = $row['total_days'] > 0 ? round(($row['present_days'] / $row['total_days']) * 100, 2) : 0; ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['class']); ?></td>
                                            <td><?php echo htmlspecialchars($row['total_days']); ?></td>
                                            <td><?php echo htmlspecialchars($row['present_days']); ?></td>
                                            <td>
                                                <div class="progress" style="height: 10px;">
                                                    <div class="progress-bar <?php echo $percentage >= 75 ? 'bg-success' : 'bg-warning'; ?>" 
                                                         role="progressbar" 
                                                         style="width: <?php echo $percentage; ?>%;" 
                                                         aria-valuenow="<?php echo $percentage; ?>" 
                                                         aria-valuemin="0" aria-valuemax="100">
                                                    </div>
                                                </div>
                                                <?php echo $percentage; ?>%
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($attendance_summary)): ?>
                                        <tr><td colspan="4" class="text-center">No data available.</td></tr>
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
    <script>
        const ctx = document.getElementById('performanceChart').getContext('2d');
        const chartData = <?php echo json_encode($chart_data); ?>;
        const subjects = <?php echo json_encode($all_subjects); ?>;
        const datasets = Object.keys(chartData).map((className, index) => ({
            label: className,
            data: subjects.map(subject => chartData[className][subject] || 0),
            backgroundColor: `rgba(${index * 50 % 255}, ${100 + index * 30 % 155}, ${200 - index * 20 % 155}, 0.6)`,
            borderColor: `rgba(${index * 50 % 255}, ${100 + index * 30 % 155}, ${200 - index * 20 % 155}, 1)`,
            borderWidth: 1
        }));
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: subjects,
                datasets: datasets
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top', labels: { font: { family: 'Poppins', size: 14 } } },
                    title: { 
                        display: true, 
                        text: 'Class Performance by Subject', 
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
                            text: 'Average Marks', 
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