<?php
require 'config.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: student_login.php");
    exit;
}

$error = null;
$success = null;

// Fetch student details
$stmt = $conn->prepare("SELECT student_id, first_name, last_name, class FROM students WHERE student_id = ?");
$stmt->bind_param("s", $_SESSION['student_id']);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
if (!$student) {
    $_SESSION['error'] = "Student data not found.";
    header("Location: student_dashboard.php");
    exit;
}

// Handle payment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = filter_input(INPUT_POST, 'student_id', FILTER_SANITIZE_STRING);
    $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);
    $payment_date = filter_input(INPUT_POST, 'payment_date', FILTER_SANITIZE_STRING);
    $fee_type = filter_input(INPUT_POST, 'fee_type', FILTER_SANITIZE_STRING);
    $section = filter_input(INPUT_POST, 'section', FILTER_SANITIZE_STRING);

    if ($student_id !== $_SESSION['student_id']) {
        $error = "Unauthorized student ID.";
    } elseif ($amount <= 0 || empty($payment_date) || !preg_match("/^\d{4}-\d{2}-\d{2}$/", $payment_date)) {
        $error = "Please provide a valid amount and date.";
    } elseif (!in_array($fee_type, ['school_fees', 'bus_fees', 'library_fees', 'exam_fees', 'other'])) {
        $error = "Invalid fee type.";
    } else {
        $stmt = $conn->prepare("INSERT INTO payments (student_id, amount, payment_date, status, fee_type, section) VALUES (?, ?, ?, 'completed', ?, ?)");
        $stmt->bind_param("sdsss", $student_id, $amount, $payment_date, $fee_type, $section);
        if ($stmt->execute()) {
            $success = "Payment recorded successfully.";
            $stmt = $conn->prepare("INSERT INTO logs (user_id, action) VALUES (?, ?)");
            $action = "Student recorded payment of $amount for $fee_type on $payment_date";
            $stmt->bind_param("is", $_SESSION['student_user_id'], $action);
            $stmt->execute();
        } else {
            $error = "Failed to record payment.";
        }
    }
}

// Fetch payment history
$stmt = $conn->prepare("SELECT p.*, s.first_name, s.last_name, s.class 
                        FROM payments p 
                        JOIN students s ON p.student_id = s.student_id 
                        WHERE p.student_id = ? 
                        ORDER BY p.payment_date DESC");
$stmt->bind_param("s", $_SESSION['student_id']);
$stmt->execute();
$payments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fee Payments</title>
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
                    <a class="nav-link" href="student_attendance.php"><i class="bi bi-calendar-check"></i> Attendance</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="student_payments.php"><i class="bi bi-wallet2"></i> Payments</a>
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
                <h2 class="welcome-header mb-4">Fee Payments</h2>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <div class="card dashboard-card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Make a Payment</h5>
                        <form action="student_payments.php" method="POST">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="student_id" class="form-label">Student ID</label>
                                    <select class="form-control" id="student_id" name="student_id" required>
                                        <option value="<?php echo htmlspecialchars($student['student_id']); ?>" selected>
                                            <?php echo htmlspecialchars($student['student_id']); ?>
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="student_name" class="form-label">Student Name</label>
                                    <input type="text" class="form-control" id="student_name" 
                                           value="<?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?>" 
                                           readonly>
                                </div>
                                <div class="col-md-4">
                                    <label for="class" class="form-label">Class</label>
                                    <input type="text" class="form-control" id="class" 
                                           value="<?php echo htmlspecialchars($student['class']); ?>" 
                                           readonly>
                                </div>
                                <div class="col-md-4">
                                    <label for="section" class="form-label">Section (Optional)</label>
                                    <input type="text" class="form-control" id="section" name="section" 
                                           value="<?php echo htmlspecialchars($section ?? ''); ?>">
                                </div>
                                <div class="col-md-4">
                                    <label for="fee_type" class="form-label">Fee Type</label>
                                    <select class="form-control" id="fee_type" name="fee_type" required>
                                        <option value="school_fees">School Fees</option>
                                        <option value="bus_fees">Bus Fees</option>
                                        <option value="library_fees">Library Fees</option>
                                        <option value="exam_fees">Exam Fees</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="amount" class="form-label">Amount</label>
                                    <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="payment_date" class="form-label">Payment Date</label>
                                    <input type="date" class="form-control" id="payment_date" name="payment_date" 
                                           value="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary mt-3">Submit Payment</button>
                        </form>
                    </div>
                </div>
                <div class="card dashboard-card">
                    <div class="card-body">
                        <h5 class="card-title">Payment History</h5>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Student Name</th>
                                        <th>Class</th>
                                        <th>Section</th>
                                        <th>Fee Type</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($payments as $payment): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($payment['first_name'] . ' ' . $payment['last_name']); ?></td>
                                            <td><?php echo htmlspecialchars($payment['class']); ?></td>
                                            <td><?php echo htmlspecialchars($payment['section'] ?? '-'); ?></td>
                                            <td>
                                                <span class="badge bg-info">
                                                    <?php echo ucfirst(str_replace('_', ' ', htmlspecialchars($payment['fee_type']))); ?>
                                                </span>
                                            </td>
                                            <td><?php echo htmlspecialchars(number_format($payment['amount'], 2)); ?></td>
                                            <td><?php echo htmlspecialchars($payment['payment_date']); ?></td>
                                            <td>
                                                <span class="badge <?php echo $payment['status'] === 'completed' ? 'bg-success' : ($payment['status'] === 'pending' ? 'bg-warning' : 'bg-danger'); ?>">
                                                    <?php echo ucfirst(htmlspecialchars($payment['status'])); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($payments)): ?>
                                        <tr><td colspan="7" class="text-center">No payment records found.</td></tr>
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