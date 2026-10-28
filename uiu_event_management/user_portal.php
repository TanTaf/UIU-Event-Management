<?php
session_start();
include 'db.php';

// Check if the user is logged in and has the 'user' role
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
    header("Location: login.php");
    exit;
}

// Fetch user's events
$stmt = $conn->prepare("SELECT * FROM events WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

// Count total events and approved events
$stmt_count = $conn->prepare("SELECT COUNT(*) as total, SUM(CASE WHEN approval_status = 'approved' THEN 1 ELSE 0 END) as approved FROM events WHERE user_id = ?");
$stmt_count->bind_param("i", $_SESSION['user_id']);
$stmt_count->execute();
$counts = $stmt_count->get_result()->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Portal - UIU Event Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --text-color: #333;
            --light-text-color: #f8f9fa;
            --bg-color: #f8f9fa;
            --card-bg: #ffffff;
            --card-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
        }

        .user-portal-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 2rem;
        }

        .welcome-section {
            background-color: var(--primary-color);
            color: var(--light-text-color);
            padding: 2rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }

        .stats-card {
            background-color: var(--card-bg);
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: var(--card-shadow);
        }

        .stats-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--primary-color);
        }

        .action-buttons .btn {
            margin-right: 1rem;
            margin-bottom: 1rem;
        }

        .event-list {
            background-color: var(--card-bg);
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 2rem;
            box-shadow: var(--card-shadow);
        }

        .event-item {
            border-bottom: 1px solid #dee2e6;
            padding: 1rem 0;
        }

        .event-item:last-child {
            border-bottom: none;
        }

        .event-title {
            font-weight: bold;
            color: var(--primary-color);
        }

        .badge-status {
            font-size: 0.8rem;
            padding: 0.3em 0.6em;
            border-radius: 0.25rem;
        }

        .badge-approved {
            background-color: #2ecc71;
            color: var(--light-text-color);
        }

        .badge-pending {
            background-color: #f39c12;
            color: var(--light-text-color);
        }
    </style>
</head>
<body>

<?php include('nav.php'); ?>

<div class="container user-portal-container">
    <div class="welcome-section">
        <h1><i class="fas fa-user-circle me-2"></i>Welcome, <?php echo htmlspecialchars($_SESSION['email']); ?></h1>
        <p>Manage your events and stay updated with the latest information.</p>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="stats-card">
                <h3>Total Events</h3>
                <div class="stats-number"><?php echo $counts['total']; ?></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="stats-card">
                <h3>Approved Events</h3>
                <div class="stats-number"><?php echo $counts['approved']; ?></div>
            </div>
        </div>
    </div>

    <div class="action-buttons mt-4">
        <a href="create_event.php" class="btn btn-primary btn-lg">
            <i class="fas fa-plus-circle me-2"></i>Create New Event
        </a>
        <a href="my_events.php" class="btn btn-secondary btn-lg">
            <i class="fas fa-calendar-alt me-2"></i>View All My Events
        </a>
    </div>

    <div class="event-list">
        <h2 class="mb-4">Your Recent Events</h2>
        <?php while ($event = $result->fetch_assoc()): ?>
            <div class="event-item">
                <h4 class="event-title"><?php echo htmlspecialchars($event['event_name']); ?></h4>
                <p><strong>Organized by:</strong> <?php echo htmlspecialchars($event['organized_by']); ?></p>
                <p><strong>Time Frame:</strong> <?php echo htmlspecialchars($event['time_frame']); ?></p>
                <p>
                    <span class="badge badge-status <?php echo $event['approval_status'] === 'approved' ? 'badge-approved' : 'badge-pending'; ?>">
                        <?php echo ucfirst(htmlspecialchars($event['approval_status'])); ?>
                    </span>
                </p>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

