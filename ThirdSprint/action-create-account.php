<?php session_start();

include("snippets/project-utils.php");

$account_name = pg_escape_string($_POST['account-name']);
$account_description = pg_escape_string($_POST['account-description']);
$account_category = pg_escape_string($_POST['account-category']);
$initial_balance = floatval($_POST['initial-balance']);
$order = pg_escape_string($_POST['order']);
$statement = pg_escape_string($_POST['statement']);
$comment = pg_escape_string($_POST['comment']);
$customer = pg_escape_string($_SESSION['selected_customer']);
$username = pg_escape_string(($_SESSION['username']));
$date = date("Y/m/d");
$time = date("Y-m-d H:i:s");

$dbconn = pg_connect("postgresql://zenteamrole:npg_I7ZNn1hVqjtA@ep-raspy-smoke-a5pyv0mk-pooler.us-east-2.aws.neon.tech/zenledgerdb?sslmode=require")
    or die('Could not connect: ' . pg_last_error());

$query = "INSERT INTO chart_of_accounts "
        ."(account_add_time, account_add_date, account_name, account_description, account_category, initial_balance,"
        ."total_balance, total_debit, total_credit, \"order\", statement, comments, is_activated, customer_name) VALUES "
        ."('$time', '$date', '$account_name', '$account_description', '$account_category', "
        ."$initial_balance, $initial_balance, 0, 0, $order, '$statement', '$comment', "
        ."'false', '$customer') "
        ."returning account_id;";

// error_log($query);

$result = pg_query($dbconn, $query) or die('Query failed: ' . pg_last_error());
$row = pg_fetch_row($result, null, PGSQL_NUM);

$changelog_query = "INSERT INTO chart_of_accounts_changelog "
        ."(account_name_after, account_description_after, account_category_after, initial_balance_after, order_after, statement_after,"
        ."comments_after, is_activated_after, account_id, customer_name, employee_username, changelog_time) VALUES "
        ."('$account_name', '$account_description', '$account_category', $initial_balance, '$order', '$statement',"
        ."'$comment', 'false', $row[0], '$customer', '$username', '$time');";
error_log($changelog_query);
$changelog_result = pg_query($dbconn, $changelog_query) or die('Query failed: ' . pg_last_error());

header("Location: accounts-details.php?number=$row[0]");
die();

?>
