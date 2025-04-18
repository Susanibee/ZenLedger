<?php
session_start();

if (!isset($_SESSION["admin"])) {
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
    <title>ZenLedger - Edit Account Info</title>
</head>
<body>

    <main>
        <?php include('snippets/logged-in-top-bar.php'); ?>

        <div class="cosmic-container">
            <h1>Edit Account Info</h1>
            <p class="cosmic-message"><?php echo $cosmic_message; ?></p>
        </div>
        <hr>

        <!--
        <div class="helper">
                  <img src="images/zenledger logo.png" class="background-logo" />
              </div>
      -->

        <h2> <?php echo $_POST['username']?> </h2>

        <?php
        $dbconn = pg_connect("postgresql://zenteamrole:npg_I7ZNn1hVqjtA@ep-raspy-smoke-a5pyv0mk-pooler.us-east-2.aws.neon.tech/zenledgerdb?sslmode=require")
        or die('Could not connect: ' . pg_last_error());

        // SQL query to read columns
        $query = "select account_name, account_description, account_category, comments from chart_of_accounts where account_id = '".$_POST["number"]."';";
        $result = pg_query($dbconn, $query) or die('Query failed: ' . pg_last_error());
        $info = pg_fetch_array($result, null, PGSQL_NUM);
        ?>
        <form method="POST" action="action-update-account-info.php">
            <input type="hidden" name="number" value="<?php echo htmlspecialchars($_POST["number"]);?>">

            <label for="name">Name:</label>
            <input type="text" name="name"
            value="<?php echo htmlspecialchars($info[0]);?>"
            required
            >
            <br>
            <label for="description">Description:</label>
            <input  type="text" name="description"
            value="<?php echo htmlspecialchars($info[1]);?>"
            >
            <br>
            <label for="account-category">Account Category</label>
            <select name="account-category" id="account-category" onchange="configureDropDownLists(this,document.getElementById('account-subcategory'))" required>
            <option value="Assets">Assets</option>
            <option value="Liabilities">Liabilities</option>
            <option value="Equity">Equity</option>
            <option value="Revenue">Revenue</option>
            <option value="Expense">Expense</option>
            </select>
            <br>
            <label for="order">Order</label>
            <input type="number" value="1" min="1" step="1" name="order" id="order" required />
            <br>
            <label for="statement">Statement</label>
            <select name="statement" id="statement" required>
                <option value="is">IS</option>
                <option value="bs">BS</option>
                <option value="re">RE</option>
            </select>
            <br>
            <label for="comments">Comment:</label>
            <input type="comments" name="comments"
            value="<?php echo htmlspecialchars($info[4]);?>"
            >
            <br>

            <input type="submit" name="update" value="Update">
        </form>

        <p><a href="accounts-edit.php">Return to Accounts Edit</a></p>
    </main>
    <footer>
    <div class="booties"><a href="help.php" class="help-button">Need help?</a>
    </div>
    </footer>
</body>
</html>

