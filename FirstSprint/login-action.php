<?php session_start();

// TODO: FIX LOGIN ATTEMPT QUERIES

// Connecting, selecting database
//                                    username : password       @ database url
$dbconn = pg_connect("postgresql://zenteamrole:npg_jqtelOpA2V6s@ep-raspy-smoke-a5pyv0mk-pooler.us-east-2.aws.neon.tech/zenledgerdb?sslmode=require")
    or die('Could not connect: ' . pg_last_error());


// SQL query to read columns
$query = "select employee_password,employee_role,is_activated, employee_ban_start_date, requested_inactivity_days FROM employee_personal_information WHERE employee_username='".$_POST['username']."'";

$result = pg_query($dbconn, $query) or die('Query failed: ' . pg_last_error());

// variable is literally illogically named
$user_query_array = pg_fetch_array($result, null, PGSQL_NUM);

//---LOGIN ATTEMPT CHECK---

// if(pg_query($dbconn, "SELECT password_attempts FROM employee_personal_information WHERE employee_username= $user_query_array") > 3){
//     //Im probably missing something here, haven't written querys in a while
//         pg_query($dbconn, "UPDATE employee_personal_information SET is_activated TO false WHERE employee_username = $user_query_array");
// }

if(password_verify($_POST['password'], $user_query_array[0])) {
    if($user_query_array[2] === "true") {
        // is there a date in the database for inactivity?
        if (pg_field_is_null($result, 0, "requested_inactivity_days") == 0) {
            $string_of_date = $user_query_array[3];
            // php date 
            $start_date = new DateTime($string_of_date);
            $end_date = clone($start_date);

            $total_days_of_inactivity = $user_query_array[4];
            echo ($total_days_of_inactivity . "\n");
            if($total_days_of_inactivity == 1) {
                $interval = DateInterval::createFromDateString('1 day');
                $end_date->add($interval);
            }
            else {
                $interval = DateInterval::createFromDateString($total_days_of_inactivity.' days');
                $end_date->add($interval);
            }

            // $end_date->add(new DateInterval("$total_days_of_inactivity"."d"));
            //$result = $date->format('Y-m-d H:i:s');
            echo($start_date->format('Y-m-d') . "\n");
            echo($end_date->format('Y-m-d') . "\n");
        }
        else {

        // if no, we skip the whole thing and move onto what we added
        
        //sets login attempts to 0
        // pg_query($dbconn, "UPDATE employee_personal_information SET login_attempts TO 0 WHERE employee_username = $user_query_array");
        
        $_SESSION["username"] = $_POST['username'];
        // check this
        if($user_query_array[1] == "Administrator"){
            $_SESSION["admin"] = true;
            header("Location: admin-dashboard.php");
        }
        else {
            header("Location: index.php");
        }    
        }
    }
    else {

        /*
        //increment user login attempts by 1
        pg_query($dbconn, "UPDATE employee_personal_information SET login_attempts TO +1 WHERE employee_username = $user_query_array");
*/
        $Message = urlencode("Account is inactive.");
        header("Location:login.php?Message=".$Message);
    }
}
else {
/*
    //increment user login attemtps by 1
    pg_query($dbconn, "UPDATE employee_personal_information SET login_attempts TO +1 WHERE employee_username = $user_query_array");
*/
    $Message = urlencode("Account details are incorrect.");
    header("Location:login.php?Message=".$Message);    
}

// Free resultset
pg_free_result($result);

// Closing connection
pg_close($dbconn);

?>