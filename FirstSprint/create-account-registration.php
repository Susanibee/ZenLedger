<?php session_start();

$first = $_POST['first'];
$last = $_POST['last'];
$street = $_POST['street'];
$secondary = $_POST['secondary'];
$city = $_POST['city'];
$state = $_POST['state'];
$zip = $_POST['zip'];
$dob = $_POST['dob'];
$email = $_POST['email'];
$password = $_POST['password'];
$hashedpass = password_hash($password, PASSWORD_DEFAULT);

// create username from scratch
$username = substr($first, 0, 1) . $last . substr($dob, 5, 2) . substr($dob, 2, 2);
$dbconn = pg_connect("postgresql://zenteamrole:npg_jqtelOpA2V6s@ep-raspy-smoke-a5pyv0mk-pooler.us-east-2.aws.neon.tech/zenledgerdb?sslmode=require")
    or die('Could not connect: ' . pg_last_error());

$query = "INSERT INTO employee_personal_information (employee_username, employee_password, employee_first_name, employee_last_name, employee_street_address, employee_secondary_address, employee_city, employee_state, employee_zip_code, employee_email_address, employee_role, is_activated) VALUES ('".$username."', '".$hashedpass."', '".$first."', '".$last."', '".$street."', '".$secondary."', '".$city."', '".$state."', ".$zip.", '".$email."', 'User', 'false');";

$result = pg_query($dbconn, $query) or die('Query failed: ' . pg_last_error());

header("Location: index.php");
?>