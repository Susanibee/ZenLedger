<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

if (empty($_SESSION['selected_customer'])) {
    header("Location: index.php");
    exit();
}
include("snippets/cosmic-message.php");
?>


<!DOCTYPE html>
<html lang="">
<head>
    <meta charset="utf-8">
    <link href="style/nonregisterstyle.css" rel="stylesheet" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ZenLedger - Income Statement</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            padding: 8px 12px;
            border: none;
            text-align: left;
        }
    </style>
</head>
<body>
    <main>
    <?php include('snippets/logged-in-top-bar.php'); ?>
    <div class="cosmic-container">
    <h1>Income Statement</h1>
    <p class="cosmic-message"><?php echo $cosmic_message; ?></p>
</div>
    <hr>

    <div class="helper">
                  <img src="images/zenledger logo.png" class="background-logo" />
              </div>

    <?php
    $conn = pg_connect("postgresql://zenteamrole:npg_I7ZNn1hVqjtA@ep-raspy-smoke-a5pyv0mk-pooler.us-east-2.aws.neon.tech/zenledgerdb?sslmode=require");


    $accounts_result = pg_query($conn, "SELECT DISTINCT account_name FROM chart_of_accounts ORDER BY account_name ASC");
    ?>

    <form method="GET">
        <label for="accountFilter"><strong>Filter by Account:</strong></label>
        <select name="accountFilter" id="accountFilter" onchange="this.form.submit()">
            <option value="">-- All Accounts --</option>
            <?php
            while ($account = pg_fetch_assoc($accounts_result)) {
                $selected = ($_GET['accountFilter'] ?? '') === $account['account_name'] ? 'selected' : '';
                echo "<option value=\"" . htmlspecialchars($account['account_name']) . "\" $selected>" . htmlspecialchars($account['account_name']) . "</option>";
            }
            ?>
        </select>
    </form>
    <br>


    <form method="GET">
    <label for="year">Select Year:</label>
    <select name="year" id="year" onchange="this.form.submit()">
        <?php
        for ($y = date('Y'); $y >= 2020; $y--) {
            $selected = ($y == $selected_year) ? 'selected' : '';
            echo "<option value=\"$y\" $selected>$y</option>";
        }
        ?>
    </select>
</form>

    <?php

    $filter = $_GET['accountFilter'] ?? '';

    if (!empty($filter)) {
        pg_prepare($conn, "filtered_query", "
            SELECT account_subcategory, account_category, SUM(debit) AS debit, SUM(credit) AS credit
            FROM chart_of_accounts
            WHERE account_name = $1
            GROUP BY account_subcategory, account_category
            ORDER BY account_subcategory ASC
        ");
        $result = pg_execute($conn, "filtered_query", [$filter]);
    } else {
        $result = pg_query($conn, "
            SELECT account_subcategory, account_category, SUM(debit) AS debit, SUM(credit) AS credit
            FROM chart_of_accounts
            GROUP BY account_subcategory, account_category
            ORDER BY account_subcategory ASC
        ");
    }

    $revenues = [];
    $expenses = [];
    $total_revenue = 0;
    $total_expense = 0;

    while ($row = pg_fetch_assoc($result)) {
        $name = $row['account_subcategory'];
        $category = $row['account_category'];
        $debit = floatval($row['debit']);
        $credit = floatval($row['credit']);
        $balance = $credit - $debit;

        if ($category === 'Liabilities' || $category === 'Owners Equity') {
            $revenues[] = ['name' => $name, 'amount' => $balance];
            $total_revenue += $balance;
        } elseif ($category === 'Assets') {
            $amount = $debit - $credit;
            $expenses[] = ['name' => $name, 'amount' => $amount];
            $total_expense += $amount;
        }
    }

    $net_income_before_taxes = $total_revenue - $total_expense;
    $less_taxes = 0;
    $net_income = $net_income_before_taxes - $less_taxes;
    ?>

<div class="print-background">

<?php
$selected_year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
$end_of_year = new DateTime("December 31, $selected_year");

echo "<p style='text-align: center; margin-bottom: 15px;'>Income Statement</p>";
echo "<p style='text-align: center; margin-bottom: 15px;'>For the year ending " . $end_of_year->format("F j, Y") . "</p>";
?>


<table>
    <tr><td colspan="2" class="account-title">Revenues</td></tr>
    <?php foreach ($revenues as $rev): ?>
        <tr>
            <td><?= htmlspecialchars($rev['name']) ?></td>
            <td>$<?= number_format($rev['amount'], 2) ?></td>
        </tr>
    <?php endforeach; ?>
    <tr class="total">
        <td>Total Revenues:</td>
        <td>$<?= number_format($total_revenue, 2) ?></td>
    </tr>


    <tr><td colspan="2" class="account-title">Expenses</td></tr>
    <?php foreach ($expenses as $exp): ?>
        <tr>
            <td><?= htmlspecialchars($exp['name']) ?></td>
            <td>($<?= number_format($exp['amount'], 2) ?>)</td>
        </tr>
    <?php endforeach; ?>
    <tr class="total">
        <td>Total Expenses:</td>
        <td>($<?= number_format($total_expense, 2) ?>)</td>
    </tr>


    <tr><td colspan="2" class="account-title">Summary</td></tr>
    <tr class="total">
        <td>Net Income Before Taxes:</td>
        <td>$<?= number_format($net_income_before_taxes, 2) ?></td>
    </tr>
    <tr class="total">
        <td>Less Taxes:</td>
        <td>$<?= number_format($less_taxes, 2) ?></td>
    </tr>
    <tr class="total">
        <td><strong>Net Income:</strong></td>
        <td><strong>$<?= number_format($net_income, 2) ?></strong></td>
    </tr>

</table>
</div>

    <?php include('snippets/print.php'); ?>

</body>
</html>
