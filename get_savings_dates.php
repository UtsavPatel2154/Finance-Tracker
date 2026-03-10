<?php
date_default_timezone_set('Asia/Kolkata'); // Or your local timezone

$conn = new mysqli("localhost", "root", "", "financetracker");
if ($conn->connect_error) die("Connection failed");

$sql = "SELECT amount, target_datetime FROM savings";
$result = $conn->query($sql);

$goals = [];
while ($row = $result->fetch_assoc()) {
    $goals[] = $row;
}

header('Content-Type: application/json');
echo json_encode($goals);
?>
