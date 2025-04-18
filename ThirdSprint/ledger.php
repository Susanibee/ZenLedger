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
    <h1>Ledger</h1>
    <p class="cosmic-message"><?php echo $cosmic_message; ?></p>
</div>
        <hr>
        <div class="helper">
                  <img src="images/zenledger logo.png" class="background-logo" />
              </div>

        <form method="GET" action="" style="margin-bottom: 20px;">
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" value="<?php echo isset($_GET['name']) ? htmlspecialchars($_GET['name']) : ''; ?>">
            <label for="balance_min">Min Balance:</label>
            <input type="number" name="balance_min" id="balance_min" step="0.01" value="<?php echo isset($_GET['balance_min']) ? htmlspecialchars($_GET['balance_min']) : ''; ?>">
            <label for="date_min">Earliest:</label>
            <input type="date" name="date_min" id="date_min" value="<?php echo isset($_GET['date_min']) ? htmlspecialchars($_GET['date_min']) : ''; ?>">
            <label for="date_max">Latest:</label>
            <input type="date" name="date_max" id="date_max" value="<?php echo isset($_GET['date_max']) ? htmlspecialchars($_GET['date_max']) : ''; ?>">
            <input type="submit" value="Filter" title="Filter accounts by selected criteria">
        </form>

        <?php 
$dbconn = pg_connect("postgresql://zenteamrole:npg_I7ZNn1hVqjtA@ep-raspy-smoke-a5pyv0mk-pooler.us-east-2.aws.neon.tech/zenledgerdb?sslmode=require")
    or die('Could not connect: ' . pg_last_error());

$customer = pg_escape_string($_SESSION['selected_customer']);


$accounts_query = "
    SELECT account_id, account_name 
    FROM chart_of_accounts 
    WHERE customer_name = '$customer'
    ORDER BY account_name
";
$accounts_result = pg_query($dbconn, $accounts_query) or die('Query failed: ' . pg_last_error());

$accounts = [];
while ($row = pg_fetch_assoc($accounts_result)) {
    $accounts[$row['account_id']] = $row['account_name'];
}


foreach ($accounts as $account_id => $account_name) {
    $entry_query = "
        SELECT je.date, je.description, je.post_reference,
               s.is_crediting, s.amount, s.account_balance
        FROM journal_entries je,
             LATERAL unnest(je.subentries) AS s
        WHERE s.account_id = $account_id 
          AND je.is_approved = TRUE
    ";


    if (!empty($_GET['date_min'])) {
        $date_min = pg_escape_string($_GET['date_min']);
        $entry_query .= " AND je.date >= '$date_min'";
    }
    if (!empty($_GET['date_max'])) {
        $date_max = pg_escape_string($_GET['date_max']);
        $entry_query .= " AND je.date <= '$date_max'";
    }

    $entry_query .= " ORDER BY je.date";

    $entry_result = pg_query($dbconn, $entry_query) or die('Query failed: ' . pg_last_error());

    if (pg_num_rows($entry_result) === 0) continue;



    echo "<div class='center-ledger'>";
    echo "<h4 class='account-name'>" . htmlspecialchars($account_name) . " (ID: " . $account_id . ")</h4>";


    echo <<<EOT
    <style>
        table.ledger-table {
            width: 75%;
            border-collapse: collapse;
            margin: 0 auto;
            text-align: center;
            font-size: 0.9em;
            margin-bottom: 30px;
        }
    
        table.ledger-table th, 
        table.ledger-table td {
            border: 1px solid #aaa;
            padding: 6px;
        }
    
        table.ledger-table th {
            border-bottom: 2px solid #444;
        }
    
        table.ledger-table td:nth-child(2) {
            border-right: 2px solid #444;
        }
    
        table.ledger-table th:nth-child(2) {
            border-right: 2px solid #444;
        }

         .center-ledger {
        width: 90%;
        margin: 0 auto 40px auto; 
    }

    .account-name {
        font-size: 1em;
        margin-bottom: 10px;
        text-align: left;
        padding-left: 12%;
    }

    </style>
    <table class='ledger-table'>
        <tr>
            <th>Date</th>
            <th>Debit</th>
            <th>Credit</th>
            <th>Balance</th>
            <th>Description</th>
            <th>Post Ref</th>
        </tr>
    EOT;


    while ($row = pg_fetch_row($entry_result, null, PGSQL_NUM)) {
        list($date, $desc, $pr, $is_crediting, $amount, $balance) = $row;
    
        echo "<tr>";
        echo "<td>" . htmlspecialchars($date) . "</td>";
    
        if ($is_crediting === 'f') {
            echo "<td>$" . number_format($amount, 2) . "</td><td></td>";
        } else {
            echo "<td></td><td>$" . number_format($amount, 2) . "</td>";
        }
    
        echo "<td>$" . number_format($balance, 2) . "</td>";
        echo "<td>" . htmlspecialchars($desc) . "</td>";
        echo "<td><a href='journal-view.php#$pr'>" . htmlspecialchars($pr) . "</a></td>";
        echo "</tr>";
    }



    echo "</table>";
    echo "</div>";
}

pg_close($dbconn);
?>



        </main>
        <footer>
        <div class="booties"><a href="help.php" class="help-button">Need help?</a>
        </div>
        </footer>
    </body>
</html>
