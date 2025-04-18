<?php session_start();

// Connecting, selecting database
//                                    username : password       @ database url
$dbconn = pg_connect("postgresql://zenteamrole:${{ secrets.pgpass }}@ep-raspy-smoke-a5pyv0mk-pooler.us-east-2.aws.neon.tech/zenledgerdb?sslmode=require")
or die('Could not connect: ' . pg_last_error());

// SQL query to read columns
$query = "select employee_password, employee_role, is_activated, employee_ban_start_date, requested_inactivity_days, password_attempts FROM employee_personal_information WHERE employee_username='".$_POST['username']."'";

$result = pg_query($dbconn, $query) or die('Query failed: ' . pg_last_error());

// variable is literally illogically named
$user_query_array = pg_fetch_array($result, null, PGSQL_NUM);

if($user_query_array == false) {
    $Message = urlencode("Account details are incorrect.");
    header("Location:login.php?Message=".$Message);
    die();
}

//---LOGIN ATTEMPT CHECK---
if(!pg_field_is_null($result,"password_attempts"))
{
    if($user_query_array[5] > 3)
    {
        pg_query($dbconn, "UPDATE employee_personal_information SET is_activated='false' WHERE employee_username = '".$_POST['username']."'");
        $Message = urlencode("Too many login attempts. ");
    }
}

if($user_query_array[2] === "true")
{
    if(password_verify($_POST['password'], $user_query_array[0]))
    {
        // is there a date in the database for inactivity?
        if ( ( pg_field_is_null($result, 0, "employee_ban_start_date") == 0 ) && 
             ( pg_field_is_null($result, 0, "requested_inactivity_days") == 0 ) )
        {
            $string_of_date = $user_query_array[3];
            // php date
            $start_date = new DateTime($string_of_date);
            $end_date = clone($start_date);

            $total_days_of_inactivity = $user_query_array[4];
            if($total_days_of_inactivity == 1)
            {
                $interval = DateInterval::createFromDateString('1 day');
                $end_date->add($interval);
            }
            else
            {
                $interval = DateInterval::createFromDateString($total_days_of_inactivity.' days');
                $end_date->add($interval);
            }

            //$result = $date->format('Y-m-d H:i:s');
            $today = new DateTime(date('Y/m/d'));

            // CHECK IF WE ARE WITHIN THE DATE AND IF SO, KICK THE USER OUT
            if( $today >= $start_date && $today <= $end_date )
            {
                $Message = urlencode("Account is an inactive period.");
                header("Location:login.php?Message=".$Message);
                die();
            }
            // Null both fields!
            else if ( $today > $end_date )
            {
                pg_query($dbconn, "UPDATE employee_personal_information SET employee_ban_start_date=NULL, requested_inactivity_days=NULL WHERE employee_username = '".$_POST['username']."'");

            }
        }

        $_SESSION["username"] = $_POST['username'];
        // check this
        if($user_query_array[1] == "Manager")
        {
            $_SESSION["manager"] = true;
        }
        if($user_query_array[1] == "Administrator")
        {
            $_SESSION["admin"] = true;
        }
        header("Location: index.php");
        die();
    }
    else
    {
         //increment user login attemtps by 1
        $result = pg_query($dbconn, "UPDATE employee_personal_information SET password_attempts=coalesce(password_attempts, 0)+1 WHERE employee_username = '".$_POST['username']."'") or die('Query failed: ' . pg_last_error());
         
        $Message = urlencode("Account details are incorrect.");
        header("Location:login.php?Message=".$Message);
        die();
    }
}
else
{
    $Message = $Message.urlencode("Account is inactive.");
    header("Location:login.php?Message=".$Message);
    die();
}

// Free resultset
pg_free_result($result);

// Closing connection
pg_close($dbconn);

?>
