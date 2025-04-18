<?php
$dbconn = pg_connect("postgresql://zenteamrole:${{ secrets.pgpass }}@ep-raspy-smoke-a5pyv0mk-pooler.us-east-2.aws.neon.tech/zenledgerdb?sslmode=require")
or die('Could not connect: ' . pg_last_error());

// collecting all the info from the form
$username = pg_escape_string($_POST["username"]);
$street = pg_escape_string($_POST["street"]);
$secondary = pg_escape_string($_POST["secondary"]);
$city = pg_escape_string($_POST["city"]);
$state = pg_escape_string($_POST["state"]);
$zip = $_POST["zip"];
$phone = $_POST["phone"];

if(empty($phone)){ $phone="null"; }

$query = "update employee_personal_information set employee_street_address='".$street."', employee_secondary_address='".$secondary."', employee_city='".$city."', employee_state='".$state."', employee_zip_code=".$zip.", employee_phone=".$phone." where employee_username = '".$username."';";
$result = pg_query($dbconn, $query) or die('Query failed: ' . pg_last_error());

header("Location: admin-dashboard.php");
die();
?>
