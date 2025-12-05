<?php
// login.php
session_start(); // 1. Start the Session immediately to store user data
include 'db_connect.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // 2. Prepare SQL to find user by email
    $sql = "SELECT id, username, password, role_id FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        // 3. Check if user exists
        if ($row = mysqli_fetch_assoc($result)) {
            // 4. Verify the Password
            // password_verify() checks if the plain text matches the hash
            if (password_verify($password, $row['password'])) {
                
                // 5. Set Session Variables
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['role_id'] = $row['role_id'];

                // 6. Role-Based Redirection
                // If Role ID is 1 (Admin), go to Admin Panel. Otherwise, go to User Dashboard.
                if ($row['role_id'] == 1) {
                    header("Location: admin_dashboard.php");
                } else {
                    header("Location: user_dashboard.php");
                }
                exit; // Stop script execution after redirect

            } else {
                $message = "Invalid password.";
            }
        } else {
            $message = "No account found with that email.";
        }
        mysqli_stmt_close($stmt);
    } else {
        $message = "Database error.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Apex Task 3</title>
    <style>
    body {
        background: linear-gradient(135deg, #046e40ff );
        min-height: 100vh;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        display: flex;
        align-items: center; /* Centers the box vertically */
        justify-content: center; /* Centers the box horizontally */
        margin: 0;
    }
    .container {
        width: 100%;
        max-width: 400px;
        background: #b6c737ff;
        padding: 40px;
        border-radius: 15px; /* Rounded corners */
        box-shadow: 0 10px 25px rgba(0,0,0,0.1); /* Soft shadow */
    }
    h2 { text-align: center; color: #000000ff; margin-bottom: 20px; }
    input { 
        width: 100%; 
        padding: 12px; 
        margin: 10px 0; 
        border: 1px solid #ddd; 
        border-radius: 5px; 
        box-sizing: border-box; 
    }
    button { 
        width: 100%; 
        padding: 12px; 
        background: #003571ff; /* Nice modern blue */
        color: white; 
        border: none; 
        border-radius: 5px; 
        font-size: 16px; 
        cursor: pointer; 
        transition: background 0.3s;
    }
    button:hover { background: #357abd; }
    .message { color: red; text-align: center; margin-bottom: 10px; }
    p { text-align: center; margin-top: 15px; }
    a { color: #fb0000ff; text-decoration: none; }
    a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="container">
    <h2>Login</h2>
    
    <?php if(!empty($message)): ?>
        <p class="message"><?php echo $message; ?></p>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <label>Email</label>
        <input type="email" name="email" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <button type="submit">Login</button>
    </form>
    <p style="text-align:center;">Don't have an account? <a href="register.php">Register</a></p>
</div>

</body>
</html>