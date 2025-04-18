<?php
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
        <title>ZenLedger - Ledger</title>
    </head>
    <body>
        <main>
        <?php include('snippets/logged-in-top-bar.php'); ?>

        <div class="cosmic-container">
    <h1>Financial Statements</h1>
    <p class="cosmic-message"><?php echo $cosmic_message; ?></p>
</div>
    <hr>
    <div class="helper">
                  <img src="images/zenledger logo.png" class="background-logo" />
              </div>

    <div class="reports">
        <a href="trial-balance.php" class="report-button">Trial Balance</a><br>
        <a href="income-statement.php" class="report-button">Income Statement</a><br>
        <a href="balance.php" class="report-button">Balance Sheet</a><br>
        <a href="retained-earnings.php" class="report-button">Retained Earnings Statement</a>
    </div>
</main>
<footer>
    <div class="booties"><a href="help.php" class="help-button">Need help?</a>
    </div>
    </footer>
</body>
</html>
