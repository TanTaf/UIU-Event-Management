<?php
session_start();
include 'db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $event_name = $_POST['event_name'];
    $organized_by = $_POST['organized_by'];
    $time_frame = $_POST['time_frame'];
    $event_details = $_POST['event_details'];
    
   
    $check_stmt = $conn->prepare("SELECT COUNT(*) FROM events WHERE event_name = ?");
    $check_stmt->bind_param("s", $event_name);
    $check_stmt->execute();
    $check_stmt->bind_result($count);
    $check_stmt->fetch();
    $check_stmt->close();

    if ($count > 0) {
        $error_message = "An event with this name already exists. Please choose a different name.";
    } else {
        if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] == 0) {
            $image_name = $_FILES['event_image']['name'];
            $image_tmp_name = $_FILES['event_image']['tmp_name'];
            $image_size = $_FILES['event_image']['size'];
            $image_ext = pathinfo($image_name, PATHINFO_EXTENSION);
            
            $allowed_extensions = ['jpg', 'jpeg', 'png'];
            if (!in_array(strtolower($image_ext), $allowed_extensions)) {
                $error_message = "Invalid file type. Only JPG, JPEG, PNG are allowed.";
            } else {
                $image_new_name = uniqid('', true) . "." . $image_ext;
                $image_path = "uploads/images/" . $image_new_name;
                
                if (move_uploaded_file($image_tmp_name, $image_path)) {
                    $stmt = $conn->prepare("INSERT INTO events (event_name, organized_by, time_frame, event_details, approval_status, user_id, event_image) VALUES (?, ?, ?, ?, 'pending', ?, ?)");
                    $stmt->bind_param("ssssss", $event_name, $organized_by, $time_frame, $event_details, $_SESSION['user_id'], $image_path);
                    
                    if ($stmt->execute()) {
                        $success_message = "Event created successfully.";
                    } else {
                        $error_message = "Error creating event. Please try again later.";
                    }
                } else {
                    $error_message = "Error uploading image. Please try again later.";
                }
            }
        } else {
            $error_message = "Please select an image for the event.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Event - UIU Event Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --text-color: #333;
            --bg-color: #f8f9fa;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
        }

        .container {
            max-width: 800px;
        }

        .card {
            border: none;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: var(--primary-color);
            color: white;
            font-weight: bold;
        }

        .form-label {
            font-weight: 600;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: #2980b9;
            border-color: #2980b9;
        }

        .alert {
            border-radius: 0;
        }
    </style>
</head>
<body>

<?php include 'nav.php'; ?>

<div class="container mt-5">
    <div class="card">
        <div class="card-header">
            <h2 class="text-center mb-0">Create Event</h2>
        </div>
        <div class="card-body">
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($success_message)): ?>
                <div class="alert alert-success" role="alert">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="event_name" class="form-label">Event Name</label>
                    <input type="text" class="form-control" id="event_name" name="event_name" required>
                </div>

                <div class="mb-3">
                    <label for="organized_by" class="form-label">Organized By</label>
                    <input type="text" class="form-control" id="organized_by" name="organized_by" required>
                </div>

                <div class="mb-3">
                    <label for="time_frame" class="form-label">Time Frame</label>
                    <input type="text" class="form-control" id="time_frame" name="time_frame" required>
                </div>

                <div class="mb-3">
                    <label for="event_details" class="form-label">Event Details</label>
                    <textarea class="form-control" id="event_details" name="event_details" rows="4" required></textarea>
                </div>

                <div class="mb-3">
                    <label for="event_image" class="form-label">Event Image</label>
                    <input type="file" class="form-control" id="event_image" name="event_image" accept="image/*" required>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg">Create Event</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>