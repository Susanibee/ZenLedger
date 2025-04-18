<?php session_start();

$username = pg_escape_string($_POST['username']);
$password = pg_escape_string($_POST['password']);
$hashedpass = password_hash($password, PASSWORD_DEFAULT);
$role = $_POST['role'];

$dbconn = pg_connect("postgresql://zenteamrole:${{ secrets.pgpass }}@ep-raspy-smoke-a5pyv0mk-pooler.us-east-2.aws.neon.tech/zenledgerdb?sslmode=require")
    or die('Could not connect: ' . pg_last_error());

$query = "INSERT INTO employee_personal_information (employee_username, employee_password, employee_role, is_activated, \"employee_ID_number\") VALUES ('".$username."', '".$hashedpass."', '".$role."', 'true', 000);";

$result = pg_query($dbconn, $query) or die('Query failed: ' . pg_last_error());

if(isset($_SESSION["admin"])) {
    header("Location: admin-dashboard.php");
}
?>
