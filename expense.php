<?php
$conn = new mysqli("localhost", "root", "", "financetracker");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $amount = $_POST['expense_amount'];
    $type = $_POST['expense_type'];
    if (!empty($amount) && !empty($type)) {
        $stmt = $conn->prepare("INSERT INTO expense (amount, type) VALUES (?, ?)");
        $stmt->bind_param("ds", $amount, $type);
        $stmt->execute();
        $stmt->close();
        header("Location: expense.php?success=1");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Expense Tracking</title>
  <link rel="stylesheet" href="expense.css">
</head>
<body>
  <h2>Expense Tracking</h2>
  <?php if (isset($_GET['success'])) echo "<p style='color:green;'>Expense added!</p>"; ?>
  <form method="POST">
    <label>Enter Expense:</label>
    <input type="number" name="expense_amount" step="0.01" required><br>
    <label>Expense Type:</label>
    <input type="text" name="expense_type" required><br>
    <button type="submit">Add Expense</button>
  </form>
  <br><button onclick="window.location.href='home.html'">Back</button>
</body>
</html>
