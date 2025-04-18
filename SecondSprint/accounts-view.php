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
        <title>ZenLedger - Accounts View</title>
        <!-- Note(Fran): This CSS is **necessary** for html tabbing effect to work. All other CSS goes in a separate stylesheet! -->
        <style>
            [data-tab-info] { display: none; }
            .active[data-tab-info] { display: block; }
            .tab-header { cursor: pointer; margin: 4em; }
        </style>
    </head>
    <body>
        <main>
        <?php include('snippets/logged-in-top-bar.php'); ?>
        <h1> Accounts </h1>
        <hr>
        <?php if(isset($_SESSION["admin"])) { ?>
            <a href="accounts-add.php"><span class="tab-header">Add</span></a>
            <a href="accounts-deactivate.php"><span class="tab-header">Deactivate</span></a>
            <a href="accounts-edit.php"><span class="tab-header">Edit</span></a>
        <?php } ?>
        <a href="accounts-view.php" style="color:white;background-color:#7fb285;"><span class="tab-header" data-tab-value="#View-Tab">View</span></a>
        <!-- admin stuff-->
        <!-- Goal 8 -->
        <form method="GET" action="accounts-view.php" style="margin-bottom: 20px;">
            <label for="search">Search by Name or Number:</label>
            <input type="text" name="search" id="search" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <input type="submit" value="Search" title="Search accounts by name or number">
        </form>

        <!-- Goal 12 -->
        <form method="GET" action="accounts-view.php" style="margin-bottom: 20px;">
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" value="<?php echo isset($_GET['name']) ? htmlspecialchars($_GET['name']) : ''; ?>">
            <label for="number">Number:</label>
            <input type="text" name="number" id="number" value="<?php echo isset($_GET['number']) ? htmlspecialchars($_GET['number']) : ''; ?>">
            <label for="category">Category:</label>
            <input type="text" name="category" id="category" value="<?php echo isset($_GET['category']) ? htmlspecialchars($_GET['category']) : ''; ?>">
            <label for="subcategory">Subcategory:</label>
            <input type="text" name="subcategory" id="subcategory" value="<?php echo isset($_GET['subcategory']) ? htmlspecialchars($_GET['subcategory']) : ''; ?>">
            <label for="balance_min">Min Balance:</label>
            <input type="number" name="balance_min" id="balance_min" step="0.01" value="<?php echo isset($_GET['balance_min']) ? htmlspecialchars($_GET['balance_min']) : ''; ?>">
            <input type="submit" value="Filter" title="Filter accounts by selected criteria">
        </form>
        </main>

        <table style="margin-top: 20px; width: 100%; border-collapse: collapse;">
            <tr style="border-bottom: 1px solid #000;">
                <th>Name</th>
                <th>Number</th>
                <th>Category</th>
                <th>Subcategory</th>
                <th>Balance</th>
                <!-- Goal 7-->
                <th>Action</th>
            </tr>
            <?php 
            // Connecting, selecting database
            $dbconn = pg_connect("postgresql://zenteamrole:npg_I7ZNn1hVqjtA@ep-raspy-smoke-a5pyv0mk-pooler.us-east-2.aws.neon.tech/zenledgerdb?sslmode=require")
                or die('Could not connect: ' . pg_last_error());
            
            // SQL query to read columns
            $conditions = [];
            $params = [];
            $param_count = 1;

            // Goal 8: Search by name or number
            if (!empty($_GET['search'])) {
                $search = $_GET['search'];
                $conditions[] = "(account_name ILIKE $" . $param_count . " OR account_number::TEXT ILIKE $" . $param_count . ")";
                $params[] = "%$search%";
                $param_count++;
            }

            // Goal 12: Filter by various fields
            if (!empty($_GET['name'])) {
                $conditions[] = "account_name ILIKE $" . $param_count;
                $params[] = "%" . $_GET['name'] . "%";
                $param_count++;
            }
            if (!empty($_GET['number'])) {
                $conditions[] = "account_number::TEXT ILIKE $" . $param_count;
                $params[] = "%" . $_GET['number'] . "%";
                $param_count++;
            }
            if (!empty($_GET['category'])) {
                $conditions[] = "account_category ILIKE $" . $param_count;
                $params[] = "%" . $_GET['category'] . "%";
                $param_count++;
            }
            if (!empty($_GET['subcategory'])) {
                $conditions[] = "account_subcategory ILIKE $" . $param_count;
                $params[] = "%" . $_GET['subcategory'] . "%";
                $param_count++;
            }
            if (!empty($_GET['balance_min'])) {
                $conditions[] = "balance >= $" . $param_count;
                $params[] = $_GET['balance_min'];
                $param_count++;
            }

            $query = "SELECT account_name, account_number, account_category, account_subcategory, balance FROM chart_of_accounts";
            if (!empty($conditions)) {
                $query .= " WHERE " . implode(" AND ", $conditions);
            }
            $query .= " ORDER BY account_name";
            
            $result = pg_query_params($dbconn, $query, $params) or die('Query failed: ' . pg_last_error());
            
            while ($row = pg_fetch_row($result, null, PGSQL_NUM)) { ?>
            <tr style="border-bottom: 1px solid #ddd; line-height: 3em;">
                <td style="text-align: center;"> <a href="dummy-ledger.php"> <?php echo htmlspecialchars($row[0]) ?> </a> </td>
                <td style="text-align: center;"><?php echo htmlspecialchars($row[1]) ?></td>
                <td style="text-align: center;"><?php echo htmlspecialchars($row[2]) ?></td>
                <td style="text-align: center;"><?php echo htmlspecialchars($row[3]) ?></td>
                <td  style="text-align: center;">$<?php echo htmlspecialchars($row[4]) ?></td>
                <!-- Goal 7 -->
                <td style="text-align: center;">
                    <a href="accounts-details.php?number=<?php echo $row[1]; ?>">View Details</a>
                </td>
                <script>
                    const balance = document.getElementById("balance");
                    balance.innerHTML = "$"+balance.innerHTML.replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",");
                </script>
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
