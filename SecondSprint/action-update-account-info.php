<?php session_start();

include("snippets/project-utils.php");

$dbconn = pg_connect("postgresql://zenteamrole:npg_I7ZNn1hVqjtA@ep-raspy-smoke-a5pyv0mk-pooler.us-east-2.aws.neon.tech/zenledgerdb?sslmode=require")
or die('Could not connect: ' . pg_last_error());
$number = $_POST["number"];

$query_old_select = "select * from chart_of_accounts where account_number='"
            .$number."'";
$result_old_select = pg_query($dbconn, $query_old_select) or die('Query failed: ' . pg_last_error());

$ref_old_arr = pg_fetch_array($result_old_select, 0, PGSQL_ASSOC);
$old_arr = $ref_old_arr;

// collecting all the info from the form
$name = addslashes($_POST["name"]);
$description = addslashes($_POST["description"]);
$account_category = addslashes($_POST["account-category"]);
$account_subcategory = addslashes($_POST["account-subcategory"]);
$comments = addslashes($_POST["comments"]);

$query_update = "update chart_of_accounts set account_name='".$name
            ."', account_description='".$description
            ."', account_category='".$account_category
            ."', account_subcategory='".$account_subcategory
            ."', comments='".$comments
            ."' where account_number='".$number."';";
$result_update = pg_query($dbconn, $query_update) or die('Query failed: ' . pg_last_error());

$query_new_select = "select * from chart_of_accounts where account_number='"
            .$number."'";
$result_new_select = pg_query($dbconn, $query_new_select) or die('Query failed: ' . pg_last_error());

$ref_new_arr = pg_fetch_array($result_new_select, 0, PGSQL_ASSOC);
$new_arr = $ref_new_arr;

$username = $_SESSION['username'];

$result_arr = addToAccountChangelog($dbconn, $number, $username, $old_arr, $new_arr);

header("Location: accounts-edit.php");
die();
?>
