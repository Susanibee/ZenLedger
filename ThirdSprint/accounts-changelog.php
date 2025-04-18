<?php 
session_start();

if (isset($_SESSION["admin"])) {
    include("snippets/project-utils.php");

    if (empty($_SESSION['selected_customer'])) {
        header("Location: index.php");
        exit();
    }


    include("snippets/cosmic-message.php");
    ?>


        <!DOCTYPE html>
        <html lang="">
            <head>
                <meta charset="utf-8" />
                <meta name="viewport" content="width=device-width, initial-scale=1.0" />
                <link href="style/nonregisterstyle.css" rel="stylesheet" />
                <title>ZenLedger - Accounts Changelog</title>
                <style>
                    [data-tab-info] {
                        display: none;
                    }

                    .active[data-tab-info] {
                        display: block;
                    }
                    .tab-header {
                        cursor:pointer;
                        margin: 4em;
                    }
                </style>
            </head>
          <body>
          <main>
            <?php include('snippets/logged-in-top-bar.php'); ?>
            <div class="cosmic-container">
                <h1>Accounts Changelog</h1>
                <p class="cosmic-message"><?php echo $cosmic_message; ?></p>
            </div>
            <hr>
            <div class="butterdish">
                <a href="accounts-add.php"  class="journal-button">Add</a>
                <a href="accounts-deactivate.php" class="journal-button">Deactivate</a>
                <a href="accounts-edit.php" class="journal-button">Edit</a>
                <a href="accounts-changelog.php" class="journal-button">Changelog</a>
                <a href="accounts-view.php" class="journal-button">View</a>
                </div>
            <div class="tab" id="Add-Tab">
                <table style="margin-top: 20px; width: 100%; border-collapse: collapse;">
                    <tr style="border-bottom: 1px solid #000;">
                        <th>ID</th>
                        <th>Account</th>
                        <th>User</th>
                        <th>Timestamp</th>
                        <th>Before</th>
                        <th>After</th>
                    </tr>
                    <?php 
                        // Connecting, selecting database
                        $dbconn = pg_connect("postgresql://zenteamrole:${{ secrets.pgpass }}@ep-raspy-smoke-a5pyv0mk-pooler.us-east-2.aws.neon.tech/zenledgerdb?sslmode=require")
                            or die('Could not connect: ' . pg_last_error());
                        
                        
                        // SQL query to read columns
                        $html_safe_customer = htmlspecialchars($_SESSION['selected_customer']);
                        $query = "select * from chart_of_accounts_changelog where customer_name='$html_safe_customer' order by changelog_time DESC;";
                        
                        $result = pg_query($dbconn, $query) or die('Query failed: ' . pg_last_error());
                        
                        
                        $counter = 0;
                        while ($row = pg_fetch_row($result, null, PGSQL_ASSOC)) {
                    ?>
                        <tr>
                            <td style="text-align: center;"> <?php echo htmlspecialchars($row['changelog_id']) ?> </td>
                            <td style="text-align: center;">
                                <a href="accounts-details.php?number=<?php echo htmlspecialchars($row['account_id']) ?>">
                                    <?php
                                        $name_query = "select account_name from chart_of_accounts where account_id='$row[account_id]'";
                                        $name_result = pg_query($dbconn, $name_query) or die('Query failed: ' . pg_last_error());
                                        $name_row = pg_fetch_row($name_result, null, PGSQL_NUM);
                                        echo htmlspecialchars($name_row[0]);
                                    ?>
                                </a>
                            </td>
                            <td style="text-align: center;"><?php echo htmlspecialchars($row['employee_username']) ?></td>
                            <td style="text-align: center;"><?php echo htmlspecialchars($row['changelog_time']) ?></td>

                            <td> <!-- before -->
                            <?php
                                $suff = "_before";
                                if(pg_field_is_null($result, $counter,'initial_balance'.$suff) == 0) {
                            ?>
                            <ul>
                                <li>
                                    Name: 
                                    <?php echo $row['account_name'.$suff]; ?>
                                </li>
                                <li>
                                    Description:
                                    <?php echo $row['account_description'.$suff]; ?>
                                </li>
                                <li>
                                    Category:
                                    <?php echo $row['account_category'.$suff]; ?>
                                </li>
                                <li>
                                    Initial Balance:
                                    <?php echo $row['initial_balance'.$suff]; ?>
                                </li>
                                <li>
                                    Order:
                                    <?php echo $row['order'.$suff]; ?>
                                </li>
                                <li>
                                    Statement:
                                    <?php echo $row['statement'.$suff]; ?>
                                </li>
                                <li>
                                    Comments:
                                    <?php echo $row['comments'.$suff]; ?>
                                </li>
                                <li>
                                    Status:
                                    <?php
                                        $activated = $row['is_activated'.$suff] === 't' ? "Activated" : "Deactivated";
                                        echo $activated;
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
                                    Name: 
                                    <?php echo $row['account_name'.$suff]; ?>
                                </li>
                                <li>
                                    Description:
                                    <?php echo $row['account_description'.$suff]; ?>
                                </li>
                                <li>
                                    Category:
                                    <?php echo $row['account_category'.$suff]; ?>
                                </li>
                                <li>
                                    Initial Balance:
                                    <?php echo $row['initial_balance'.$suff]; ?>
                                </li>
                                <li>
                                    Order:
                                    <?php echo $row['order'.$suff]; ?>
                                </li>
                                <li>
                                    Statement:
                                    <?php echo $row['statement'.$suff]; ?>
                                </li>
                                <li>
                                    Comments:
                                    <?php echo $row['comments'.$suff]; ?>
                                </li>
                                <li>
                                    Status:
                                    <?php
                                        $activated = $row['is_activated'.$suff] === 't' ? "Activated" : "Deactivated";
                                        echo $activated;
                                    ?>
                                </li>
                            </ul>
                            </td>
                        </tr>
                        <script>
                        </script>
                    <?php
                        $counter = $counter + 1;       
                    }
                    ?>
                </table>
          </main>
          <div class="booties"><a href="help.php" class="help-button">Need help?</a>
    </div>
    </footer>
          </html>

<?php } else { echo "Unauthorized Page"; } ?>
