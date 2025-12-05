<?php
// edit_user.php
session_start();
include 'db_connect.php';

// Check Admin Access
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'];
$message = "";

// 1. FETCH EXISTING DATA
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

// 2. HANDLE UPDATE SUBMISSION
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $role_id = $_POST['role_id'];
    
    // Default to existing image
    $image_path = $user['profile_image']; 

    // 3. IMAGE UPLOAD LOGIC (Requirement: Validation)
    // 3. IMAGE UPLOAD LOGIC
    if (!empty($_FILES['profile_image']['name'])) {
        $target_dir = "uploads/";
        
        // --- FIX STARTS HERE ---
        // Check if folder exists, if not, create it automatically
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        // --- FIX ENDS HERE ---

        $file_name = basename($_FILES["profile_image"]["name"]);
        $target_file = $target_dir . time() . "_" . $file_name; 
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        // Validate File Type
        $allowed_types = array("jpg", "jpeg", "png");
        if (in_array($imageFileType, $allowed_types)) {
            // Validate File Size (Limit to 2MB)
            if ($_FILES["profile_image"]["size"] < 2000000) {
                if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
                    $image_path = $target_file; // Update path for database
                } else {
                    $message = "Error uploading file. Check folder permissions.";
                }
            } else {
                $message = "File is too large (Max 2MB).";
            }
        } else {
            $message = "Invalid file type. Only JPG, JPEG, & PNG allowed.";
        }
    }

    // 4. UPDATE DATABASE
    if (empty($message)) {
        $update_sql = "UPDATE users SET username=?, email=?, role_id=?, profile_image=? WHERE id=?";
        $update_stmt = mysqli_prepare($conn, $update_sql);
        mysqli_stmt_bind_param($update_stmt, "ssisi", $username, $email, $role_id, $image_path, $id);
        
        if (mysqli_stmt_execute($update_stmt)) {
            // Redirect back to dashboard on success
            header("Location: admin_dashboard.php?msg=Updated");
            exit;
        } else {
            $message = "Error updating record: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        background: linear-gradient(135deg, #e0eafc 0%, #cfdef3 100%);
        min-height: 100vh;
    }
    .card {
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        border: none; /* Removes the default grey border */
    }
    .navbar {
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        background: linear-gradient(to right, #2c3e50, #4ca1af) !important; /* Optional: Makes navbar look cooler too */
    }
    </style>
</head>
<body class="bg-light">

<div class="container mt-5" style="max-width: 500px;">
    <div class="card">
        <div class="card-header">
            <h4>Edit User</h4>
        </div>
        <div class="card-body">
            <?php if ($message): ?>
                <div class="alert alert-danger"><?php echo $message; ?></div>
            <?php endif; ?>

            <form action="" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                </div>
                
                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>

                <div class="mb-3">
                    <label>Role</label>
                    <select name="role_id" class="form-control">
                        <option value="1" <?php if($user['role_id'] == 1) echo 'selected'; ?>>Admin</option>
                        <option value="2" <?php if($user['role_id'] == 2) echo 'selected'; ?>>User</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Profile Image</label><br>
                    <img src="<?php echo htmlspecialchars($user['profile_image']); ?>" width="80" class="mb-2"><br>
                    <input type="file" name="profile_image" class="form-control">
                    <small class="text-muted">Allowed: JPG, PNG. Max 2MB.</small>
                </div>

                <button type="submit" class="btn btn-primary">Update User</button>
                <a href="admin_dashboard.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>

</body>
</html>