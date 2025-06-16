<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = filter_input(INPUT_POST, 'student_id', FILTER_SANITIZE_STRING);
    $subject = filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_STRING);
    $marks = filter_input(INPUT_POST, 'marks', FILTER_VALIDATE_INT);
    $semester = filter_input(INPUT_POST, 'semester', FILTER_VALIDATE_INT);

    if (empty($student_id) || empty($subject) || $marks === false || $semester === false) {
        $_SESSION['error'] = "Please fill in all fields correctly.";
    } elseif ($marks < 0 || $marks > 100) {
        $_SESSION['error'] = "Marks must be between 0 and 100.";
    } else {
        $stmt = $conn->prepare("INSERT INTO results (student_id, subject, marks, semester) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssii", $student_id, $subject, $marks, $semester);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Result added successfully.";
            $stmt = $conn->prepare("INSERT INTO logs (user_id, action) VALUES (?, ?)");
            $action = "Added result for student: $student_id";
            $stmt->bind_param("is", $_SESSION['user_id'], $action);
            $stmt->execute();
        } else {
            $_SESSION['error'] = "Failed to add result.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enter Results</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">SRMS</a>
            <div class="navbar-nav">
                <a class="nav-link" href="manage_students.php">Manage Students</a>
                <a class="nav-link" href="enter_results.php">Enter Results</a>
                <a class="nav-link" href="view_reports.php">View Reports</a>
                <a class="nav-link" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
        <h2>Enter Student Results</h2>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <form action="enter_results.php" method="POST">
            <div class="mb-3">
                <label for="student_id" class="form-label">Student ID</label>
                <select class="form-control" id="student_id" name="student_id" required>
                    <?php
                    $result = $conn->query("SELECT student_id, first_name, last_name FROM students");
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . htmlspecialchars($row['student_id']) . "'>" . htmlspecialchars($row['student_id'] . " - " . $row['first_name'] . " " . $row['last_name']) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="subject" class="form-label">Subject</label>
                <input type="text" class="form-control" id="subject" name="subject" required>
            </div>
            <div class="mb-3">
                <label for="marks" class="form-label">Marks</label>
                <input type="number" class="form-control" id="marks" name="marks" required min="0" max="100">
            </div>
            <div class="mb-3">
                <label for="semester" class="form-label">Semester</label>
                <input type="number" class="form-control" id="semester" name="semester" required min="1">
            </div>
            <button type="submit" class="btn btn-primary">Submit Result</button>
        </form>
    </div>
</body>
</html>