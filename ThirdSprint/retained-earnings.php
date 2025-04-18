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
        <title>ZenLedger - Retained Earnings</title>
    </head>
    <body>
        <main>
        <?php include('snippets/logged-in-top-bar.php'); ?>
        <div class="cosmic-container">
    <h1>Retained Earnings Statement</h1>
    <p class="cosmic-message"><?php echo $cosmic_message; ?></p>
</div>
        <hr>
        <div class="helper">
                  <img src="images/zenledger logo.png" class="background-logo" />
              </div>

<?php
    $conn = pg_connect("postgresql://zenteamrole:npg_I7ZNn1hVqjtA@ep-raspy-smoke-a5pyv0mk-pooler.us-east-2.aws.neon.tech/zenledgerdb?sslmode=require");

    $result = pg_query($conn, "
        SELECT account_subcategory, SUM(debit) as total_debit, SUM(credit) as total_credit 
        FROM chart_of_accounts 
        WHERE account_subcategory IN ('Revenue', 'Expense', 'Dividend', 'Retained Earnings')
        GROUP BY account_subcategory
    ");

    $net_income = 0;
    $dividends = 0;
    $beginning_re = 0;

    while ($row = pg_fetch_assoc($result)) {
        $type = strtolower($row['account_type']);
        $debit = $row['total_debit'];
        $credit = $row['total_credit'];

        switch ($type) {
            case 'revenue': $net_income += ($credit - $debit); break;
            case 'expense': $net_income -= ($debit - $credit); break;
            case 'dividend': $dividends = $debit; break;
            case 'retained earnings': $beginning_re = $credit - $debit; break;
        }
    }

    $ending_re = $beginning_re + $net_income - $dividends;
?>

    <table>
        <tr><td class="total">Previous Amount</td><td class="amount">$<?= number_format($beginning_re, 2) ?></td></tr>
        <tr><td class="total">Add Net Income</td><td class="amount">$<?= number_format($net_income, 2) ?></td></tr>
        <tr><td class="total">Less Dividends</td><td class="amount">($<?= number_format($dividends, 2) ?>)</td></tr>
        <tr><th class="total">Retained Earnings</th><th class="amount">$<?= number_format($ending_re, 2) ?></th></tr>
    </table>

    <?php include('snippets/print.php'); ?>

</body>
</html>
