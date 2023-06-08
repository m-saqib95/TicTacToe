<?php
// Create a database connection
$db = new mysqli("localhost", "root", "1234", "tictactoe");

// Check connection
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Save chat message
function saveChatMessage($db, $player, $message) {
    $player = $db->real_escape_string($player);
    $message = $db->real_escape_string($message);
    $query = "INSERT INTO chat (player, message) VALUES ('$player', '$message')";
    $result = $db->query($query);

    if (!$result) {
        echo "Error: " . $db->error;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["message"]) && isset($_POST["player"])) {
        $message = $_POST["message"];
        $player = $_POST["player"];
        saveChatMessage($db, $player, $message);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tic Tac Toe Game</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        .square {
            width: 100px;
            height: 100px;
            border: 1px solid black;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 48px;
        }
    </style>
</head>
<body>
    <br>
    <div class="container">
        <h1 class="text-center">Tic Tac Toe Game</h1>
        <div class="row">
            <div class="col-md-6">
               
            </div>
            <div class="col-md-6">
            <h3>Player 1 (X)</h3>
                <div id="player1" class="mb-3"></div>

                <h3>Player 2 (O)</h3>
                <div id="player2" class="mb-3"></div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <h4>Chat</h4>
                <div id="chat" class="mb-3"></div>
                <input type="text" id="message" placeholder="Enter your message">
                <button onclick="sendMessage()">Send</button>
            </div>
            <div class="col-md-6">
                <h4>Game Board</h4>
                <div id="game">
                    <div class="row">
                        <div class="square" id="00" onclick="makeMove(0, 0)"></div>
                        <div class="square" id="01" onclick="makeMove(0, 1)"></div>
                        <div class="square" id="02" onclick="makeMove(0, 2)"></div>
                    </div>
                    <div class="row">
                        <div class="square" id="10" onclick="makeMove(1, 0)"></div>
                        <div class="square" id="11" onclick="makeMove(1, 1)"></div>
                        <div class="square" id="12" onclick="makeMove(1, 2)"></div>
                    </div>
                    <div class="row">
                        <div class="square" id="20" onclick="makeMove(2, 0)"></div>
                        <div class="square" id="21" onclick="makeMove(2, 1)"></div>
                        <div class="square" id="22" onclick="makeMove(2, 2)"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script>
        var currentPlayer = "X";
        var gameOver = false;

        // Function to make a move
        function makeMove(row, col) {
            if (!gameOver) {
                var square = $("#" + row + col);
                if (square.html() === "") {
                    square.html(currentPlayer);
                    checkWin();
                    togglePlayer();
                }
            }
        }

        // Function to toggle between players
        function togglePlayer() {
            currentPlayer = currentPlayer === "X" ? "O" : "X";
        }

        // Function to check for a win
        function checkWin() {
            var rows = [
                [$("#00"), $("#01"), $("#02")],
                [$("#10"), $("#11"), $("#12")],
                [$("#20"), $("#21"), $("#22")]
            ];

            var cols = [
                [$("#00"), $("#10"), $("#20")],
                [$("#01"), $("#11"), $("#21")],
                [$("#02"), $("#12"), $("#22")]
            ];

            var diagonals = [
                [$("#00"), $("#11"), $("#22")],
                [$("#02"), $("#11"), $("#20")]
            ];

            var winConditions = [...rows, ...cols, ...diagonals];

            winConditions.forEach(function (condition) {
                var cells = condition.map(function (cell) {
                    return cell.html();
                });

                if (cells[0] === currentPlayer && cells[1] === currentPlayer && cells[2] === currentPlayer) {
                    gameOver = true;
                    alert("Player " + currentPlayer + " wins!");
                    location.reload();
                    clearchat();
                }
            });
        }

        // Function to send chat message
        function sendMessage() {
            var message = $("#message").val();
            if (message !== "") {
                $.ajax({
                    url: "index.php",
                    method: "POST",
                    data: {
                        message: message,
                        player: currentPlayer
                    },
                    success: function () {
                        $("#message").val("");
                        fetchChat();
                    }
                });
            }
        }

        // Function to fetch chat messages
        function fetchChat() {
            $.ajax({
                url: "chat.php",
                method: "GET",
                success: function (data) {
                    $("#chat").html(data);
                    $("#chat").scrollTop($("#chat")[0].scrollHeight);
                }
            });
        }


        // truncate the chat 

        function clearchat(){
            $.ajax({
                url: "truncate.php",
                type: "POST",
                success: function(response) {
                    // Handle the response if needed
                    console.log(response);
                },
                error: function(xhr, status, error) {
                    // Handle the error if necessary
                    console.log("Error: " + error);
                }
            });
        }

        // Fetch chat messages on page load
        $(document).ready(function () {
            fetchChat();
            clearchat();
        });

        // Fetch chat messages every 2 seconds
        setInterval(fetchChat, 1000);
    </script>
</body>
</html>
