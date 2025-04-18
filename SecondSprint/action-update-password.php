<?php
$last = addslashes($_POST['last']);
$zip = addslashes($_POST['zip']);
$email = addslashes($_POST['email']);
$password = addslashes($_POST['password']);
$hashedpass = password_hash($password, PASSWORD_DEFAULT);

$dbconn = pg_connect("postgresql://zenteamrole:npg_I7ZNn1hVqjtA@ep-raspy-smoke-a5pyv0mk-pooler.us-east-2.aws.neon.tech/zenledgerdb?sslmode=require")
or die('Could not connect: ' . pg_last_error());

$query = "select employee_password, employee_old_passwords from employee_personal_information where employee_last_name = '".$last."' AND employee_zip_code = ".$zip." AND employee_email_address = '".$email."'";
$result = pg_query($dbconn, $query) or die('Query failed: ' . pg_last_error());
$fetchedresults = pg_fetch_array($result, null, PGSQL_NUM);
if (is_null($fetchedresults[0])) {
    $Message = urlencode("Account information is incorrect.");
    header("Location:recover-password.php?Message=".$Message);
}

$old_password = $fetchedresults[0];
$array_of_old_passwords = $fetchedresults[1];
$array_of_old_passwords = substr($array_of_old_passwords, 1);
$array_of_old_passwords = substr($array_of_old_passwords, 0, -1);
$array_of_old_passwords = explode(",", $array_of_old_passwords);

echo $array_of_old_passwords;

if(!is_null($array_of_old_passwords)){ 
    foreach($array_of_old_passwords as $index) {
        if(password_verify($password, $index)) {
            $Message = urlencode("Password has been previously used. Enter a brand new password.");
            header("Location:recover-password.php?Message=".$Message);
            die();
        }
    }
}

$query = "update employee_personal_information set employee_old_passwords = array_append(employee_old_passwords, '".$old_password."') where employee_last_name = '".$last."' AND employee_zip_code = ".$zip." AND employee_email_address = '".$email."' returning *";
$result = pg_query($dbconn, $query) or die('Query failed: ' . pg_last_error());

$query = "update employee_personal_information set employee_password='".$hashedpass."' where employee_last_name = '".$last."' AND employee_zip_code = ".$zip." AND employee_email_address = '".$email."' returning *";
$result = pg_query($dbconn, $query) or die('Query failed: ' . pg_last_error());
    
$Message = urlencode("Successfully updated password.");
header("Location:login.php?Message=".$Message);    
