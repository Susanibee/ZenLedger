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
        <title>ZenLedger - Accounts Deactivate</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link href="style/nonregisterstyle.css" rel="stylesheet" />
        <!-- Note(Fran): This CSS is **necessary** for html tabbing effect to work. All other CSS goes in a separate stylesheet! -->
        <style>
            [data-tab-info] { display: none; }
            .active[data-tab-info] { display: block; }
            .tab-header { cursor:pointer; margin: 4em; }
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

        <p>
            <?php
                if(isset($_GET['Message'])){
                    echo $_GET['Message'];
            }?>
        </p>

        <table style="margin-top: 20px; width: 100%; border-collapse: collapse;">
            <tr style="border-bottom: 1px solid #000;">
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
            $html_safe_customer = htmlspecialchars($_SESSION['selected_customer']);
            $query = "select account_name, account_id, account_category, total_balance, is_activated from chart_of_accounts where customer_name='$html_safe_customer' order by account_name;";
            
            $result = pg_query($dbconn, $query) or die('Query failed: ' . pg_last_error());
            
            while ($row = pg_fetch_row($result, null, PGSQL_NUM)) { ?>
                <tr style="text-align: center; border-bottom: 1px solid #ddd; line-height: 3em;">
                    <td>
                        <a href="accounts-details.php?number=<?php echo $row[1]?>">
                            <?php echo htmlspecialchars($row[0]) ?>
                        </a>
                    </td>
                    <td><?php echo htmlspecialchars($row[1]) ?></td>
                    <td>$<?php echo htmlspecialchars(number_format($row[3], 2)) ?></td>
                    <td id="<?php echo $row[1];?>">
                        <form action="action-toggle-accout-status.php" method="POST" >
                            <input name="name" value="<?php echo htmlspecialchars($row[0]) ?>" type="hidden">
                            <input name="balance" value="<?php echo htmlspecialchars($row[3]) ?>" type="hidden">
                            <input name="number" value="<?php echo htmlspecialchars($row[1]) ?>" type="hidden">
                            <input name="is_activated" value="<?php echo htmlspecialchars($row[4]) ?>" type="hidden">
                            <a href="javascript:;" onclick="parentNode.submit();"><?php echo ($row[4] === 'true') ? 'Deactivate' : 'Activate' ?></a>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </table>

    </main>

    <footer>
        <div class="booties"><a href="help.php" class="help-button">Need help?</a>
        </div>
    </footer>
    </body>
</html>
