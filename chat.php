<?php
$db = new mysqli("localhost", "root", "1234", "tictactoe");

// Check connection
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

$query = "SELECT * FROM chat ORDER BY id DESC LIMIT 10";
$result = $db->query($query);

$messages = "";
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $player = $row['player'];
        $message = $row['message'];
        $messages .= "<p><strong>Player $player:</strong> $message</p>";
    }
}

$db->close();

echo $messages;
?>
