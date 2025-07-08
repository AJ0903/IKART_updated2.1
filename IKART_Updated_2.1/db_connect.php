


<?php
// Database connection details
$host = "localhost";
$username = "root";
$password = "";
$database = "ikart"; // Make sure this matches your actual DB name

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// Optional: confirm connection (for testing)
# echo "✅ Connected successfully to the database!";
?>

