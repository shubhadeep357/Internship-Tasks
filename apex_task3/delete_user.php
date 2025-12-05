<?php
// delete_user.php
session_start();
include 'db_connect.php';

// Check Admin Access
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Prevent Admin from deleting themselves
    if ($id == $_SESSION['user_id']) {
        echo "<script>alert('You cannot delete yourself!'); window.location='admin_dashboard.php';</script>";
        exit;
    }

    // Prepare Delete Statement
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        
        if (mysqli_stmt_execute($stmt)) {
            // Success
            header("Location: admin_dashboard.php?msg=UserDeleted");
        } else {
            echo "Error deleting record.";
        }
        mysqli_stmt_close($stmt);
    }
} else {
    header("Location: admin_dashboard.php");
}
?>