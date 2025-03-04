<?php
// db.php - Database Connection File
$servername = "localhost";
$username = "root";
$password = ""; // Ensure this matches your database password
$dbname = "interview_scheduling"; // Ensure this matches your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all departments for dropdown selection
function getDepartments($conn) {
    $departments = [];
    $result = $conn->query("SELECT id, name FROM departments");
    while ($row = $result->fetch_assoc()) {
        $departments[] = $row;
    }
    return $departments;
}

// Fetch interviewers with department names
function getInterviewers($conn) {
    $sql = "SELECT interviewers.id, interviewers.name, interviewers.email, departments.name AS department_name 
            FROM interviewers 
            LEFT JOIN departments ON interviewers.department_id = departments.id";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}
?>