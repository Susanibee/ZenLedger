<?php session_start();
    if(isset($_SESSION["admin"])) {
        include("snippets/project-utils.php");
    ?>
        <!DOCTYPE html>
        <html lang="">
            <head>
                <meta charset="utf-8" />
                <meta name="viewport" content="width=device-width, initial-scale=1.0" />
                <link href="style/nonregisterstyle.css" rel="stylesheet" />
                <title>ZenLedger - Accounts Changelog</title>
            </head>
          <body>
          <main>
                <?php include('snippets/logged-in-top-bar.php'); ?>
                <h1>Accounts Changelog</h1>
                <hr>
                <table style="margin-top: 20px; width: 100%; border-collapse: collapse;">
                    <tr>
                        <th>ID</th>
                        <th>Account</th>
                        <th>User</th>
                        <th>Timestamp</th>
                        <th>Before</th>
                        <th>After</th>
                    </tr>
                    <?php 
                        // Connecting, selecting database
                        $dbconn = pg_connect("postgresql://zenteamrole:npg_I7ZNn1hVqjtA@ep-raspy-smoke-a5pyv0mk-pooler.us-east-2.aws.neon.tech/zenledgerdb?sslmode=require")
                            or die('Could not connect: ' . pg_last_error());
                        
                        
                        // SQL query to read columns
                        $query = "select * from chart_of_accounts_changelog order by changelog_id;";
                        
                        $result = pg_query($dbconn, $query) or die('Query failed: ' . pg_last_error());
                        
                        
                        while ($row = pg_fetch_row($result, null, PGSQL_ASSOC)) {
                    ?>
                        <tr>
                            <td style="text-align: center;"><?php echo htmlspecialchars($row['changelog_id']) ?></td>
                            <td style="text-align: center;"><?php echo htmlspecialchars($row['account_number']) ?></td>
                            <td style="text-align: center;"><?php echo htmlspecialchars($row['user_id']) ?></td>
                            <td style="text-align: center;"><?php echo htmlspecialchars($row['change_time']) ?></td>

                            <td><i>
                            <?php echoChangelogBeforeImage($row);?>
                            </i></td>

                            <td>
                            <?php echoChangelogAfterImage($row);?>
                            </td>
                        </tr>
                        <script>
                        </script>
                    <?php } ?>
                </table>
          </main>
          <footer>
    <div class="booties"><a href="help.php" class="help-button">Need help?</a>
    </div>
    <script src="snippets/calendar.js"></script> 
    </footer>
          </html>

<?php } else { echo "Unauthorized Page"; } ?>
