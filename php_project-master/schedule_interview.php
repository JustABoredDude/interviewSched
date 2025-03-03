<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$showSummary = false;
$interviewer = [];
$applicant = [];
$scheduled_time = '';

$interviewer_id = isset($_GET['interviewer_id']) ? intval($_GET['interviewer_id']) : 0;
$applicant_id = isset($_GET['applicant_id']) ? intval($_GET['applicant_id']) : 0;

if ($interviewer_id <= 0 || $applicant_id <= 0) {
    die("Invalid Interviewer or Applicant ID!");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $scheduled_time = $_POST['scheduled_time'] ?? '';

    // Fetch Interviewer Info including Department Name
    $stmt = $conn->prepare("
        SELECT interviewers.name, interviewers.email, departments.name AS department_name
        FROM interviewers
        JOIN departments ON interviewers.department_id = departments.id
        WHERE interviewers.id = ?
    ");
    $stmt->bind_param("i", $interviewer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $interviewer = $result->fetch_assoc();

    if (!$interviewer) {
        die("Error: Interviewer not found!");
    }

    // Fetch Applicant Info
    $stmt = $conn->prepare("SELECT name, email FROM applicants WHERE id = ?");
    $stmt->bind_param("i", $applicant_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $applicant = $result->fetch_assoc();

    if (!$applicant) {
        die("Error: Applicant not found!");
    }

    // Insert the interview schedule
    $stmt = $conn->prepare("INSERT INTO interviews (interviewer_id, applicant_id, scheduled_time) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $interviewer_id, $applicant_id, $scheduled_time);
    $stmt->execute();

    // After scheduling the interview, show the summary
    $showSummary = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule Interview</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Schedule Interview</h2>

        <!-- Form for scheduling time -->
        <form action="schedule_interview.php?interviewer_id=<?= $interviewer_id ?>&applicant_id=<?= $applicant_id ?>" method="POST" <?= $showSummary ? 'style="display: none;"' : '' ?>>
            <label for="scheduled_time">Select Date & Time:</label>
            <input type="datetime-local" name="scheduled_time" required>

            <!-- Back Button for navigating to the previous page -->
            <button type="button" class="submit-btn" onclick="window.history.back()">Back</button>
            
            <!-- Confirm Button for submitting the form -->
            <button type="submit" class="submit-btn">Confirm</button>
        </form>

        <!-- Summary Section, after form submission -->
        <?php if ($showSummary): ?>
            <div class="summary">
                <h3>Confirm Schedule</h3>
                <p><strong>Applicant Name:</strong> <?= htmlspecialchars($applicant['name'] ?? 'N/A') ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($applicant['email'] ?? 'N/A') ?></p>
                <p><strong>Date:</strong> <?= date('Y-m-d', strtotime($scheduled_time)) ?></p>
                <p><strong>Time:</strong> <?= date('h:i A', strtotime($scheduled_time)) ?></p>
                <p><strong>Interviewer:</strong> <?= htmlspecialchars($interviewer['name'] ?? 'N/A') ?></p>
                <p><strong>Department:</strong> <?= htmlspecialchars($interviewer['department_name'] ?? 'N/A') ?></p>
                
                <!-- Back Button to go to previous page -->
                <button onclick="window.history.back()">Back</button>

                <!-- Confirm Button to finalize scheduling -->
                <form action="index.php" method="GET">
                    <button type="submit" class="submit-btn">Confirm Schedule</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
