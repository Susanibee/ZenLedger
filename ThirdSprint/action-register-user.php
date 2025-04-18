<?php session_start();

$first = pg_escape_string($_POST['first']);
$last = pg_escape_string($_POST['last']);
$street = pg_escape_string($_POST['street']);
$secondary = pg_escape_string($_POST['secondary']);
$city = pg_escape_string($_POST['city']);
$state = pg_escape_string($_POST['state']);
$zip = $_POST['zip'];
$email = $_POST['email'];
$password = pg_escape_string($_POST['password']);
$hashedpass = password_hash($password, PASSWORD_DEFAULT);

// create username from scratch
$username = substr($first, 0, 1) . $last . date("my");
$dbconn = pg_connect("postgresql://zenteamrole:${{ secrets.pgpass }}@ep-raspy-smoke-a5pyv0mk-pooler.us-east-2.aws.neon.tech/zenledgerdb?sslmode=require")
    or die('Could not connect: ' . pg_last_error());

/* old, working query
$query = "INSERT INTO employee_personal_information (employee_username, employee_password, employee_first_name, employee_last_name, employee_street_address, employee_secondary_address, employee_city, employee_state, employee_zip_code, employee_email_address, employee_role, is_activated) VALUES ('".$username."', '".$hashedpass."', '".$first."', '".$last."', '".$street."', '".$secondary."', '".$city."', '".$state."', ".$zip.", '".$email."', 'User', 'false');";
*/

$randomNumber = rand(100, 999);

$imageQuery = "INSERT INTO employee_images 
    (employee_id_number) 
    VALUES 
    (".$randomNumber.");";

    /*
$chart_query = "INSERT INTO chart_of_accounts (\"employee_id_number\") VALUES (".$randomNumber.");";
    */

$query = "INSERT INTO employee_personal_information 
    (employee_username, employee_password, employee_first_name, employee_last_name, employee_street_address, 
    employee_secondary_address, employee_city, employee_state, employee_zip_code, employee_email_address, 
    employee_role, is_activated, employee_id_number) 
    VALUES 
    ('".$username."', '".$hashedpass."', '".$first."', '".$last."', '".$street."', '".$secondary."', '".$city."', 
    '".$state."', ".$zip.", '".$email."', 'User', 'false', ".$randomNumber.");";

// $result = pg_query($dbconn, $chart_query) or die('Query failed: ' . pg_last_error());
$result = pg_query($dbconn, $imageQuery) or die('Query failed: ' . pg_last_error());
$result = pg_query($dbconn, $query) or die('Query failed: ' . pg_last_error());

header("Location: index.php");
?>
