<?php
session_start();

if (!isset($_SESSION["admin"]) && !isset($_SESSION["username"])) {
    echo "Unauthorized Page";
    exit;
}

if (empty($_SESSION["selected_customer"])) {
    header("Location: index.php"); 
    exit;
}
include("snippets/cosmic-message.php");

include("snippets/project-utils.php");
?>


<!DOCTYPE html>
<html>
<head>
    <title>ZenLedger - Journal Changelog</title>
    <link href="style/nonregisterstyle.css" rel="stylesheet" />
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>
<body>
    <main>
        <?php include('snippets/logged-in-top-bar.php'); ?>
        <div class="cosmic-container">
            <h1>Journal Changelog</h1>
            <p class="cosmic-message"><?php echo $cosmic_message; ?></p>
        </div>
        <hr>
        <!--
        <div class="helper">
            <img src="images/zenledger logo.png" class="background-logo" />
        </div>
        -->
        <?php include("snippets/journal-tab-bar.php"); ?>
        <table style="margin-top: 20px; width: 100%; border-collapse: collapse;">
            <tr style="border-bottom: 1px solid #000;">
                <th>ID</th>
                <th>PR</th>
                <th>Date</th>
                <th>Before</th>
                <th>After</th>
            </tr>
            <?php
                $dbconn = pg_connect("postgresql://zenteamrole:${{ secrets.pgpass }}@ep-raspy-smoke-a5pyv0mk-pooler.us-east-2.aws.neon.tech/zenledgerdb?sslmode=require")
                    or die('Could not connect: ' . pg_last_error());
                
                // SQL query to read columns
                $html_safe_customer = htmlspecialchars($_SESSION['selected_customer']);
                $query = "select * from journal_entries_changelog where customer_name='$html_safe_customer' order by time_stamp desc;";
                $result = pg_query($dbconn, $query) or die('Query failed: ' . pg_last_error());
                $counter = 0;
                while ($row = pg_fetch_row($result, null, PGSQL_ASSOC)) {
            ?>
                    <tr>
                        <td style="text-align: center;"> <?php echo htmlspecialchars($row['entry_id']) ?> </td>
                        <td style="text-align: center;"> <?php echo htmlspecialchars($row['post_reference']) ?> </td>
                        <td style="text-align: center;"><?php echo htmlspecialchars($row['date']) ?></td>

                        <td> <!-- before -->
                        <?php
                            $suff = "_before";
                            if(pg_field_is_null($result, $counter,'journal_subentry'.$suff) == 0) {
                        ?>
                        <ul>
                            <li>
                                Description:
                                <?php echo htmlspecialchars($row['description'.$suff]); ?>
                            </li>
                            <li>
                                Comments:
                                <?php echo htmlspecialchars($row['comments'.$suff]); ?>
                            </li>
                            <li>
                                Subentries:<ul>
                                <?php
                                    $subentries_query =" select (before).is_crediting as is_crediting, (before).amount as amount, (before).account_id as account_id from (select unnest(journal_subentry_before) as before, unnest(journal_subentry_after) as after from journal_entries_changelog where entry_id='$row[entry_id]') ";
                                    $subentries_result = pg_query($dbconn, $subentries_query) or die('Query failed: ' . pg_last_error());
                                    while ($sub = pg_fetch_row($subentries_result, null, PGSQL_ASSOC)) {
                                        echo "<li><span>";
                                        if($sub['is_crediting']=="t") {
                                            echo "CREDIT ";
                                        }
                                        else {
                                            echo "DEBIT ";
                                        }
                                        echo '<a href="accounts-details.php?number='.$sub['account_id'].'"\">';

                                        $query = "select account_name from chart_of_accounts where account_id='$sub[account_id]'";
                                        $s_result = pg_query($dbconn, $query) or die('Query failed: ' . pg_last_error());
                                        $q_row = pg_fetch_row($s_result, null, PGSQL_NUM);
                                        echo htmlspecialchars($q_row[0])."</a> ";
                                        echo "$".$sub['amount']."</li>";
                                        echo "</span></li>";
                                    }
                                ?>
                                    </ul>
                            </li>
                            <li>
                                Date:
                                <?php echo htmlspecialchars($row['date'.$suff]); ?>
                            </li>
                            <li>
                                Approval:
                                <?php
                                    $approved = $row['is_approved'.$suff] === 't' ? "Approved" : "Unapproved";
                                    if($approved == "Unapproved") {
                                        if($row['is_rejected'.$suff] == 't') {
                                            $approved = "Rejected";
                                        };
                                    }
                                    echo htmlspecialchars($approved);
                                ?>
                            </li>
                        </ul>
                        <?php } ?>
                        </td>

                        <td>
                        <?php
                            $suff = "_after";
                        ?>
                        <ul>
                            <li>
                                Description:
                                <?php echo htmlspecialchars($row['description'.$suff]); ?>
                            </li>
                            <li>
                                Comments:
                                <?php echo htmlspecialchars($row['comments'.$suff]); ?>
                            </li>
                            <li>
                                Subentries:<ul>
                                <?php
                                    $subentries_query =" select (after).is_crediting as is_crediting, (after).amount as amount, (after).account_id as account_id from (select unnest(journal_subentry_before) as before, unnest(journal_subentry_after) as after from journal_entries_changelog where entry_id='$row[entry_id]') ";
                                    $subentries_result = pg_query($dbconn, $subentries_query) or die('Query failed: ' . pg_last_error());
                                    while ($sub = pg_fetch_row($subentries_result, null, PGSQL_ASSOC)) {
                                        echo "<li><span>";
                                        if($sub['is_crediting']=="t") {
                                            echo "CREDIT ";
                                        }
                                        else {
                                            echo "DEBIT ";
                                        }
                                        echo '<a href="accounts-details.php?number='.$sub['account_id'].'"\">';

                                        $query = "select account_name from chart_of_accounts where account_id='$sub[account_id]'";
                                        $s_result = pg_query($dbconn, $query) or die('Query failed: ' . pg_last_error());
                                        $q_row = pg_fetch_row($s_result, null, PGSQL_NUM);
                                        echo htmlspecialchars($q_row[0])."</a> ";
                                        echo "$".$sub['amount']."</li>";
                                        echo "</span></li>";
                                    }
                                ?>
                                    </ul>
                            </li>
                            <li>
                                Date:
                                <?php echo htmlspecialchars($row['date'.$suff]); ?>
                            </li>
                            <li>
                                Approval:
                                <?php
                                    $approved = $row['is_approved'.$suff] === 't' ? "Approved" : "Unapproved";
                                    if($approved == "Unapproved") {
                                        if($row['is_rejected'.$suff] == 't') {
                                            $approved = "Rejected";
                                        };
                                    }
                                    echo htmlspecialchars($approved);
                                ?>
                            </li>
                        </ul>
                        </td>
                    </tr>
            <?php 
                $counter = $counter + 1;
            } ?>
        </table>
    </main>
    <footer>
    <div class="booties"><a href="help.php" class="help-button">Need help?</a>
    </div>
    </footer>
</body>
</html>
