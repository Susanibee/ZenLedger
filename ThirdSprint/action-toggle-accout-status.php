<?php session_start();

include("snippets/project-utils.php");

$dbconn = pg_connect("postgresql://zenteamrole:${{ secrets.pgpass }}@ep-raspy-smoke-a5pyv0mk-pooler.us-east-2.aws.neon.tech/zenledgerdb?sslmode=require")
or die('Could not connect: ' . pg_last_error());

$number = $_POST['number'];
$old_status = $_POST['is_activated'];
$new_status = $old_status === "true" ? 'false' : 'true';
$balance = $_POST['balance'];
$name = htmlspecialchars($_POST['name']);

if(floatval($balance) > 0 && ($old_status=='true')) {
    $Message = urlencode("<em>$name</em> has not been deactivated. Balance must be at <em>$0</em> to deactivate.");
    header("Location: accounts-deactivate.php?Message=".$Message);
    die();
}

$query = "update chart_of_accounts set is_activated = '". $new_status ."' where account_id = '".$number."' returning *;";

$result = pg_query($dbconn, $query) or die('Query failed: ' . pg_last_error());

$result_arr = pg_fetch_array($result, 0, PGSQL_ASSOC);

$account_name = pg_escape_string($result_arr['account_name']);
$account_description = pg_escape_string($result_arr['account_description']);
$account_category = pg_escape_string($result_arr['account_category']);
$initial_balance = floatval($result_arr['initial_balance']);
$order = pg_escape_string($result_arr['order']);
$statement = pg_escape_string($result_arr['statement']);
$comment = pg_escape_string($result_arr['comments']);

$customer = pg_escape_string($_SESSION['selected_customer']);
$username = pg_escape_string(($_SESSION['username']));
$time = date("Y-m-d H:i:s");

$changelog_query = "INSERT INTO chart_of_accounts_changelog "
        ."(account_name_before, account_description_before, account_category_before, initial_balance_before, order_before, statement_before,"
        ."comments_before, is_activated_before,"
        ."account_name_after, account_description_after, account_category_after, initial_balance_after, order_after, statement_after,"
        ."comments_after, is_activated_after,"
        ." account_id, customer_name, employee_username, changelog_time) VALUES "
        ."('$account_name', '$account_description', '$account_category', $initial_balance, '$order', '$statement',"
        ."'$comment', '$old_status',"
        ."'$account_name', '$account_description', '$account_category', $initial_balance, '$order', '$statement',"
        ."'$comment', '$new_status',"
        ."$number, '$customer', '$username', '$time');";
        error_log($changelog_query);
$result = pg_query($dbconn, $changelog_query) or die('Query failed: ' . pg_last_error());


header("Location: accounts-deactivate.php");
die();

?>
