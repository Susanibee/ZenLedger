<?php
$dbconn = pg_connect("postgresql://zenteamrole:npg_I7ZNn1hVqjtA@ep-raspy-smoke-a5pyv0mk-pooler.us-east-2.aws.neon.tech/zenledgerdb?sslmode=require")
or die('Could not connect: ' . pg_last_error());

// collecting all the info from the form
$username = addslashes($_POST["username"]);
$street = addslashes($_POST["street"]);
$secondary = addslashes($_POST["secondary"]);
$city = addslashes($_POST["city"]);
$state = addslashes($_POST["state"]);
$zip = $_POST["zip"];
$phone = $_POST["phone"];

if(empty($phone)){ $phone="null"; }

$query = "update employee_personal_information set employee_street_address='".$street."', employee_secondary_address='".$secondary."', employee_city='".$city."', employee_state='".$state."', employee_zip_code=".$zip.", employee_phone=".$phone." where employee_username = '".$username."';";
$result = pg_query($dbconn, $query) or die('Query failed: ' . pg_last_error());

header("Location: admin-dashboard.php");
die();
?>
