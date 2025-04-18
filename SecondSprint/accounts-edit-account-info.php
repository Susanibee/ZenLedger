<?php session_start();
if(isset($_SESSION["admin"])) {
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

        <h1> Edit Account Info </h1>
        <hr>
        <h2> <?php echo $_POST['username']?> </h2>

        <?php
        $dbconn = pg_connect("postgresql://zenteamrole:npg_I7ZNn1hVqjtA@ep-raspy-smoke-a5pyv0mk-pooler.us-east-2.aws.neon.tech/zenledgerdb?sslmode=require")
        or die('Could not connect: ' . pg_last_error());

        // SQL query to read columns
        $query = "select account_name, account_description, account_category, account_subcategory, comments from chart_of_accounts where account_number = '".$_POST["number"]."';";
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
            <option value="Owners Equity">Owner's Equity</option>
            </select><br>

            <label for="account-subcategory">Account Subcategory</label>
            <select name="account-subcategory" id="account-subcategory" required>
                <option value="subcategory">Subcategory</option>
            </select>

            <!-- Javascript to make dropdowns work efficiently:-->
            <script>
            function createOption(ddl, text) {
              var opt = document.createElement('option');
              opt.value = text;
              opt.text = text;
              ddl.options.add(opt);

            }
            function configureDropDownLists(ddl1, ddl2) {
              var assets = ["Cash", "Accounts Receivable", "Inventory", "Prepaid Rent", "Prepaid Insurance", "General Equipment", "Furniture", "Supplies", "Buildings", "Vehicles", "Land", "Investments"];
              var liabilities = ["Accounts Payable", "Taxes Payable", "Salaries Payable", "Interest Payable", "Debts Payable", "Notes Payable", "Accrued Expenses", "Unearned Revenue"];
              var owners_equity = ['Retained Earnings', 'Stock', 'Dividends'];

              switch (ddl1.value) {
                case 'Assets':
                  ddl2.options.length = 0;
                  for (i = 0; i < assets.length; i++) {
                    createOption(ddl2, assets[i], assets[i]);
                  }
                  break;
                case 'Liabilities':
                  ddl2.options.length = 0;
                  for (i = 0; i < liabilities.length; i++) {
                    createOption(ddl2, liabilities[i], liabilities[i]);
                  }
                  break;
                case 'Owners Equity':
                  ddl2.options.length = 0;
                  for (i = 0; i < owners_equity.length; i++) {
                    createOption(ddl2, owners_equity[i], owners_equity[i]);
                  }
                  break;
              }
            }

            document.getElementById("account-category").value =  "<?php echo htmlspecialchars($info[2]);?>";
            configureDropDownLists(document.getElementById("account-category"),document.getElementById("account-subcategory"));
            document.getElementById("account-subcategory").value =  "<?php echo htmlspecialchars($info[3]);?>";
        </script>
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
    <script src="snippets/calendar.js"></script> 
    </footer>
</body>
</html>

<?php }
else {
    echo "Unauthorized Page";
}
?>
