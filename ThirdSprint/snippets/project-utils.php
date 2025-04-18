<?php
function printSubentries($pr) {
    $dbconn = pg_connect("postgresql://zenteamrole:npg_I7ZNn1hVqjtA@ep-raspy-smoke-a5pyv0mk-pooler.us-east-2.aws.neon.tech/zenledgerdb?sslmode=require")
        or die('Could not connect: ' . pg_last_error());
    $query = "select (unnest).is_crediting, (unnest).amount, (unnest).account_id from (select unnest(subentries) from journal_entries where post_reference='$pr')";
    $result = pg_query($dbconn, $query);
    while ($row = pg_fetch_row($result, null, PGSQL_ASSOC)) {
        echo "<span>";
        if($row['is_crediting']=="t") {
            echo "CREDIT ";
        }
        else {
            echo "DEBIT ";
        }
        echo '<a href="accounts-details.php?number='.$row['account_id'].'"\">';

        $query = "select account_name from chart_of_accounts where account_id='$row[account_id]'";
        $s_result = pg_query($dbconn, $query) or die('Query failed: ' . pg_last_error());
        $q_row = pg_fetch_row($s_result, null, PGSQL_NUM);
        echo htmlspecialchars($q_row[0])."</a> ";
        echo "$".$row['amount']."</li>";
        echo "</span><br>";
    }
}
function SLOWprintTransactionFromCompositeString($composite_str)
{
        $dbconn = pg_connect("postgresql://zenteamrole:npg_I7ZNn1hVqjtA@ep-raspy-smoke-a5pyv0mk-pooler.us-east-2.aws.neon.tech/zenledgerdb?sslmode=require")
            or die('Could not connect: ' . pg_last_error());
        $raw_string = $composite_str;
        $raw_string = str_replace("\",\"", "+", $raw_string);
        $raw_string = str_replace("\"}", "", $raw_string);
        $raw_string = str_replace("{\"", "", $raw_string);
        $raw_string = str_replace("(", "", $raw_string);
        $raw_string = str_replace(")", "", $raw_string);
        $subentries_string = explode("+", $raw_string);

        $subentries = array();
        foreach($subentries_string as $subentry_string) {
            $subentry = explode(",", $subentry_string);
            $subentries[] = $subentry;
        }
        $is_credit_now = false;

        foreach($subentries as $subentry)
        {
            echo "<span>";
            if($subentry[0]=="t") {
                echo "CREDIT ";
            }
            else {
                echo "DEBIT ";
            }
            echo '<a href="accounts-details.php?number='.$subentry[2].'"\">';

            $query = "select account_name from chart_of_accounts where account_id='$subentry[2]'";
            $result = pg_query($dbconn, $query) or die('Query failed: ' . pg_last_error());
            $row = pg_fetch_row($result, null, PGSQL_NUM);
            echo htmlspecialchars($row[0])."</a> ";
            echo "$".$subentry[1]."</li>";
            echo "</span><br>";
        }

}
?>
