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
            
            // Main query to fetch accounts with transactions in the date range
            $query = "SELECT account_name, account_id, account_category, account_subcategory, balance 
                      FROM chart_of_accounts 
                      WHERE account_id IN (
                          SELECT DISTINCT (unnest(subentries)).account_id 
                          FROM journal_entries 
                          WHERE is_approved = TRUE";
            if (!empty($_GET['date_min'])) {
                $query .= " AND date >= '" . pg_escape_string($_GET['date_min']) . "'";
            }
            if (!empty($_GET['date_max'])) {
                $query .= " AND date <= '" . pg_escape_string($_GET['date_max']) . "'";
            }
            $query .= ")";
            $conditions = [];
            if (!empty($_GET['name'])) {
                $conditions[] = "account_name ILIKE '%" . pg_escape_string($_GET['name']) . "%'";
            }
            if (!empty($_GET['balance_min'])) {
                $conditions[] = "balance >= " . floatval($_GET['balance_min']);
            }
            if (!empty($conditions)) {
                $query .= " AND " . implode(" AND ", $conditions);
            }
            $query .= " ORDER BY account_name";
            
            $result = pg_query($dbconn, $query) or die('Query failed: ' . pg_last_error());
            $category_subcategory_list = [];
            while ($row = pg_fetch_row($result, null, PGSQL_NUM)) {
                $category_subcategory_list[$row[2]][$row[3]][] = array($row[0], $row[1], $row[4]);
            } 
            $category_keys = array_keys($category_subcategory_list);
            $category_index = 0;
            foreach($category_subcategory_list as $category) {
                echo "<h1>".$category_keys[$category_index]."</h1>";
                $subcategory_keys = array_keys($category);
                $subcategory_index = 0;
                foreach($category as $subcategory) {
                    echo "<h2>".$subcategory_keys[$subcategory_index]."</h2>";
                    foreach($subcategory as $account) {
                        echo "<div style=\"display: grid; grid-template-columns: 1fr 1fr; text-align: center;\">";
                        echo "<div>";
                        echo "<h3 id=\"".$account[1]."\">".$account[1]." - ".$account[0]."</h3>";
                        echo "<table style=\"width:100%\">";
                        echo "<tr>";
                        echo "<th>PR</th>";
                        echo "<th>DATE</th>";
                        echo "<th>DEBIT</th>";
                        echo "<th>CREDIT</th>";
                        echo "<th>BALANCE</th>";
                        echo "</tr>";
                        $trans_query = "SELECT post_reference, date, (unnest).is_crediting, (unnest).amount, (unnest).account_balance 
                                        FROM (SELECT unnest(subentries), date, post_reference 
                                              FROM journal_entries 
                                              WHERE is_approved = TRUE) AS t 
                                        WHERE (unnest).account_id = " . $account[1];
                        if (!empty($_GET['date_min'])) {
                            $trans_query .= " AND date >= '" . pg_escape_string($_GET['date_min']) . "'";
                        }
                        if (!empty($_GET['date_max'])) {
                            $trans_query .= " AND date <= '" . pg_escape_string($_GET['date_max']) . "'";
                        }
                        $trans_result = pg_query($dbconn, $trans_query) or die('Query failed: ' . pg_last_error());
                        while ($row = pg_fetch_row($trans_result, null, PGSQL_NUM)) {
                            echo "<tr>";
                            echo "<td><a href=\"journal-view.php#".$row[0]."\">".$row[0]."</a></td>";
                            echo "<td>".$row[1]."</td>";
                            if($row[2] == 'f') {
                                echo "<td>$".number_format($row[3], 2)."</td>";
                                echo "<td></td>";
                            } else {
                                echo "<td></td>";
                                echo "<td>$".number_format($row[3], 2)."</td>";
                            }
                            echo "<td>$".number_format($row[4], 2)."</td>";
                            echo "</tr>";
                        }
                        echo "</table>";
                        echo "</div>";
                        echo "</div>";
                    }
                    $subcategory_index++;
                }
                $category_index++;
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
