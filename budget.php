<?php
$conn = new mysqli("localhost", "root", "", "financetracker");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $amount = $_POST['budget_amount'];
    $category = $_POST['budget_category'];
    if (!empty($amount) && !empty($category)) {
        $stmt = $conn->prepare("INSERT INTO budget (amount, category) VALUES (?, ?)");
        $stmt->bind_param("ds", $amount, $category);
        $stmt->execute();
        $stmt->close();
        header("Location: budget.php?success=1");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Budget Management</title>
  <link rel="stylesheet" href="budgetmanagement.css">
</head>
<body>
  <h2>Budget Management</h2>
  <?php if (isset($_GET['success'])) echo "<p style='color:green;'>Budget set!</p>"; ?>
  <form method="POST">
    <label>Enter Budget:</label>
    <input type="number" name="budget_amount" step="0.01" required><br>
    <label>Category:</label>
    <input type="text" name="budget_category" required><br>
    <button type="submit">Set Budget</button>
  </form>
  <br><button onclick="window.location.href='home.html'">Back</button>
</body>
</html>
