<?php
$servername = "localhost";
$username = "root";
$password = "1234";
$dbname = "tictactoe";


$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the chat table needs truncation
$query = "SELECT COUNT(*) as count FROM chat";
$result = $conn->query($query);
$row = $result->fetch_assoc();
$count = $row["count"];

if ($count > 0) {
    // Truncate the chat table
    $truncateQuery = "TRUNCATE TABLE chat";
    if ($conn->query($truncateQuery) === TRUE) {
        echo "Chat table truncated successfully.";
    } else {
        echo "Error truncating chat table: " . $conn->error;
    }
} else {
    echo "Chat table is already empty.";
}
// Close the database connection
$conn->close();
?>
