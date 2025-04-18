<?php session_start();
    $data = json_decode(file_get_contents('php://input'), true);

    $form_date = $data['date_of_transaction'];
    $date_of_transaction = date($form_date);
    $description = pg_escape_string($data['description']);
    $comments = pg_escape_string($data['comments']);
    $customer = pg_escape_string($_SESSION['selected_customer']);
    $time = date("Y-m-d H:i:s");


    $dbconn = pg_connect("postgresql://zenteamrole:npg_I7ZNn1hVqjtA@ep-raspy-smoke-a5pyv0mk-pooler.us-east-2.aws.neon.tech/zenledgerdb?sslmode=require")
    or die('Could not connect: ' . pg_last_error());

    $is_approved = "FALSE";
    $is_app_b = false;
    if(isset($_SESSION["manager"])){
        $is_approved = "TRUE";
        $is_app_b = true;
    }

    $query = "INSERT INTO journal_entries (date, description, comments, is_approved, customer_name) values ('".$date_of_transaction."', '".$description."', '".$comments."', '$is_approved', '$customer') returning post_reference";
    $result = pg_query($dbconn, $query) or die('Query failed: ' . pg_last_error());
    $post_reference = pg_fetch_array($result, 0, PGSQL_NUM)[0];

    $debit_accounts = $data['debit_accounts'];
    $debit_values = $data['debit_values'];
    $debit_loop_size = count($debit_accounts);
    for($i = 0; $i < $debit_loop_size; $i++)
    {
        if($is_app_b) {
            $query_update_account = "update chart_of_accounts set total_debit = total_debit + ".$debit_values[$i].", total_balance = total_balance - ".$debit_values[$i];
            $query_update_account .= " where account_id=".$debit_accounts[$i]." returning total_balance";
            $result_update_account = pg_query($dbconn, $query_update_account) or die('Query failed: ' . pg_last_error());
            $balance = pg_fetch_row($result_update_account, NULL, PGSQL_NUM)[0];
        }

        $query_add_transactions = "update journal_entries set subentries = subentries || '{\"";
        $query_add_transactions .= "(0, ".$debit_values[$i].", ".$debit_accounts[$i];
        if($is_app_b){
            $query_add_transactions .= ", ".$balance;
        }
        else{
            $query_add_transactions .= ",";
        }
        $query_add_transactions .= ")";
        $query_add_transactions .= "\"}' where post_reference='".$post_reference."'";
        error_log($query_add_transactions);
        $result_add_transactions = pg_query($dbconn, $query_add_transactions) or die('Query failed: ' . pg_last_error());
    }

    $credit_accounts = $data['credit_accounts'];
    $credit_values = $data['credit_values'];
    $credit_loop_size = count($credit_accounts);
    for($i = 0; $i < $credit_loop_size; $i++)
    {
        if($is_app_b) {
            $query_update_account = "update chart_of_accounts set total_credit = total_credit + ".$credit_values[$i].", total_balance = total_balance + ".$credit_values[$i];
            $query_update_account .= " where account_id=".$credit_accounts[$i]." returning total_balance";
            $result_update_account = pg_query($dbconn, $query_update_account) or die('Query failed: ' . pg_last_error());
            $balance = pg_fetch_row($result_update_account, NULL, PGSQL_NUM)[0];
        }

        $query_add_transactions = "update journal_entries set subentries = subentries || '{\"";
        $query_add_transactions .= "(1, ".$credit_values[$i].", ".$credit_accounts[$i];
        if($is_app_b){
            $query_add_transactions .= ", ".$balance;
        }
        else{
            $query_add_transactions .= ",";
        }
        $query_add_transactions .= ")";
        $query_add_transactions .= "\"}' where post_reference='".$post_reference."' returning subentries";
        $result_add_transactions = pg_query($dbconn, $query_add_transactions) or die('Query failed: ' . pg_last_error());
    }
    $subentries_arr = pg_fetch_row($result_add_transactions,null,PGSQL_NUM);
    $subentries = pg_escape_string($subentries_arr[0]);
    $query_changelog = "insert into journal_entries_changelog (post_reference, date_after, customer_name, time_stamp,"
                          ."description_after, comments_after, is_approved_after, journal_subentry_after)"
                      ."values ('$post_reference', '$date_of_transaction', '$customer', '$time',"
                          ."'$description', '$comments', '$is_approved', '$subentries')";
    $result_changelog = pg_query($dbconn, $query_changelog) or die('Query failed: ' . pg_last_error());
    
    echo json_encode($post_reference);
?>
