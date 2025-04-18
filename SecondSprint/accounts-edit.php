<?php session_start();
if(isset($_SESSION["username"]))
{
?>
<!DOCTYPE html>
<html lang="">
    <head>
        <meta charset="utf-8">
        <link href="style/nonregisterstyle.css" rel="stylesheet" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>ZenLedger - Accounts Edit</title>
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
        <?php
        if(isset($_SESSION["admin"]))
        {
        ?>
            <a href="accounts-add.php"  ><span class="tab-header">Add</span></a>
            <a href="accounts-deactivate.php"><span class="tab-header">Deactivate</span></a>
            <a href="accounts-edit.php" style="color:white;background-color:#7fb285;"><span class="tab-header">Edit</span></a>
        <?php
        }
        ?>
        <a href="accounts-view.php"><span class="tab-header" data-tab-value="#View-Tab">View</span></a>

        <table style="margin-top: 20px; width: 100%; border-collapse: collapse;">
            <tr style="border-bottom: 1px solid #000;">
                <th>Name</th>
                <th>Number</th>
                <th>Category</th>
                <th>Subcategory</th>
                <th>Action</th>
            </tr>
            <?php 
            // Connecting, selecting database
            $dbconn = pg_connect("postgresql://zenteamrole:npg_I7ZNn1hVqjtA@ep-raspy-smoke-a5pyv0mk-pooler.us-east-2.aws.neon.tech/zenledgerdb?sslmode=require")
                or die('Could not connect: ' . pg_last_error());
            
            
            // SQL query to read columns
            $query = "select account_name, account_number, account_category, account_subcategory, balance from chart_of_accounts order by account_name;";
            
            $result = pg_query($dbconn, $query) or die('Query failed: ' . pg_last_error());
            
            while ($row = pg_fetch_row($result, null, PGSQL_NUM)) { ?>
                <tr style="text-align: center; border-bottom: 1px solid #ddd; line-height: 3em;">
                        <td><?php echo htmlspecialchars($row[0]) ?></td>
                        <td><?php echo htmlspecialchars($row[1]) ?></td>
                        <td><?php echo htmlspecialchars($row[2]) ?></td>
                        <td ><?php echo htmlspecialchars($row[3]) ?></td>
                        <td>
                        <form action="accounts-edit-account-info.php" method="POST">
                            <input type="hidden" name="name" value="<?php echo $row[0]?>">
                            <input type="hidden" name="number" value="<?php echo $row[1]?>">
                            <input type="hidden" name="category" value="<?php echo $row[2]?>">
                            <input type="hidden" name="subcategory" value="<?php echo $row[3]?>">
                            <a href="javascript:;" onclick="parentNode.submit();">Edit Account</a>
                        </form>
                        </td>
                </tr>
            <?php } ?>
        </table>
        </main>

        <footer>
    <div class="booties"><a href="help.php" class="help-button">Need help?</a>
    </div>
    <script src="snippets/calendar.js"></script> 
    </footer>
</html>
<?php
}
else {
    echo("Error: Unauthorized Page");
}
?>
