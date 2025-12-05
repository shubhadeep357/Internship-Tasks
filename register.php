<?php
// register.php
include 'db_connect.php'; // Include the connection file we made earlier

$message = "";

// Check if the form was submitted using POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Collect and Sanitize Input
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // 2. Server-Side Validation (Basic)
    if (empty($username) || empty($email) || empty($password)) {
        $message = "All fields are required!";
    } else {
        // 3. Password Hashing (Security Requirement [cite: 163])
        // We never store plain text passwords. We encrypt them.
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // 4. Prepared Statement to Prevent SQL Injection (Security Requirement )
        // We use ? as placeholders instead of putting variables directly in the query.
        // role_id defaults to 2 (User) as defined in your database.
        $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        
        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            // "sss" means we are passing 3 strings (username, email, hashed_password)
            mysqli_stmt_bind_param($stmt, "sss", $username, $email, $hashed_password);

            // Execute the query
            if (mysqli_stmt_execute($stmt)) {
                $message = "Registration Successful! <a href='login.php'>Login here</a>";
            } else {
                $message = "Error: Could not register. Email might already exist.";
            }
            mysqli_stmt_close($stmt);
        } else {
            $message = "Database error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Apex Task 3</title>
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
        background: #2600ffff; /* Nice modern blue */
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
    a { color: #1c009bff; text-decoration: none; }
    a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="container">
    <h2>User Registration</h2>
    
    <?php if(!empty($message)): ?>
        <p class="message"><?php echo $message; ?></p>
    <?php endif; ?>

    <form action="register.php" method="POST">
        <label>Username</label>
        <input type="text" name="username" required>

        <label>Email</label>
        <input type="email" name="email" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <button type="submit">Register</button>
    </form>
    <p style="text-align:center;">Already have an account? <a href="login.php">Login</a></p>
</div>

</body>
</html>