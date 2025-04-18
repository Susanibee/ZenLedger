<?php
$string_start_date = $_POST['suspend_start'];
$string_end_date = $_POST['suspend_end'];

$start_date = new DateTime($string_start_date);
$end_date = new DateTime($string_end_date);

// flip 'em!
if($start_date > $end_date) {
    $temp_start = clone($start_date);
    $start_date = clone($end_date);
    $end_date = $temp_start;
}

$difference_in_days = $start_date->diff($end_date)->format("%r%a"); 

$sql_string_start_date = $start_date->format('Y-m-d');

$dbconn = pg_connect("postgresql://zenteamrole:npg_I7ZNn1hVqjtA@ep-raspy-smoke-a5pyv0mk-pooler.us-east-2.aws.neon.tech/zenledgerdb?sslmode=require")
or die('Could not connect: ' . pg_last_error());

$query = "update employee_personal_information set employee_ban_start_date='".$sql_string_start_date."', requested_inactivity_days=".$difference_in_days." where employee_username = '".$_POST['username']."';";
$result = pg_query($dbconn, $query) or die('Query failed: ' . pg_last_error());

header("Location: admin-dashboard.php");
die();
?>
