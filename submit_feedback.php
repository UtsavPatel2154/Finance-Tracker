<?php
$servername = "localhost"; // Change if needed
$username = "root"; // Change if using a different MySQL user
$password = ""; // Change if you set a password
$dbname = "financetracker";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get data from form
$name = $_POST['name'];
$email = $_POST['email'];
$feedback = $_POST['feedback'];

// Prepare and execute SQL statement
$stmt = $conn->prepare("INSERT INTO feedback (name, email, message) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $name, $email, $feedback);

if ($stmt->execute()) {
    echo '<script>alert("Thank you for your feedback!"); window.location.href="home.html";</script>';
} else {
    echo '<script>alert("Error submitting feedback. Please try again."); window.history.back();</script>';
}

// Close connection
$stmt->close();
$conn->close();
?>
