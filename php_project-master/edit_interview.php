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
    $sql = "SELECT i.id, i.interviewer_id, i.applicant_id, i.scheduled_time, 
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
                <select id="interviewer" name="interviewer" required>
                    <option value="" disabled>Select Interviewer</option>
                    <?php foreach ($interviewers as $interviewer): ?>
                        <option value="<?= $interviewer['id'] ?>" <?= $interviewer['id'] == $interview['interviewer_id'] ? 'selected' : '' ?>>
                            <?= $interviewer['name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label for="interviewer">Interviewer</label>
            </div>

            <!-- Applicant Field -->
            <div class="form-group">
                <select id="applicant" name="applicant" required>
                    <option value="" disabled>Select Applicant</option>
                    <?php foreach ($applicants as $applicant): ?>
                        <option value="<?= $applicant['id'] ?>" <?= $applicant['id'] == $interview['applicant_id'] ? 'selected' : '' ?>>
                            <?= $applicant['name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label for="applicant">Applicant</label>
            </div>

            <!-- Scheduled Time Field -->
            <div class="form-group">
                <input type="datetime-local" id="scheduled_time" name="scheduled_time" value="<?= date('Y-m-d\TH:i', strtotime($interview['scheduled_time'])) ?>" required>
                <label for="scheduled_time">Scheduled Time</label>
            </div>

            <!-- Buttons -->
            <button type="submit">Update Interview</button>
            <a href="index.php" class="cancel-button">Cancel</a>
        </form>
    </div>
</body>
</html>