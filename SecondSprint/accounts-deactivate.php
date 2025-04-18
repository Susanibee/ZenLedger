<?php session_start();
if(isset($_SESSION["username"]))
{
?>
<!DOCTYPE html>
<html lang="">
    <head>
        <meta charset="utf-8">
        <title>ZenLedger - Accounts Deactivate</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link href="style/nonregisterstyle.css" rel="stylesheet" />
        <!-- Note(Fran): This CSS is **necessary** for html tabbing effect to work. All other CSS goes in a separate stylesheet! -->
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
        <h1> Accounts </h1>
        <hr>
        <!-- admin stuff-->
        </main>
        <?php
        if(isset($_SESSION["admin"]))
        {
        ?>
            <a href="accounts-add.php"  ><span class="tab-header">Add</span></a>
            <a href="accounts-deactivate.php" style="color:white;background-color:#7fb285;"><span class="tab-header">Deactivate</span></a>
            <a href="accounts-edit.php" ><span class="tab-header">Edit</span></a>
        <?php
        }
        ?>
        <a href="accounts-view.php"><span class="tab-header" data-tab-value="#View-Tab">View</span></a>

        <p>
            <?php
                if(isset($_GET['Message'])){
                    echo $_GET['Message'];
            }?>
        </p>

        <table style="border-bottom: 1px solid #000; margin-top: 20px; width: 100%; border-collapse: collapse;">
            <tr>
                <th>Name</th>
                <th>Number</th>
                <th>Balance</th>
                <th>Action</th>
            </tr>
            <?php 
            // Connecting, selecting database
            $dbconn = pg_connect("postgresql://zenteamrole:npg_I7ZNn1hVqjtA@ep-raspy-smoke-a5pyv0mk-pooler.us-east-2.aws.neon.tech/zenledgerdb?sslmode=require")
                or die('Could not connect: ' . pg_last_error());
            
            
            // SQL query to read columns
            $query = "select account_name, account_number, account_category, account_subcategory, balance, is_activated from chart_of_accounts order by account_name;";
            
            $result = pg_query($dbconn, $query) or die('Query failed: ' . pg_last_error());
            
            while ($row = pg_fetch_row($result, null, PGSQL_NUM)) { ?>
            <tr style="text-align: center; border-bottom: 1px solid #ddd; line-height: 3em;">
                <td><?php echo htmlspecialchars($row[0]) ?></td>
                <td><?php echo htmlspecialchars($row[1]) ?></td>
                <td>$<?php echo htmlspecialchars($row[4]) ?></td>
                <td>
                <form action="action-toggle-accout-status.php" method="POST">
                    <input name="balance" value="<?php echo htmlspecialchars($row[4]) ?>" type="hidden">
                    <input name="number" value="<?php echo htmlspecialchars($row[1]) ?>" type="hidden">
                    <input name="is_activated" value="<?php echo htmlspecialchars($row[5]) ?>" type="hidden">
                    <a href="javascript:;" onclick="parentNode.submit();"><?php echo ($row[5] === 'true') ? 'Deactivate' : 'Activate' ?></a>
                </form>
                </td>
            </tr>
            <?php } ?>
        </table>

        <footer>
    <div class="booties"><a href="help.php" class="help-button">Need help?</a>
    </div>
    <script src="snippets/calendar.js"></script> 
    </footer>
    </body>
</html>
<?php
}
else {
    echo("Error: Unauthorized Page");
}
?>
