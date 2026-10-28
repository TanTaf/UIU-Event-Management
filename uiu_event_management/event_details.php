<?php
session_start();
include 'db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    echo "Event not found.";
    exit;
}

$event_id = $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Event not found.";
    exit;
}

$event = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Details - <?php echo htmlspecialchars($event['event_name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --text-color: #3498db;
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

        .event-detail-container {
            max-width: 900px;
            margin: 2rem auto;
            background-color: var(--card-bg);
            border-radius: 15px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
        }

        .event-image-container {
            position: relative;
            height: 400px;
            overflow: hidden;
        }

        .event-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .event-title-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.8), rgba(0,0,0,0));
            color: var(--light-text-color);
            padding: 2rem;
        }

        .event-title {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 0;
            color: var(--light-text-color);
        }

        .event-detail-content {
            padding: 2rem;
        }

        .event-info-item {
            margin-bottom: 1.5rem;
        }

        .event-info-label {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--secondary-color);
            margin-bottom: 0.5rem;
        }

        .event-info-value {
            font-size: 1.1rem;
            color: #000000;
        }

        .event-description {
            font-size: 1.1rem;
            line-height: 1.6;
            color: #000000;
        }

        .badge-status {
            font-size: 1rem;
            padding: 0.5em 1em;
            border-radius: 50px;
            color: var(--light-text-color);
        }

        .badge-approved {
            background-color: #2ecc71;
        }

        .badge-pending {
            background-color: #f39c12;
        }

        .back-btn {
            margin-top: 2rem;
        }

        .btn-back {
            padding: 0.75rem 2rem;
            font-size: 1.1rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: var(--light-text-color);
        }

        .btn-back:hover {
            background-color: #2980b9;
            border-color: #2980b9;
            color: var(--light-text-color);
        }
    </style>
</head>
<body>

<?php include('nav.php'); ?>

<div class="container event-detail-container">
    <div class="event-image-container">
        <img src="<?php echo htmlspecialchars($event['event_image'] ? $event['event_image'] : 'default-image.jpg'); ?>" alt="<?php echo htmlspecialchars($event['event_name']); ?>" class="event-image">
        <div class="event-title-overlay">
            <h1 class="event-title"><?php echo htmlspecialchars($event['event_name']); ?></h1>
        </div>
    </div>

    <div class="event-detail-content">
        <div class="row">
            <div class="col-md-8">
                <div class="event-info-item">
                    <h2 class="event-info-label">Event Details</h2>
                    <p class="event-description"><?php echo nl2br(htmlspecialchars($event['event_details'])); ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="event-info-item">
                    <h3 class="event-info-label">Organized By</h3>
                    <p class="event-info-value"><?php echo htmlspecialchars($event['organized_by']); ?></p>
                </div>
                <div class="event-info-item">
                    <h3 class="event-info-label">Time Frame</h3>
                    <p class="event-info-value"><?php echo htmlspecialchars($event['time_frame']); ?></p>
                </div>
                <div class="event-info-item">
                    <h3 class="event-info-label">Status</h3>
                    <span class="badge badge-status <?php echo $event['approval_status'] === 'approved' ? 'badge-approved' : 'badge-pending'; ?>">
                        <?php echo ucfirst(htmlspecialchars($event['approval_status'])); ?>
                    </span>
                </div>
                <div class="event-info-item">
                    <h3 class="event-info-label">Created On</h3>
                    <p class="event-info-value"><?php echo htmlspecialchars($event['created_at']); ?></p>
                </div>
            </div>
        </div>

        <div class="back-btn text-center">
            <a href="view_events.php" class="btn btn-back">
                <i class="fas fa-arrow-left me-2"></i>Back to Events
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>