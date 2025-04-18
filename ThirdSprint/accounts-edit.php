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
        <div class="cosmic-container">
        <h1>Accounts</h1>
        <p class="cosmic-message"><?php echo $cosmic_message; ?></p>
</div>
        <hr>

        <!--
        <div class="helper">
                  <img src="images/zenledger logo.png" class="background-logo" />
        </div>
        -->
        <!-- admin stuff-->

        <div class="butterdish">
        <?php
        if(isset($_SESSION["admin"]))
        {
        ?>

            <a href="accounts-add.php"  class="journal-button">Add</a>
            <a href="accounts-deactivate.php" class="journal-button">Deactivate</a>
            <a href="accounts-edit.php" class="journal-button">Edit</a>
            <a href="accounts-changelog.php" class="journal-button">Changelog</a>
        <?php
        }
        ?>
        <a href="accounts-view.php" class="journal-button">View</a>
    </div>

        <table style="margin-top: 20px; width: 100%; border-collapse: collapse;">
            <tr style="border-bottom: 1px solid #000;">
                <th>Name</th>
                <th>Number</th>
                <th>Category</th>
                <th>Action</th>
            </tr>
            <?php 
            // Connecting, selecting database
            $dbconn = pg_connect("postgresql://zenteamrole:npg_I7ZNn1hVqjtA@ep-raspy-smoke-a5pyv0mk-pooler.us-east-2.aws.neon.tech/zenledgerdb?sslmode=require")
                or die('Could not connect: ' . pg_last_error());
            
            
            // SQL query to read columns
            $html_safe_customer = htmlspecialchars($_SESSION['selected_customer']);
            $query = "select account_name, account_id, account_category from chart_of_accounts where customer_name='$html_safe_customer' order by account_name;";
            
            $result = pg_query($dbconn, $query) or die('Query failed: ' . pg_last_error());
            
            while ($row = pg_fetch_row($result, null, PGSQL_NUM)) { ?>
                <tr style="text-align: center; border-bottom: 1px solid #ddd; line-height: 3em;">
                        <td>
                            <a href="accounts-details.php?number=<?php echo $row[1]?>">
                                <?php echo htmlspecialchars($row[0]) ?>
                            </a>
                        </td>
                        <td ><?php echo htmlspecialchars($row[1]) ?></td>
                        <td ><?php echo htmlspecialchars($row[2]) ?></td>
                        <td>
                        <form action="accounts-edit-account-info.php" method="POST">
                            <input type="hidden" name="name" value="<?php echo $row[0]?>">
                            <input type="hidden" name="number" value="<?php echo $row[1]?>">
                            <input type="hidden" name="category" value="<?php echo $row[2]?>">
                            <a href="javascript:;" onclick="parentNode.submit();">Edit Account</a>
                        </form>
                        </td>
                </tr>
            <?php } ?>
        </table>
        </main>

        <div class="booties"><a href="help.php" class="help-button">Need help?</a>
    </div>
    </footer>
    </body>
</html>
