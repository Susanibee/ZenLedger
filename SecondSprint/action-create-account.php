<?php session_start();

include("snippets/project-utils.php");

$account_name = addslashes($_POST['account-name']);
$account_number = $_POST['account-number'];
$account_description = addslashes($_POST['account-description']);
$account_category = addslashes($_POST['account-category']);
$account_subcategory = addslashes($_POST['account-subcategory']);
$initial_balance = floatval($_POST['initial-balance']);
$debit = floatval($_POST['debit']);
$credit = floatval($_POST['credit']);
$order = $_POST['order'];
$statement = addslashes($_POST['statement']);
$comment = addslashes($_POST['comment']);

$date = date("Y/m/d");
$time = date("Y-m-d H:i:s");


$dbconn = pg_connect("postgresql://zenteamrole:npg_I7ZNn1hVqjtA@ep-raspy-smoke-a5pyv0mk-pooler.us-east-2.aws.neon.tech/zenledgerdb?sslmode=require")
    or die('Could not connect: ' . pg_last_error());

$query = "INSERT INTO chart_of_accounts (account_add_time, account_add_date, account_name, account_number, account_description, account_category, account_subcategory, initial_balance, balance, debit, credit, \"order\", statement, comments, is_activated) VALUES ('".$time."', '".$date."' , '".$account_name."', ".$account_number.", '".$account_description."', '".$account_category."', '".$account_subcategory."', ".$initial_balance.", ".$initial_balance.", ".$debit.", ".$credit.", ".$order.", '".$statement."', '".$comment."', 'true') returning *;";

$result = pg_query($dbconn, $query) or die('Query failed: ' . pg_last_error());
$result_arr = pg_fetch_array($result, 0, PGSQL_ASSOC);

$username = $_SESSION['username'];
addToAccountChangelog($dbconn, $account_number, $username, null, $result_arr);

header("Location: accounts-add.php");
die();

?>
