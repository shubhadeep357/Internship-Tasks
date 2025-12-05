<?php
// db_connect.php

// 1. Database Credentials
// Default XAMPP username is 'root' and password is '' (empty)
$servername = "localhost";
$username = "root";
$password = ""; 
$dbname = "apex_user_mngmt"; // The database name we created in Step 1

// 2. Create Connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// 3. Check Connection
if (!$conn) {
    // If connection fails, stop the script and show the error
    die("Connection failed: " . mysqli_connect_error());
}

// Optional: Echo a message for testing (Comment this out after testing!)
//echo "Connected successfully to database: " . $dbname;

?>