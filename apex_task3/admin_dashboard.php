<?php
// admin_dashboard.php
session_start()
include 'db_connect.php';

// 1. Access Control (Security)
// Check if user is logged in AND is an Admin (role_id = 1)
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: login.php");
    exit;
}

// 2. Fetch All Users (Read Operation)
// We join with the roles table to show "Admin/User" instead of just "1/2"
$sql = "SELECT users.id, users.username, users.email, users.profile_image, roles.role_name 
        FROM users 
        JOIN roles ON users.role_id = roles.id";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
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

<nav class="navbar navbar-dark bg-dark p-3">
    <a class="navbar-brand" href="#">Admin Panel</a>
    <div>
        <span class="text-white me-3">Welcome, <?php echo $_SESSION['username']; ?></span>
        <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
    </div>
</nav>

<div class="container mt-5">
    <h2>User Management</h2>
    <a href="register.php" class="btn btn-success mb-3">Add New User</a>
    
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Photo</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td>
                            <img src="<?php echo htmlspecialchars($row['profile_image']); ?>" 
                                 width="40" height="40" class="rounded-circle" alt="User">
                        </td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td>
                            <span class="badge bg-<?php echo ($row['role_name'] == 'Admin') ? 'primary' : 'secondary'; ?>">
                                <?php echo $row['role_name']; ?>
                            </span>
                        </td>
                        <td>
                            <a href="edit_user.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                            
                            <a href="delete_user.php?id=<?php echo $row['id']; ?>" 
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Are you sure you want to delete this user?');">
                                Delete
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>