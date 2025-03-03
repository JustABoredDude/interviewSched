<?php
require_once 'db.php';

function getApplicants($conn) {
    $sql = "SELECT * FROM applicants";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getInterviews($conn, $department = null, $status) {
    $sql = "SELECT 
                interviews.id, 
                applicants.name AS applicant, 
                applicants.email AS applicant_email, 
                interviewers.name AS interviewer, 
                departments.name AS interviewer_department, 
                interviews.scheduled_time,
                interviews.status 
            FROM interviews
            JOIN applicants ON interviews.applicant_id = applicants.id
            JOIN interviewers ON interviews.interviewer_id = interviewers.id
            JOIN departments ON interviewers.department_id = departments.id
            WHERE interviews.status = ?"; // ✅ Filters by status

    if ($department) {
        $sql .= " AND departments.name = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $status, $department);
    } else {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $status);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}






?>