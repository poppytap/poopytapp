<?php
// Database connection
$servername = "localhost";
$username = "u518630229_puppycoin";
$password = "#Pakuk2024";
$dbname = "u518630229_puppycoin";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the raw POST data and decode it
$data = json_decode(file_get_contents('php://input'), true);
$userId = $data['userId'];

// Prepare SQL to get user data
$sql = "SELECT balance, daily_tasks FROM users WHERE telegram_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // User exists, fetch their data
    $row = $result->fetch_assoc();
    $response = [
        'balance' => $row['balance'],
        'daily_tasks' => json_decode($row['daily_tasks'])
    ];
} else {
    // New user, initialize their data
    $initial_balance = 0;
    $initial_tasks = json_encode(['Task 1', 'Task 2', 'Task 3']); // Example tasks
    $insert_sql = "INSERT INTO users (telegram_id, balance, daily_tasks) VALUES (?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("sis", $userId, $initial_balance, $initial_tasks);
    $insert_stmt->execute();

    $response = [
        'balance' => $initial_balance,
        'daily_tasks' => json_decode($initial_tasks)
    ];
}

echo json_encode($response);

$stmt->close();
$conn->close();
?>