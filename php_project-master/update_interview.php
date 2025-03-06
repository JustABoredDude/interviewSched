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
    $status = $_POST['status'];

    // Update the interview and status
    $stmt = $conn->prepare("UPDATE interviews SET interviewer_id = ?, applicant_id = ?, scheduled_time = ?, status = ? WHERE id = ?");
    $stmt->bind_param("iissi", $interviewer_id, $applicant_id, $scheduled_time, $status, $interview_id);

    if ($stmt->execute()) {
        // Redirect based on status
        switch ($status) {
            case 'rescheduled':
                header("Location: rescheduled.php");
                break;
            case 'cancelled':
                header("Location: cancelled.php");
                break;
            default:
                header("Location: index.php");
                break;
        }
        exit();
    } else {
        echo "Error updating interview: " . $stmt->error;
    }

    $stmt->close();
}
?>
