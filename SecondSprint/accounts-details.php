<?php session_start();
if (!isset($_SESSION["username"])) {
    echo "Error: Unauthorized Page";
    exit();
}
// TODO(Art): Beautify
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>ZenLedger - Account Details</title>
    <link href="style/nonregisterstyle.css" rel="stylesheet" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>
<body>
    <main>
        <?php include('snippets/logged-in-top-bar.php'); ?>
        <h1>Account Details</h1>
        <hr>
        <?php
        $dbconn = pg_connect("postgresql://zenteamrole:npg_I7ZNn1hVqjtA@ep-raspy-smoke-a5pyv0mk-pooler.us-east-2.aws.neon.tech/zenledgerdb?sslmode=require")
            or die('Could not connect: ' . pg_last_error());
        $number = $_GET['number'];
        $query = "SELECT * FROM chart_of_accounts WHERE account_number = $1";
        $result = pg_query_params($dbconn, $query, array($number)) or die('Query failed: ' . pg_last_error());
        if ($row = pg_fetch_assoc($result)) {
            echo "<p><strong>Name:</strong> " . htmlspecialchars(stripslashes($row['account_name'])) . "</p>";
            echo "<p><strong>Number:</strong> " . htmlspecialchars($row['account_number']) . "</p>";
            echo "<p><strong>Category:</strong> " . htmlspecialchars(stripslashes($row['account_category'])) . "</p>";
            echo "<p><strong>Subcategory:</strong> " . htmlspecialchars(stripslashes($row['account_subcategory'])) . "</p>";
            echo "<p><strong>Balance:</strong> $" . number_format($row['balance'], 2) . "</p>";
            echo "<p><strong>Description:</strong> " . htmlspecialchars(stripslashes($row['account_description'])) . "</p>";
            echo "<p><strong>Comment:</strong> " . htmlspecialchars(stripslashes($row['comments'])) . "</p>";
            echo "<p><strong>Status:</strong> " . ($row['is_activated'] === 'true' ? 'Active' : 'Inactive') . "</p>";
            echo "<p><strong>Added On:</strong> " . $row['account_add_date'] . " at " . $row['account_add_time'] . "</p>";
        } else {
            echo "<p>Account not found.</p>";
        }
        pg_close($dbconn);
        ?>
        <p><a href="accounts-view.php">Back to All Accounts</a></p>
    </main>
    <footer>
    <div class="booties"><a href="help.php" class="help-button">Need help?</a>
    </div>
    <script src="snippets/calendar.js"></script> 
    </footer>
</body>
</html>
