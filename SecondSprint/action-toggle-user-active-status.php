<?php

$dbconn = pg_connect("postgresql://zenteamrole:npg_I7ZNn1hVqjtA@ep-raspy-smoke-a5pyv0mk-pooler.us-east-2.aws.neon.tech/zenledgerdb?sslmode=require")
or die('Could not connect: ' . pg_last_error());

$username = $_POST['username'];
$new_status = $_POST['is_activated'] === "true" ? 'false' : 'true';

$query = "update employee_personal_information set is_activated = '". $new_status ."', password_attempts=0 where employee_username = '".$username."';";

$result = pg_query($dbconn, $query) or die('Query failed: ' . pg_last_error());
header("Location: admin-dashboard.php");

?>
