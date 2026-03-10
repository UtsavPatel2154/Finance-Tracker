<?php
$conn = new mysqli("localhost", "root", "", "financetracker");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $amount = $_POST['income_amount'];
    $type = $_POST['income_type'];
    if (!empty($amount) && !empty($type)) {
        $stmt = $conn->prepare("INSERT INTO income (amount, type) VALUES (?, ?)");
        $stmt->bind_param("ds", $amount, $type);
        $stmt->execute();
        $stmt->close();
        header("Location: income.php?success=1");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Income Tracking</title>
  <link rel="stylesheet" href="income.css">
</head>
<body>
  <h2>Income Tracking</h2>
  <?php if (isset($_GET['success'])) echo "<p style='color:green;'>Income added!</p>"; ?>
  <form method="POST">
    <label>Enter Income:</label>
    <input type="number" name="income_amount" step="0.01" required><br>
    <label>Income Type:</label>
    <input type="text" name="income_type" required><br>
    <button type="submit">Add Income</button>
  </form>
  <br><button onclick="window.location.href='home.html'">Back</button>
</body>
</html>
