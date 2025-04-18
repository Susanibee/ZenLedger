<?php session_start();

include("snippets/project-utils.php");

$dbconn = pg_connect("postgresql://zenteamrole:npg_I7ZNn1hVqjtA@ep-raspy-smoke-a5pyv0mk-pooler.us-east-2.aws.neon.tech/zenledgerdb?sslmode=require")
or die('Could not connect: ' . pg_last_error());

$number = $_POST['number'];
$new_status = $_POST['is_activated'] === "true" ? 'false' : 'true';
$balance = $_POST['balance'];

if(floatval($balance) > 0) {
    $Message = urlencode("Account has not been deactivated. Balance must be at $0 to deactivate.");
    header("Location: accounts-deactivate.php?Message=".$Message);
    die();
}

$query = "update chart_of_accounts set is_activated = '". $new_status ."' where account_number = '".$number."' returning *;";

$result = pg_query($dbconn, $query) or die('Query failed: ' . pg_last_error());

$result_arr = pg_fetch_array($result, 0, PGSQL_ASSOC);
$old_result_arr = $result_arr;
$old_result_arr['is_activated'] = $result_arr['is_activated'] === "true" ? 'false' : 'true';

$username = $_SESSION['username'];
addToAccountChangelog($dbconn, $number, $username, $old_result_arr, $result_arr);

header("Location: accounts-deactivate.php");
die();

?>
