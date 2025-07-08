<?php
// Include your existing database connection file
include 'db_connect.php';  // Make sure this file is in the same folder

// Simple query to check if any user exists
$sql = "SELECT * FROM users LIMIT 1";
$result = $conn->query($sql);

// Output the result
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "✅ Database is connected! First user: " . htmlspecialchars($row['name']);
} else {
    echo "⚠️ Database is connected, but no users found.";
}
?>
