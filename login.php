<?php
// Connect to the database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "financetracker";
$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Start session to store login status
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['username']); // assuming 'username' is email
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        echo "<script>alert('Both fields are required'); window.history.back();</script>";
    } else {
        $query = "SELECT * FROM signup WHERE Email = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $row['Password'])) {
                // ✅ Successful login - store all user data in session
                $_SESSION['name'] = $row['Name'];
                $_SESSION['email'] = $row['Email'];
                $_SESSION['phone'] = $row['PhoneNumber'];
                $_SESSION['gender'] = $row['Gender'];
                $_SESSION['password'] = $password; // optional for display

                echo "<script>alert('Login successful'); window.location.href='home.html';</script>";
            } else {
                echo "<script>alert('Incorrect password'); window.history.back();</script>";
            }
        } else {
            echo "<script>alert('Email not found. Please sign up first.'); window.location.href='signup.html';</script>";
        }
    }
    mysqli_close($conn);
}
?>
