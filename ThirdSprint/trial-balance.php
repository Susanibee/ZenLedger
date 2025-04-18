<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
    <title>ZenLedger - Trial Balance</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .dropdown-form {
            max-width: 400px;
            margin: 20px auto;
            text-align: center;
        }
        .dropdown-form select {
            padding: 10px;
            font-size: 1em;
        }
        .dropdown-form button {
            padding: 10px 20px;
            font-size: 1em;
            margin-left: 10px;
        }
    </style>
</head>
<body>
<main>
<?php include('snippets/logged-in-top-bar.php'); ?>
<div class="cosmic-container">
    <h1>Trial Balance</h1>
    <p class="cosmic-message"><?php echo $cosmic_message; ?></p>
</div>
<hr>

<div class="helper">
                  <img src="images/zenledger logo.png" class="background-logo" />
              </div>

<?php
    $dbconn = pg_connect("postgresql://zenteamrole:${{ secrets.pgpass }}@ep-raspy-smoke-a5pyv0mk-pooler.us-east-2.aws.neon.tech/zenledgerdb?sslmode=require")
        or die('Could not connect: ' . pg_last_error());

    $selected_account = $_GET['account_name'] ?? '';

    $account_names_query = "SELECT DISTINCT account_name FROM chart_of_accounts ORDER BY account_name";
    $account_names_result = pg_query($dbconn, $account_names_query);

    echo "<form method='GET' class='dropdown-form'>";
    echo "<label for='account_name'>Select Account Name: </label>";
    echo "<select name='account_name' id='account_name'>";
    echo "<option value=''>-- All Accounts --</option>";
    while ($row = pg_fetch_assoc($account_names_result)) {
        $account_name = htmlspecialchars($row['account_name']);
        $selected = ($account_name === $selected_account) ? "selected" : "";
        echo "<option value='$account_name' $selected>$account_name</option>";
    }
    echo "</select>";
    echo "<button type='submit'>Filter</button>";
    echo "</form>";





    $params = [];
    $query = "SELECT account_name, account_subcategory, debit, credit FROM chart_of_accounts";

    if (!empty($selected_account)) {
        $query .= " WHERE account_name = $1";
        $params[] = $selected_account;
    }

    $query .= " ORDER BY account_name, account_subcategory";

    $result = !empty($params)
        ? pg_query_params($dbconn, $query, $params)
        : pg_query($dbconn, $query);

    if (!$result) {
        echo "<p>Error executing query: " . pg_last_error($dbconn) . "</p>";
        exit;
    }

    echo "<div class='print-background'>";

    echo "<div style='text-align: center; margin: 20px 0;'>";
    echo "<p>" . ($selected_account ? htmlspecialchars($selected_account) : "All Accounts") . "</p>";
    echo "<p>Trial Balance</p>";
    echo "<p>" . date("F j, Y") . "</p>";
    echo "</div>";

    $current_account = null;
    $subtotal_debit = 0;
    $subtotal_credit = 0;

    while ($row = pg_fetch_assoc($result)) {
        $account_name = $row['account_name'];
        $subcategory = $row['account_subcategory'];
        $debit = floatval($row['debit']);
        $credit = floatval($row['credit']);

        if ($account_name !== $current_account) {
            if ($current_account !== null) {
                echo "<tr class='total-row'>
                        <td colspan='2'>Total for {$current_account}</td>
                        <td align='right'>" . number_format($subtotal_debit, 2) . "</td>
                        <td align='right'>" . number_format($subtotal_credit, 2) . "</td>
                      </tr></table>";
            }

            echo "<div class='accounts'>";
            echo "<div class='account-title'>{$account_name}</div>";
            echo "<table>
                    <tr>
                        <th>Subcategory</th>
                        <th>Debit</th>
                        <th>Credit</th>
                    </tr>";

            $current_account = $account_name;
            $subtotal_debit = 0;
            $subtotal_credit = 0;
        }

        echo "<tr>
                <td>{$subcategory}</td>
                <td align='right'>" . number_format($debit, 2) . "</td>
                <td align='right'>" . number_format($credit, 2) . "</td>
              </tr>";

        $subtotal_debit += $debit;
        $subtotal_credit += $credit;
    }


    if ($current_account !== null) {
        echo "<tr class='total'>
        <td>Total for {$current_account}</td>
                <td align='right'>" . number_format($subtotal_debit, 2) . "</td>
                <td align='right'>" . number_format($subtotal_credit, 2) . "</td>
              </tr></table></div>";
    }

    echo "</div>";
?>
<?php include('snippets/print.php'); ?>
</main>
</body>
</html>
