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

$all_interviews = getInterviews($conn, $selected_department, $status_filter);

// Filter interviews by status manually
$interviews = array_filter($all_interviews, function ($interview) use ($status_filter) {
    return isset($interview['status']) && $interview['status'] === $status_filter;
});
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interview Scheduling System</title>
    <link rel="stylesheet" href="style.css">
    <!-- Vanilla Calendar CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@uvarov.frontend/vanilla-calendar@1.4.5/vanilla-calendar.min.css">
</head>
<body>
    <div class="container">
        <!-- Calendar Container -->
        <div id="calendar" class="calendar-container"></div>

        <header class="nav">
            <h1>Interview Scheduling System</h1>
            <div>
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
            </form>

            <div class="status-filters">
                <a href="index.php?status=scheduled" class="<?= ($status_filter == 'scheduled') ? 'active' : '' ?>">Scheduled</a>
                <a href="index.php?status=cancelled" class="<?= ($status_filter == 'cancelled') ? 'active' : '' ?>">Cancelled</a>
                <a href="index.php?status=rescheduled" class="<?= ($status_filter == 'rescheduled') ? 'active' : '' ?>">Rescheduled</a>
            </div>
        </section>

        <section class="add-schedule">
            <a href="add_interview.php" class="button">+ Add Schedule</a>
        </section>

        <h2><?= ucfirst($status_filter) ?> Interviews</h2>
        
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
                            <a href="edit_interview.php?id=<?= $interview['id'] ?>" class="button edit-btn">Edit</a>

                            <?php if ($status_filter == 'scheduled'): ?>
                                <form method="post" action="cancel_interview.php" class="inline-form" onsubmit="return confirmCancel();">
                                    <input type="hidden" name="id" value="<?= $interview['id'] ?>">
                                    <button type="submit" class="button cancel-btn">Cancel</button>
                                </form>

                                <form method="post" action="reschedule_interview.php" class="inline-form">
                                    <input type="hidden" name="id" value="<?= $interview['id'] ?>">
                                    <button type="submit" class="button reschedule-btn">Reschedule</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>No <?= $status_filter ?> interviews found.</p>
        <?php endif; ?>
    </div>

    <!-- Vanilla Calendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/@uvarov.frontend/vanilla-calendar@1.4.5/vanilla-calendar.min.js"></script>
    <script>
    // Initialize the calendar with navigation enabled
    const calendar = new VanillaCalendar('#calendar', {
        settings: {
            lang: 'en',
            visibility: {
                theme: 'light',
                monthShort: false,
            },
            selection: {
                day: 'single',
            },
            navigation: {
                enabled: true,
                scroll: true,
                buttonPrev: '‹',
                buttonNext: '›',
            },
        },
    });

    calendar.init();

    function confirmCancel() {
        return confirm("Are you sure you want to cancel this interview?");
    }
    </script>
</body>
</html>
