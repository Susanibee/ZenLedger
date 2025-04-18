<?php
function echoChangelogAfterImage($changelog)
{
    echo ("<ul>");
    echo ("<li> <strong>Name:</strong> $changelog[after_account_name]</li>");
    echo ("<li> <strong>Description:</strong> $changelog[after_account_description]</li>");
    echo ("<li> <strong>Category:</strong> $changelog[after_account_category]</li>");
    echo ("<li> <strong>Subcategory:</strong> $changelog[after_account_subcategory]</li>");
    echo ("<li> <strong>Initial Balance:</strong> \$$changelog[after_initial_balance]</li>");
    echo ("<li> <strong>Balance:</strong> \$$changelog[after_balance]</li>");
    echo ("<li> <strong>Credit:</strong> \$$changelog[after_credit]</li>");
    echo ("<li> <strong>Debit:</strong> \$$changelog[after_debit]</li>");
    echo ("<li> <strong>Order:</strong> $changelog[after_order]</li>");
    echo ("<li> <strong>Statement:</strong> $changelog[after_statement]</li>");
    echo ("<li> <strong>Post Reference:</strong> $changelog[after_post_reference]</li>");
    echo ("<li> <strong>Comment:</strong> $changelog[after_comments]</li>");
    echo ("<li> <strong>Is Activated: </strong> $changelog[after_is_activated]</li>");
    echo ("<li> <strong>Add Date:</strong> $changelog[after_account_add_date]</li>");
    echo ("<li> <strong>Add Time:</strong> $changelog[after_account_add_time]</li>");
    echo ("</ul>");
}

function echoChangelogBeforeImage($changelog)
{
    echo ("<ul>");
    echo ("<li> <strong>Name:</strong> $changelog[before_account_name]</li>");
    echo ("<li> <strong>Description:</strong> $changelog[before_account_description]</li>");
    echo ("<li> <strong>Category:</strong> $changelog[before_account_category]</li>");
    echo ("<li> <strong>Subcategory:</strong> $changelog[before_account_subcategory]</li>");
    echo ("<li> <strong>Initial Balance:</strong> \$$changelog[before_initial_balance]</li>");
    echo ("<li> <strong>Balance:</strong> \$$changelog[before_balance]</li>");
    echo ("<li> <strong>Credit:</strong> \$$changelog[before_credit]</li>");
    echo ("<li> <strong>Debit:</strong> \$$changelog[before_debit]</li>");
    echo ("<li> <strong>Order:</strong> $changelog[before_order]</li>");
    echo ("<li> <strong>Statement:</strong> $changelog[before_statement]</li>");
    echo ("<li> <strong>Post Reference:</strong> $changelog[before_post_reference]</li>");
    echo ("<li> <strong>Comment:</strong> $changelog[before_comments]</li>");
    echo ("<li> <strong>Is Activated: </strong> $changelog[before_is_activated]</li>");
    echo ("<li> <strong>Add Date:</strong> $changelog[before_account_add_date]</li>");
    echo ("<li> <strong>Add Time:</strong> $changelog[before_account_add_time]</li>");
    echo ("</ul>");
}

function addToAccountChangelog($dbconn, $number, $username, $old_image, $new_image)
{
    $change_time = date("Y-m-d H:i:s");
    $change_date = date("Y/m/d");
    $query = "";
    // is this a new account?
    if(is_null($old_image)) {
        $query = "insert into chart_of_accounts_changelog "
                    ."(change_time, change_date, user_id, account_number,"
                    ." before_credit, before_debit, before_initial_balance, before_balance, before_account_add_date, before_account_add_time," 
                    ." before_order, before_statement, before_account_name, before_account_description, before_account_category, before_account_subcategory,"
                    ." before_comments, before_post_reference, before_is_activated," 
                    ." after_credit, after_debit, after_initial_balance, after_balance, after_account_add_date, after_account_add_time," 
                    ." after_order, after_statement, after_account_name, after_account_description, after_account_category, after_account_subcategory,"
                    ." after_comments, after_post_reference, after_is_activated)" 
                    ." values "
                    ."('$change_time', '$change_date', '$username', $number,"
                    ." null, null, null, null, null, null," 
                    ." null, null, null, null, null, null,"
                    ." null, null, null," 
                    ." $new_image[credit], $new_image[debit], $new_image[initial_balance], $new_image[balance], '$new_image[account_add_date]', '$new_image[account_add_time]'," 
                    ." $new_image[order], '$new_image[statement]', '$new_image[account_name]', '$new_image[account_description]', '$new_image[account_category]', '$new_image[account_subcategory]',"
                    ." '$new_image[comments]', '$new_image[post_reference]', '$new_image[is_activated]')" 
                    ." returning *;";
    }
    // updating account?
    else {
        $query = "insert into chart_of_accounts_changelog "
                    ."(change_time, change_date, user_id, account_number,"
                    ." before_credit, before_debit, before_initial_balance, before_balance, before_account_add_date, before_account_add_time," 
                    ." before_order, before_statement, before_account_name, before_account_description, before_account_category, before_account_subcategory,"
                    ." before_comments, before_post_reference, before_is_activated," 
                    ." after_credit, after_debit, after_initial_balance, after_balance, after_account_add_date, after_account_add_time," 
                    ." after_order, after_statement, after_account_name, after_account_description, after_account_category, after_account_subcategory,"
                    ." after_comments, after_post_reference, after_is_activated)" 
                    ." values "
                    ."('$change_time', '$change_date', '$username', $number,"
                    ." $old_image[credit], $old_image[debit], $old_image[initial_balance], $old_image[balance], '$old_image[account_add_date]', '$old_image[account_add_time]'," 
                    ." $old_image[order], '$old_image[statement]', '$old_image[account_name]', '$old_image[account_description]', '$old_image[account_category]', '$old_image[account_subcategory]',"
                    ." '$old_image[comments]', '$old_image[post_reference]', '$old_image[is_activated]'," 
                    ." $new_image[credit], $new_image[debit], $new_image[initial_balance], $new_image[balance], '$new_image[account_add_date]', '$new_image[account_add_time]'," 
                    ." $new_image[order], '$new_image[statement]', '$new_image[account_name]', '$new_image[account_description]', '$new_image[account_category]', '$new_image[account_subcategory]',"
                    ." '$new_image[comments]', '$new_image[post_reference]', '$new_image[is_activated]')" 
                    ." returning *;";
    }
    $result = pg_query($dbconn, $query) or die('Query failed: ' . pg_last_error());

    $result_arr = pg_fetch_array($result, 0, PGSQL_ASSOC);
    return $result_arr;
}
?>
