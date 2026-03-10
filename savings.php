<?php
date_default_timezone_set('Asia/Kolkata'); // Or your local timezone

$conn = new mysqli("localhost", "root", "", "financetracker");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $amount = $_POST['savings_amount'];
    $duration = $_POST['savings_duration'];

    if (!empty($amount) && !empty($duration)) {
        // Convert human-readable duration into a date
        $timestamp = strtotime($duration);
        if ($timestamp === false) {
            $error = "Invalid duration format. Try '1 month', 'next Friday', or '2025-12-31'.";
        } else {
          $targetDateTime = date('Y-m-d H:i:s', $timestamp);

          $stmt = $conn->prepare("INSERT INTO savings (amount, duration, target_datetime) VALUES (?, ?, ?)");
          $stmt->bind_param("dss", $amount, $duration, $targetDateTime);
          
            $stmt->execute();
            $stmt->close();

            header("Location: savings.php?success=1");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Savings Goal</title>
  <link rel="stylesheet" href="saving.css">
</head>
<body>
  <h2>Savings Goal</h2>

  <?php if (isset($_GET['success'])) echo "<p style='color:green;'>Goal added successfully!</p>"; ?>
  <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>

  <form method="POST">
    <label>Enter Saving Goal:</label>
    <input type="number" name="savings_amount" step="0.01" required><br>

    <label>Duration:</label>
    <input type="text" name="savings_duration" required><br>

    <button type="submit">Set Goal</button>
  </form>

  <br><button onclick="window.location.href='home.html'">Back</button>

  <!-- JavaScript for local notification on target date -->
  <script>
document.addEventListener('DOMContentLoaded', () => {
  console.log("Page loaded");

  if (Notification.permission !== 'granted' && Notification.permission !== 'denied') {
    Notification.requestPermission().then(p => console.log("Permission:", p));
  }

  fetch('get_savings_dates.php')
    .then(response => response.json())
    .then(data => {
      const now = new Date();
      console.log("Now:", now.toISOString());
      console.log("Goals:", data);

      data.forEach(goal => {
        const goalTime = new Date(goal.target_datetime);
        const delay = goalTime - now;

        console.log(`Goal for ${goal.amount} at ${goalTime.toISOString()} (in ${delay / 1000}s)`);

        if (delay > 0) {
          setTimeout(() => {
            console.log(`Showing notification for goal ${goal.amount}`);
            if (Notification.permission === 'granted') {
              const notification = new Notification("Savings Goal Reminder", {
                body: `Now is your deadline for saving ${goal.amount}! Click to view.`,
              });

              notification.onclick = () => {
                window.open('home.html', '_blank');
              };
            }
          }, delay);
        }
      });
    });
});
</script>
</body>
</html>
