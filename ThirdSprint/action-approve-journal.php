<?php
session_start();
if (!isset($_SESSION["manager"])) {
    exit("Unauthorized");
}
$dbconn = pg_connect("postgresql://zenteamrole:npg_I7ZNn1hVqjtA@ep-raspy-smoke-a5pyv0mk-pooler.us-east-2.aws.neon.tech/zenledgerdb?sslmode=require") or die('Could not connect: ' . pg_last_error());

$post_ref = $_POST['post_reference'];
$action = $_POST['action'];
$comments = pg_escape_string($_POST['comments']);

if ($action === 'reject' && empty($comments)) {
    echo "<p style='color: red;'>Rejection requires a comment.</p>";
    exit;
}

$query_old = "select * from journal_entries where post_reference = '$post_ref'";
$result_old = pg_query($dbconn, $query_old);
$arr_old = pg_fetch_row($result_old, null, PGSQL_ASSOC);

$is_approved = $action === 'approve' ? 'TRUE' : 'FALSE';
$query = "UPDATE journal_entries SET is_approved = $is_approved, is_rejected = $is_approved, reject_comment = '$comments' WHERE post_reference = '$post_ref'";
pg_query($dbconn, $query);
if($is_approved == 'FALSE') {
    $failed_query = "UPDATE journal_entries SET is_rejected = TRUE WHERE post_reference = '$post_ref'";
    pg_query($dbconn, $failed_query);
}

if ($action === 'approve') {
    $query = "select (unnest).is_crediting, (unnest).account_id, (unnest).amount from (SELECT unnest(subentries), post_reference FROM journal_entries) WHERE post_reference = '$post_ref'";
    $result = pg_query($dbconn, $query);
    $sub_i = 1;
    while ($subentry = pg_fetch_row($result, NULL, PGSQL_NUM)) {
        $type = $subentry[0] == 'f' ? 'debit' : 'credit';
        $acc = $subentry[1];
        $val = $subentry[2];
        if ($type == 'debit') {
            $bal_result = pg_query($dbconn, "UPDATE chart_of_accounts SET total_debit = total_debit + $val, total_balance = total_balance - $val WHERE account_id = $acc returning total_balance");
        } else {
            $bal_result = pg_query($dbconn, "UPDATE chart_of_accounts SET total_credit = total_credit + $val, total_balance = total_balance + $val WHERE account_id = $acc returning total_balance");
        }
        $balance = pg_fetch_row($bal_result, NULL, PGSQL_NUM)[0];
        error_log($balance);
        $update_type = '0';
        if($type=='credit'){
            $update_type = '1';
        }
        $journal_update_query = "UPDATE journal_entries SET subentries[".$sub_i."]= '(".$update_type.", ".$val.", ".$acc.", ".$balance.")' WHERE post_reference='".$post_ref."' returning *";
        error_log($journal_update_query);
        $journal_result = pg_query($dbconn, $journal_update_query);

        $sub_i = $sub_i + 1;
    }
}


$customer = pg_escape_string($_SESSION['selected_customer']);
$time = date("Y-m-d H:i:s");

$query_new= "select * from journal_entries where post_reference = '$post_ref'";
$result_new = pg_query($dbconn, $query_new);
$arr_new = pg_fetch_row($result_new, null, PGSQL_ASSOC);

$desc_new = pg_escape_string($arr_new['description']);
$comm_new = pg_escape_string($arr_new['comments']);
$isap_new = pg_escape_string($arr_new['is_approved']);
$date_new = pg_escape_string($arr_new['date']);
$sube_new = pg_escape_string($arr_new['subentries']);
$reje_new = pg_escape_string($arr_new['is_rejected']);
$rejc_new = pg_escape_string($arr_new['reject_comment']);

$desc_old = pg_escape_string($arr_old['description']);
$comm_old = pg_escape_string($arr_old['comments']);
$isap_old = pg_escape_string($arr_old['is_approved']);
$date_old = pg_escape_string($arr_old['date']);
$sube_old = pg_escape_string($arr_old['subentries']);
$reje_old = pg_escape_string($arr_old['is_rejected']);
$rejc_old = pg_escape_string($arr_old['reject_comment']);

if(pg_field_is_null($result_old, 0, 'is_rejected')) {
    $reje_old = 'f';
}
if(pg_field_is_null($result_new, 0, 'is_rejected')) {
    $reje_new = 'f';
}

$query_changelog = "insert into journal_entries_changelog (post_reference, customer_name, time_stamp,"
                      ."description_before, comments_before, is_approved_before, date_before, journal_subentry_before,"
                      ."is_rejected_before, rejected_comment_before,"
                      ."description_after, comments_after, is_approved_after, date_after, journal_subentry_after,"
                      ."is_rejected_after, rejected_comment_after)"
                  ."values ('$post_ref', '$customer', '$time',"
                      ."'$desc_old', '$comm_old', '$isap_old', '$date_old', '$sube_old', '$reje_old', '$rejc_old', "
                      ."'$desc_new', '$comm_new', '$isap_new', '$date_new', '$sube_new', '$reje_new', '$rejc_new')";

error_log($query_changelog);

$result_changelog = pg_query($dbconn, $query_changelog) or die('Query failed: ' . pg_last_error());

header("Location: journal-approve.php");
?>
