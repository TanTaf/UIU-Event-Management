<?php
session_start();
include 'db.php';

if ($_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $event_id = $_GET['id'];

    $stmt = $conn->prepare("UPDATE events SET approval_status = 'rejected' WHERE id = ?");
    $stmt->bind_param("i", $event_id);

    if ($stmt->execute()) {
        echo "<script>alert('Event rejected successfully.'); window.location.href='admin_portal.php';</script>";
    } else {
        echo "<script>alert('Error rejecting event. Please try again later.'); window.location.href='admin_portal.php';</script>";
    }
} else {
    echo "<script>alert('Invalid event ID.'); window.location.href='admin_portal.php';</script>";
}
?>
