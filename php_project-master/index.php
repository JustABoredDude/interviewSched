<?php
session_start();
require_once 'db.php';
require_once 'functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$selected_department = $_GET['department'] ?? null;
$status_filter = $_GET['status'] ?? 'scheduled'; // Default to scheduled
$sort_order = $_GET['sort'] ?? 'asc'; // Default to ascending order

$all_interviews = getInterviews($conn, $selected_department, $status_filter);

// Filter interviews by status manually
$interviews = array_filter($all_interviews, function ($interview) use ($status_filter) {
    return isset($interview['status']) && $interview['status'] === $status_filter;
});

// Sort interviews by department
usort($interviews, function ($a, $b) use ($sort_order) {
    return $sort_order === 'asc'
        ? strcmp($a['interviewer_department'], $b['interviewer_department'])
        : strcmp($b['interviewer_department'], $a['interviewer_department']);
});
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interview Scheduling System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header class="nav">
            <h1>Interview Scheduling System</h1>
            <div>
            <a href="add_interview.php" class="logout-btn">Create Interview</a>
            <a href="add_interview.php" class="logout-btn">Users</a>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </header>

        <section class="filters">
            <form method="GET" action="index.php" class="department-filter">
                <label for="department">Filter by Department:</label>
                <select name="department" id="department" onchange="this.form.submit()">
                    <option value="">All</option>
                    <option value="CABA" <?= ($selected_department == 'CABA') ? 'selected' : '' ?>>CABA</option>
                    <option value="CEIT" <?= ($selected_department == 'CEIT') ? 'selected' : '' ?>>CEIT</option>
                    <option value="COED" <?= ($selected_department == 'COED') ? 'selected' : '' ?>>COED</option>
                    <option value="CAS" <?= ($selected_department == 'CAS') ? 'selected' : '' ?>>CAS</option>
                </select>

                <label for="sort">Sort by Department:</label>
                <select name="sort" id="sort" onchange="this.form.submit()">
                    <option value="asc" <?= ($sort_order == 'asc') ? 'selected' : '' ?>>Ascending</option>
                    <option value="desc" <?= ($sort_order == 'desc') ? 'selected' : '' ?>>Descending</option>
                </select>
            </form>

            
            <div class="status-filters">
                <select name="status" id="status" onchange="window.location.href='index.php?status=' + this.value">
                    <option value="scheduled" <?= ($status_filter == 'scheduled') ? 'selected' : '' ?>>Scheduled</option>
                    <option value="cancelled" <?= ($status_filter == 'cancelled') ? 'selected' : '' ?>>Cancelled</option>
                    <option value="rescheduled" <?= ($status_filter == 'rescheduled') ? 'selected' : '' ?>>Rescheduled</option>
                </select>
            </div>
        </section>

        <h2><?= ucfirst($status_filter) ?> Interviews</h2>
        
        <?php if (!empty($interviews)): ?>
            <div class="interviews-list">
                <?php foreach ($interviews as $interview): ?>
                    <div class="card">
                        <div class="card-header">
                        <span class="date"><?= date('F j, Y', strtotime($interview['scheduled_time'])) ?></span>
                        <span class="day"><?= date('l', strtotime($interview['scheduled_time'])) ?></span>
                        <span class="time">
                    <?= date('h:i A', strtotime($interview['scheduled_time'])) ?> -
                    <?= date('h:i A', strtotime($interview['scheduled_time'] . ' +30 minutes')) ?>
                </span>
            </div>

                        <div class="card-body">
                            <p><strong>Applicant:</strong> <?= htmlspecialchars($interview['applicant'] ?? 'N/A') ?></p>
                            <p><strong>Email:</strong> <?= htmlspecialchars($interview['applicant_email'] ?? 'N/A') ?></p>
                            <p><strong>Interviewer:</strong> <?= htmlspecialchars($interview['interviewer'] ?? 'N/A') ?></p>
                            <p><strong>Department:</strong> <?= htmlspecialchars($interview['interviewer_department'] ?? 'N/A') ?></p>
                        </div>
                        <div class="card-footer">
                            <a href="edit_interview.php?id=<?= $interview['id'] ?>" class="button edit-btn">Edit</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <a href="add_schedule_interview.php" class="logout-btn">Add Schedule</a>
        <?php else: ?>
            <p>No <?= $status_filter ?> interviews found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
