<?php
require 'config.php';

$results = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = filter_input(INPUT_POST, 'student_id', FILTER_SANITIZE_STRING);
    if (!empty($student_id)) {
        $stmt = $conn->prepare("SELECT r.subject, r.marks, r.semester, s.first_name, s.last_name 
                               FROM results r 
                               JOIN students s ON r.student_id = s.student_id 
                               WHERE r.student_id = ?");
        $stmt->bind_param("s", $student_id);
        $stmt->execute();
        $results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        if (empty($results)) {
            $_SESSION['error'] = "No results found for this Student ID.";
        }
    } else {
        $_SESSION['error'] = "Please enter a valid Student ID.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Results</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container mt-4">
        <h2>View Your Results</h2>
        <form action="student_results.php" method="POST">
            <div class="mb-3">
                <label for="student_id" class="form-label">Student ID</label>
                <input type="text" class="form-control" id="student_id" name="student_id" required>
            </div>
            <button type="submit" class="btn btn-primary">View Results</button>
        </form>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger mt-3"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <?php if (!empty($results)): ?>
            <h3 class="mt-4">Results for <?php echo htmlspecialchars($results[0]['first_name'] . " " . $results[0]['last_name']); ?></h3>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Marks</th>
                        <th>Semester</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $result): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($result['subject']); ?></td>
                            <td><?php echo htmlspecialchars($result['marks']); ?></td>
                            <td><?php echo htmlspecialchars($result['semester']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        <p class="mt-3"><a href="index.html" class="btn btn-secondary">Back to Home</a></p>
    </div>
</body>
</html>