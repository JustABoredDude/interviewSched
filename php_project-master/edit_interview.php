<?php
// edit_interview.php
session_start();
require_once 'db.php';
require_once 'functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch interview details
if (isset($_GET['id'])) {
    $interview_id = $_GET['id'];
    $sql = "SELECT i.id, i.interviewer_id, i.applicant_id, i.scheduled_time, i.status,
                   ir.name AS interviewer_name, a.name AS applicant_name
            FROM interviews i
            JOIN interviewers ir ON i.interviewer_id = ir.id
            JOIN applicants a ON i.applicant_id = a.id
            WHERE i.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $interview_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $interview = $result->fetch_assoc();

    if (!$interview) {
        // If no interview is found, redirect to index.php
        header("Location: index.php");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}

// Fetch interviewers and applicants for dropdowns
$interviewers = getInterviewers($conn); // Function from db.php
$applicants = getApplicants($conn); // Function from functions.php
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Interview</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Edit Interview</h1>
        <form action="update_interview.php" method="POST">
            <input type="hidden" name="id" value="<?= $interview['id'] ?>">
            
            <!-- Interviewer Field -->
            <div class="form-group">
                <label for="interviewer">Interviewer</label>
                <select id="interviewer" name="interviewer" required>
                    <option value="" disabled>Select Interviewer</option>
                    <?php foreach ($interviewers as $interviewer): ?>
                        <option value="<?= $interviewer['id'] ?>" <?= $interviewer['id'] == $interview['interviewer_id'] ? 'selected' : '' ?>>
                            <?= $interviewer['name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Applicant Field -->
            <div class="form-group">
                <label for="applicant">Applicant</label>
                <select id="applicant" name="applicant" required>
                    <option value="" disabled>Select Applicant</option>
                    <?php foreach ($applicants as $applicant): ?>
                        <option value="<?= $applicant['id'] ?>" <?= $applicant['id'] == $interview['applicant_id'] ? 'selected' : '' ?>>
                            <?= $applicant['name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Scheduled Time Field -->
            <div class="form-group">
                <label for="scheduled_time">Scheduled Time</label>
                <input type="datetime-local" id="scheduled_time" name="scheduled_time" value="<?= date('Y-m-d\TH:i', strtotime($interview['scheduled_time'])) ?>" required>
            </div>

            <!-- Status Field (Optional)-->
            <!--<div class="form-group">
                <label for="status">Interview Status</label>
                <select id="status" name="status" required>
                    <option value="" disabled>Select Status</option>
                    <option value="upcoming" <?= ($interview['status'] ?? '') == 'upcoming' ? 'selected' : '' ?>>Upcoming</option>
                    <option value="rescheduled" <?= ($interview['status'] ?? '') == 'rescheduled' ? 'selected' : '' ?>>Rescheduled</option>
                    <option value="cancelled" <?= ($interview['status'] ?? '') == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                </select>
            </div>-->

            <!-- Buttons -->
            <button type="submit">Update Interview</button>
            <a href="index.php" class="cancel-button">Cancel</a>
        </form>
    </div>
</body>
</html>
