<?php
require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $interviewer_name = $_POST['interviewer_name'];
    $interviewer_email = $_POST['interviewer_email'];
    $department_id = intval($_POST['department']); // Ensure it's an integer
    $applicant_name = $_POST['applicant_name'];
    $applicant_email = $_POST['applicant_email'];

    // Check if interviewer exists
    $stmt = $conn->prepare("SELECT id, department_id FROM interviewers WHERE email = ?");
    $stmt->bind_param("s", $interviewer_email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Interviewer exists, check department
        $row = $result->fetch_assoc();
        $interviewer_id = $row['id'];
        $existing_department = $row['department_id'];

        if ($existing_department != $department_id) {
            // Update department only if different
            $stmt = $conn->prepare("UPDATE interviewers SET department_id = ? WHERE id = ?");
            $stmt->bind_param("ii", $department_id, $interviewer_id);
            $stmt->execute();
        }
    } else {
        // Insert new interviewer
        $stmt = $conn->prepare("INSERT INTO interviewers (name, email, department_id) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $interviewer_name, $interviewer_email, $department_id);
        $stmt->execute();
        $interviewer_id = $conn->insert_id;
    }

    // Check if applicant exists
    $stmt = $conn->prepare("SELECT id FROM applicants WHERE email = ?");
    $stmt->bind_param("s", $applicant_email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $applicant_id = $row['id'];
    } else {
        // Insert new applicant
        $stmt = $conn->prepare("INSERT INTO applicants (name, email) VALUES (?, ?)");
        $stmt->bind_param("ss", $applicant_name, $applicant_email);
        $stmt->execute();
        $applicant_id = $conn->insert_id;
    }

    // Redirect to schedule interview page
    header("Location: schedule_interview.php?interviewer_id=" . urlencode($interviewer_id) . "&applicant_id=" . urlencode($applicant_id));
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Interviewer & Applicant</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Add Interviewer & Applicant</h2>
        <form action="add_interview.php" method="POST">
            <div class="form-section">
                <h3>Interviewer</h3>
                <label for="interviewer_name">Interviewer Name</label>
                <input type="text" id="interviewer_name" name="interviewer_name" required>

                <label for="interviewer_email">Interviewer Email</label>
                <input type="email" id="interviewer_email" name="interviewer_email" required>

                <label for="department">Department</label>
                <select id="department" name="department" required>
                    <option value="1">CABA</option>
                    <option value="2">CEIT</option>
                    <option value="3">COED</option>
                    <option value="4">CAS</option>
                </select>
            </div>

            <div class="form-section">
                <h3>Applicant</h3>
                <label for="applicant_name">Applicant Name</label>
                <input type="text" id="applicant_name" name="applicant_name" required>

                <label for="applicant_email">Applicant Email</label>
                <input type="email" id="applicant_email" name="applicant_email" required>
            </div>

            <button type="submit" class="submit-btn">Confirm</button>
            <a href="index.php" class="cancel-btn">Cancel</a>
        </form>
    </div>
</body>
</html>
