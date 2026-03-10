<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "financetracker";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Show checkboxes if toggled
$show = [
    'income' => isset($_POST['toggle_income']) || isset($_POST['clear_income']),
    'expense' => isset($_POST['toggle_expense']) || isset($_POST['clear_expense']),
    'budget' => isset($_POST['toggle_budget']) || isset($_POST['clear_budget']),
    'savings' => isset($_POST['toggle_savings']) || isset($_POST['clear_savings']),
];

// Handle delete of selected rows
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach (['income', 'expense', 'budget', 'savings'] as $type) {
        if (isset($_POST["clear_$type"]) && isset($_POST["{$type}_ids"])) {
            $ids = implode(",", array_map('intval', $_POST["{$type}_ids"]));
            $conn->query("DELETE FROM $type WHERE id IN ($ids)");
        }
    }
}

// Flag to hide summary if cleared
$hide_summary = false;

// Handle "Clear Summary" click (only hide/reset the values)
if (isset($_POST['clear_summary_table'])) {
    $totals['budget'] = 0;
    $totals['expense'] = 0;
    $totals['remaining'] = 0;
    $hide_summary = true;
} else {
    // Calculate Budget vs Expense Summary using single SQL query
    $summary_sql = "
        SELECT
            (SELECT IFNULL(SUM(amount), 0) FROM budget) AS total_budget,
            (SELECT IFNULL(SUM(amount), 0) FROM expense) AS total_expense
    ";
    $summary_result = $conn->query($summary_sql);
    $summary = $summary_result->fetch_assoc();

    $totals['budget'] = $summary['total_budget'];
    $totals['expense'] = $summary['total_expense'];
    $totals['remaining'] = $totals['budget'] - $totals['expense'];
}

// Fetch data for tables
$income = $conn->query("SELECT * FROM income");
$expense = $conn->query("SELECT * FROM expense");
$budget = $conn->query("SELECT * FROM budget");
$savings = $conn->query("SELECT * FROM savings");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Financial Report</title>
    <style>
        body { font-family: Arial; background: #f5f5f5; padding: 20px; }
        h2 { color: #333; }
        table { width: 100%; margin-bottom: 30px; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background-color: #f0f0f0; }
        .clear-btn, .back-btn {
            background: red; color: white;
            padding: 10px 16px;
            border: none; cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        .back-btn:hover, .clear-btn:hover { background-color: rgb(255, 0, 0); }
    </style>
    <script>
        function toggleSelectAll(source, className) {
            let checkboxes = document.querySelectorAll("." + className);
            for (let cb of checkboxes) {
                cb.checked = source.checked;
            }
        }
    </script>
</head>
<body>

<h1>📊 Financial Report</h1>

<?php
// Render each section
function renderSection($title, $data, $type, $showCheckbox) {
    echo "<h2>" . htmlspecialchars($title) . "</h2>";
    echo '<form method="POST">';
    echo '<table><tr>';
    if ($showCheckbox) echo '<th><input type="checkbox" onclick="toggleSelectAll(this, \'' . $type . '_cb\')"></th>';

    if ($type === 'income' || $type === 'expense') {
        echo '<th>Amount</th><th>Type</th><th>Date</th>';
    } elseif ($type === 'budget') {
        echo '<th>Amount</th><th>Category</th><th>Date</th>';
    } else {
        echo '<th>Amount</th><th>Duration</th><th>Date</th>';
    }
    echo '</tr>';

    while ($row = $data->fetch_assoc()) {
        echo '<tr>';
        if ($showCheckbox) {
            echo '<td><input type="checkbox" name="' . $type . '_ids[]" value="' . $row['id'] . '" class="' . $type . '_cb"></td>';
        }
        echo '<td>₹' . $row['amount'] . '</td>';
        if ($type === 'income' || $type === 'expense') {
            echo '<td>' . htmlspecialchars($row['type']) . '</td>';
        } elseif ($type === 'budget') {
            echo '<td>' . htmlspecialchars($row['category']) . '</td>';
        } else {
            echo '<td>' . htmlspecialchars($row['duration']) . '</td>';
        }
        echo '<td>' . $row['created_at'] . '</td>';
        echo '</tr>';
    }

    echo '</table>';
    if ($showCheckbox) {
        echo '<button class="clear-btn" name="clear_' . $type . '">Delete Selected</button>';
    } else {
        echo '<button class="clear-btn" name="toggle_' . $type . '">Clear ' . ucfirst($type) . '</button>';
    }
    echo '</form>';
}
?>

<!-- Render all report sections -->
<?php
renderSection('Income', $income, 'income', $show['income']);
renderSection('Expenses', $expense, 'expense', $show['expense']);
renderSection('Budgets', $budget, 'budget', $show['budget']);
renderSection('Savings Goals', $savings, 'savings', $show['savings']);
?>

<!-- Budget vs Expense Summary -->
<h2>Available Budget</h2>
<form method="POST">
    <table>
        <tr>
            <th>Details</th>
            <th>Amount (₹)</th>
        </tr>
        <tr>
            <td>Total Budget</td>
            <td><?php echo number_format($totals['budget'], 2); ?></td>
        </tr>
        <tr>
            <td>Total Expense</td>
            <td><?php echo number_format($totals['expense'], 2); ?></td>
        </tr>
        <tr>
            <td><strong>Remaining Budget</strong></td>
            <td><strong><?php echo number_format($totals['remaining'], 2); ?></strong></td>
        </tr>
    </table>
    <button class="clear-btn" name="clear_summary_table" onclick="return confirm('Hide the summary values? This won’t delete any data.')">
        Clear Available Budget
    </button>
</form>

<!-- Back Button -->
<br><button class="back-btn" onclick="window.location.href='home.html'">Back</button>

</body>
</html>

<?php $conn->close(); ?>
