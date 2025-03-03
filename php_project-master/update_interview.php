<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $interview_id = $_POST['id'];
    $interviewer_id = $_POST['interviewer'];
    $applicant_id = $_POST['applicant'];
    $scheduled_time = $_POST['scheduled_time'];

    // Update the interview
    $stmt = $conn->prepare("UPDATE interviews SET interviewer_id = ?, applicant_id = ?, scheduled_time = ? WHERE id = ?");
    $stmt->bind_param("iisi", $interviewer_id, $applicant_id, $scheduled_time, $interview_id);

    if ($stmt->execute()) {
        header("Location: index.php");
        exit();
    } else {
        echo "Error updating interview: " . $stmt->error;
    }

    $stmt->close();
}
?>