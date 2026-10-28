<?php
session_start();
include 'db.php';


if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
    header("Location: login.php");
    exit;
}


$stmt = $conn->prepare("SELECT * FROM events WHERE user_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();


if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Events - UIU Event Management</title>
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
            max-width: 1000px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: var(--card-bg);
            border-radius: 15px;
            box-shadow: var(--card-shadow);
        }

        .user-header {
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--primary-color);
        }

        .user-header h2 {
            color: var(--primary-color);
            font-weight: bold;
        }

        .table-responsive {
            margin-top: 2rem;
        }

        .table {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
            border: 1px solid #dee2e6;
            border-radius: 0.5rem;
            overflow: hidden;
        }

        .table th {
            background-color: var(--primary-color);
            color: var(--light-text-color);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 1rem;
        }

        .table td {
            padding: 1rem;
            vertical-align: middle;
            border-top: 1px solid #dee2e6;
        }

        .table tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.05);
        }

        .table tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.075);
        }

        .badge {
            font-size: 0.85rem;
            padding: 0.35em 0.65em;
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

        .create-event-link {
            display: inline-block;
            margin-top: 1.5rem;
            padding: 0.75rem 1.5rem;
            background-color: var(--primary-color);
            color: var(--light-text-color);
            text-decoration: none;
            border-radius: 0.25rem;
            transition: background-color 0.3s ease;
        }

        .create-event-link:hover {
            background-color: #2980b9;
            color: var(--light-text-color);
        }

        @media (max-width: 768px) {
            .table-responsive {
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>

<?php include('nav.php'); ?>

<div class="container user-portal-container">
  

    <h3 class="mb-4"><i class="fas fa-calendar-alt me-2"></i>Your Events</h3>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Event Name</th>
                    <th>Organized By</th>
                    <th>Time Frame</th>
                    <th>Event Details</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($event = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($event['event_name']); ?></td>
                    <td><?php echo htmlspecialchars($event['organized_by']); ?></td>
                    <td><?php echo htmlspecialchars($event['time_frame']); ?></td>
                    <td><?php echo htmlspecialchars(substr($event['event_details'], 0, 50)) . '...'; ?></td>
                    <td>
                        <span class="badge <?php echo $event['approval_status'] === 'approved' ? 'badge-approved' : 'badge-pending'; ?>">
                            <?php echo ucfirst(htmlspecialchars($event['approval_status'])); ?>
                        </span>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <div class="text-center">
        <a href="create_event.php" class="create-event-link">
            <i class="fas fa-plus-circle me-2"></i>Create New Event
        </a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>