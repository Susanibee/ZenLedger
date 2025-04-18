<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (isset($_SESSION["username"])) {
?>
<!DOCTYPE html>
<html lang="">
    <head>
        <meta charset="utf-8">
        <link href="style/nonregisterstyle.css" rel="stylesheet" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>ZenLedger - Balance Sheet</title>
        <style>
            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 40px;
            }

            th, td {
                padding: 10px;
                text-align: left;
                border-bottom: 1px solid #ddd;
            }

            .dropdown-form {
                max-width: 400px;
                margin: 20px auto;
                text-align: center;
            }

            .dropdown-form select {
                padding: 10px;
                font-size: 1em;
            }

            .dropdown-form button {
                padding: 10px 20px;
                font-size: 1em;
                margin-left: 10px;
            }
        </style>
    </head>
    <body>
        <main>
        <?php include('snippets/logged-in-top-bar.php'); ?>
        <h1>Balance Sheet</h1>
        <hr>

<?php
    $conn = pg_connect("postgresql://zenteamrole:${{ secrets.pgpass }}@ep-raspy-smoke-a5pyv0mk-pooler.us-east-2.aws.neon.tech/zenledgerdb?sslmode=require");

    if (!$conn) {
        die("<p>Connection failed: " . pg_last_error() . "</p>");
    }

    $selected_account = $_GET['account_name'] ?? '';


    $account_names_query = "SELECT DISTINCT account_name FROM chart_of_accounts ORDER BY account_name";
    $account_names_result = pg_query($conn, $account_names_query);

    echo "<form method='GET' class='dropdown-form'>";
    echo "<label for='account_name'>Select Account Name: </label>";
    echo "<select name='account_name' id='account_name'>";
    echo "<option value=''>-- All Accounts --</option>";
    while ($row = pg_fetch_assoc($account_names_result)) {
        $account_name = htmlspecialchars($row['account_name']);
        $selected = ($account_name === $selected_account) ? "selected" : "";
        echo "<option value='$account_name' $selected>$account_name</option>";
    }
    echo "</select>";
    echo "<button type='submit'>Filter</button>";
    echo "</form>";


$params = [];
$query = "SELECT account_name, account_category, account_subcategory, debit, credit FROM chart_of_accounts";

if (!empty($selected_account)) {
    $query .= " WHERE account_name = $1";
    $params[] = $selected_account;
}

$query .= " ORDER BY account_category, account_subcategory";


$result = pg_query_params($conn, $query, $params);

if (!$result) {
    die("<p>Query failed: " . pg_last_error() . "</p>");
}

$grouped_accounts = [];

while ($row = pg_fetch_assoc($result)) {
    $category = strtolower($row['account_category']);
    $subcategory = $row['account_subcategory'] ?? 'Uncategorized';
    $amount = $row['debit'] - $row['credit'];

    if ($category === 'liabilities' || $category === 'owners equity') {
        $amount = -$amount;
    }

    if (!isset($grouped_accounts[$category][$subcategory])) {
        $grouped_accounts[$category][$subcategory] = 0;
    }

    $grouped_accounts[$category][$subcategory] += $amount;
}
    
function print_section($title, $subcategories) {
    $total = 0;
    echo "<tr><td class='account-title' colspan='2'><strong>$title</strong></td></tr>";
    foreach ($subcategories as $sub => $amount) {
        echo "<tr>
                <td>" . htmlspecialchars($sub) . "</td>
                <td>" . number_format($amount, 2) . "</td>
              </tr>";
        $total += $amount;
    }
    echo "<tr><th>Total $title</th><th>" . number_format($total, 2) . "</th></tr>";
}





echo "<div class='print-background'>";

echo "<div style='text-align: center; margin-bottom: 15px;'>";
echo "<p>" . ($selected_account ? htmlspecialchars($selected_account) : "All Accounts") . "</p>";
echo "<p>Balance Sheet</p>";
echo "<p>" . date("F j, Y") . "</p>";
echo "</div>";

echo "<table>";
echo "<tr><th>Subcategory</th><th>Amount</th></tr>";

print_section("Assets", $grouped_accounts['assets'] ?? []);
print_section("Liabilities", $grouped_accounts['liabilities'] ?? []);
print_section("Owners Equity", $grouped_accounts['owners equity'] ?? []);

echo "</table>";
echo "</div>";
?>
        <?php include('snippets/print.php'); ?>
        </main>
    </body>
</html>
<?php
} else {
    header("Location: login.php");
    exit();
}
?>