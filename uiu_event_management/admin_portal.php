<?php
session_start();
include 'db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM events WHERE approval_status = 'pending'");
if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}

$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Error executing query: " . $stmt->error);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .admin-container {
            margin-top: 30px;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .table-responsive {
            margin-top: 20px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        .logout-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .logout-btn:hover {
            background-color: #c82333;
        }
        h1 {
            color: #007bff;
            font-weight: bold;
        }
        h2 {
            margin-bottom: 20px;
            color: #343a40;
        }
        .action-links a {
            margin-right: 5px;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #007bff;
            color: white;
            border-radius: 15px 15px 0 0 !important;
        }
        .btn-approve {
            background-color: #28a745;
            border-color: #28a745;
        }
        .btn-approve:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }
        .btn-reject {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        .btn-reject:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }
    </style>
</head>
<body>

    <div class="container admin-container">
        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h1><i class="fas fa-user-shield me-2"></i>Admin Portal</h1>
                <button class="btn logout-btn" onclick="window.location.href='logout.php'">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </button>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2 class="mb-0"><i class="fas fa-clock me-2"></i>Pending Events</h2>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <?php if ($result->num_rows > 0): ?>
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Event Name</th>
                                    <th>Organized By</th>
                                    <th>Time Frame</th>
                                    <th>Event Details</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($event = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($event['event_name']); ?></td>
                                    <td><?php echo htmlspecialchars($event['organized_by']); ?></td>
                                    <td><?php echo htmlspecialchars($event['time_frame']); ?></td>
                                    <td><?php echo htmlspecialchars($event['event_details']); ?></td>
                                    <td class="action-links">
                                        <a href="approve_event.php?id=<?php echo $event['id']; ?>" class="btn btn-approve btn-sm">
                                            <i class="fas fa-check me-1"></i>Approve
                                        </a>
                                        <a href="reject_event.php?id=<?php echo $event['id']; ?>" class="btn btn-reject btn-sm">
                                            <i class="fas fa-times me-1"></i>Reject
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="text-center">No pending events found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>