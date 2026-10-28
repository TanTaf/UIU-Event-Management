<?php
session_start();
include 'db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
    header("Location: login.php");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM events WHERE approval_status = 'approved'");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Approved Events - UIU Event Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --text-color: #333;
            --bg-color: #f8f9fa;
            --card-bg: #ffffff;
            --card-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
        }

        .events-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 2rem;
        }

        .section-title {
            color: var(--secondary-color);
            font-weight: 700;
            margin-bottom: 2rem;
            text-align: center;
            position: relative;
        }

        .section-title::after {
            content: '';
            display: block;
            width: 50px;
            height: 3px;
            background-color: var(--primary-color);
            margin: 0.5rem auto;
        }

        .event-card {
            background-color: var(--card-bg);
            border-radius: 10px;
            box-shadow: var(--card-shadow);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
            height: 100%;
        }

        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .event-image {
            height: 200px;
            object-fit: cover;
        }

        .event-card-body {
            padding: 1.5rem;
        }

        .event-card-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 0.75rem;
        }

        .event-info {
            font-size: 0.9rem;
            color: #000000;
            margin-bottom: 0.5rem;
        }

        .event-details {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 1rem;
        }

        .event-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 1rem;
            border-top: 1px solid #eee;
        }

        .btn-view-details {
            color: var(--primary-color);
            font-weight: 600;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .btn-view-details:hover {
            color: #2980b9;
        }

        .badge-approved {
            background-color: #2ecc71;
            color: white;
            font-weight: 500;
            padding: 0.35em 0.65em;
            border-radius: 0.25rem;
        }
    </style>
</head>
<body>

<?php include('nav.php'); ?>

<div class="container events-container">
    <h2 class="section-title">Events</h2>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php while ($event = $result->fetch_assoc()): ?>
            <div class="col">
                <div class="event-card">
                    <img src="<?php echo $event['event_image'] ? $event['event_image'] : 'default-image.jpg'; ?>" alt="<?php echo htmlspecialchars($event['event_name']); ?>" class="card-img-top event-image">
                    <div class="event-card-body">
                        <h3 class="event-card-title"><?php echo htmlspecialchars($event['event_name']); ?></h3>
                        <p class="event-info"><i class="fas fa-user-tie me-2"></i><?php echo htmlspecialchars($event['organized_by']); ?></p>
                        <p class="event-info"><i class="fas fa-clock me-2"></i><?php echo htmlspecialchars($event['time_frame']); ?></p>
                        <p class="event-details"><?php echo htmlspecialchars(substr($event['event_details'], 0, 100)) . '...'; ?></p>
                        <div class="event-footer">
                            <a href="event_details.php?id=<?php echo $event['id']; ?>" class="btn-view-details">
                                <i class="fas fa-info-circle me-1"></i> View Details
                            </a>
                            <span class="badge-approved">Approved</span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>