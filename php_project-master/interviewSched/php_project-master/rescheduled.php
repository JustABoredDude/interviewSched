<?php
session_start();
require_once 'db.php';
require_once 'functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$selected_department = $_GET['department'] ?? null;
$interviews = getInterviews($conn, $selected_department, 'rescheduled'); // Only rescheduled interviews
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rescheduled Interviews</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header class="nav">
            <h1>Interview Scheduling System</h1>
            <div>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </header>

        <section class="filters">
            <form method="GET" action="rescheduled.php" class="department-filter">
                <label for="department">Filter by Department:</label>
                <select name="department" id="department" onchange="this.form.submit()">
                    <option value="">All</option>
                    <option value="CABA" <?= ($selected_department == 'CABA') ? 'selected' : '' ?>>CABA</option>
                    <option value="CEIT" <?= ($selected_department == 'CEIT') ? 'selected' : '' ?>>CEIT</option>
                    <option value="COED" <?= ($selected_department == 'COED') ? 'selected' : '' ?>>COED</option>
                    <option value="CAS" <?= ($selected_department == 'CAS') ? 'selected' : '' ?>>CAS</option>
                </select>
            </form>

            <div class="status-filters">
                <a href="index.php">Scheduled</a>
                <a href="cancelled.php">Cancelled</a>
                <a href="rescheduled.php" class="active">Rescheduled</a>
            </div>
        </section>

        <h2>Rescheduled Interviews</h2>
        <?php if (!empty($interviews)): ?>
            <div class="interviews-list">
                <?php foreach ($interviews as $interview): ?>
                    <div class="card">
                        <div class="card-header">
                            <span class="day"><?= date('l', strtotime($interview['scheduled_time'])) ?></span>
                            <span class="time"><?= date('h:i A', strtotime($interview['scheduled_time'])) ?> - 
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
                            <form method="post" action="cancel_interview.php" onsubmit="return confirmAction('Are you sure you want to cancel this interview?');">
                                <input type="hidden" name="id" value="<?= $interview['id'] ?>">
                                <button type="submit" class="button cancel-btn">Cancel</button>
                            </form>
                            <form method="post" action="reschedule_interview.php" onsubmit="return confirmAction('Are you sure you want to reschedule this interview?');">
                                <input type="hidden" name="id" value="<?= $interview['id'] ?>">
                                <button type="submit" class="button reschedule-btn">Reschedule</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>No rescheduled interviews found.</p>
        <?php endif; ?>
    </div>

    <script>
    function confirmAction(message) {
        return confirm(message);
    }
    </script>
</body>
</html>