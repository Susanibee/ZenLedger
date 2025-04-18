<!--This is the original code I copied back in for now. Will return tomorrow to fix the table.-->

<?php
session_start();

if (isset($_SESSION["username"])) {
    include("snippets/project-utils.php");

    if (empty($_SESSION['selected_customer'])) {
        header("Location: index.php");
        exit();
    }
    
} 
include("snippets/cosmic-message.php");

?>

<!DOCTYPE html>
<html lang="">
    <head>
        <meta charset="utf-8">
        <link href="style/nonregisterstyle.css" rel="stylesheet" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>ZenLedger - Journal View</title>
    </head>
    <body>
        <main>
        <?php include('snippets/logged-in-top-bar.php'); ?>
        <div class="cosmic-container">
    <h1>Journal</h1>
    <p class="cosmic-message"><?php echo $cosmic_message; ?></p>
</div>
        <hr>
        <!--
        <div class="helper">
                  <img src="images/zenledger logo.png" class="background-logo" />
              </div>
         -->

        <?php include("snippets/journal-tab-bar.php"); ?>

        <!-- Status filter, date range, and search form -->
        <form method="GET" style="margin-bottom: 20px;">
            <label>Show:</label>
            <select name="status">
                <option value="approved" <?php echo (!isset($_GET['status']) || $_GET['status'] === 'approved') ? 'selected' : ''; ?>>Approved</option>
                <option value="pending" <?php echo ($_GET['status'] === 'pending') ? 'selected' : ''; ?>>Pending</option>
            </select>
            <label>Date Range:</label>
            <input type="date" name="start_date" value="<?php echo $_GET['start_date'] ?? ''; ?>">
            <input type="date" name="end_date" value="<?php echo $_GET['end_date'] ?? ''; ?>">
            <label>Search:</label>
            <input type="text" name="search" value="<?php echo $_GET['search'] ?? ''; ?>" placeholder="Post Reference, Amount, Date">
            <input type="submit" value="Apply">
        </form>

        <br><span style="font-size: 0.8em;">* All accounts viewable are approved. To manage accounts with other statuses, go to the 'Approve' tab. </span>

        <table style="margin-top: 20px; width: 100%; border-collapse: collapse;">
            <tr style="border-bottom: 1px solid #000;">
                <th>Post Reference</th>
                <th>Date</th>
                <th>Subentries</th>
            </tr>
            <?php 
            $dbconn = pg_connect("postgresql://zenteamrole:${{ secrets.pgpass }}@ep-raspy-smoke-a5pyv0mk-pooler.us-east-2.aws.neon.tech/zenledgerdb?sslmode=require")
                or die('Could not connect: ' . pg_last_error());

            $query = "SELECT post_reference, date, subentries, is_approved FROM journal_entries";
            $conditions = [];

            if (isset($_GET['status'])) {
                if ($_GET['status'] === 'approved') {
                    $conditions[] = "is_approved = true";
                } elseif ($_GET['status'] === 'pending') {
                    $conditions[] = "is_approved = false";
                    $conditions[] = "COALESCE (is_rejected, false) = false";
                }
            } else {
                $conditions[] = "is_approved = true"; 
            }

            if (!empty($_GET['start_date'])) {
                $start_date = pg_escape_string($_GET['start_date']);
                $conditions[] = "date >= '$start_date'";
            }
            if (!empty($_GET['end_date'])) {
                $end_date = pg_escape_string($_GET['end_date']);
                $conditions[] = "date <= '$end_date'";
            }

            if (!empty($_GET['search'])) {
                $search = pg_escape_string($_GET['search']);
                $conditions[] = "(post_reference ILIKE '%$search%' OR 
                    TO_CHAR(date, 'YYYY-MM-DD') ILIKE '%$search%' OR 
                    EXISTS (
                        SELECT 1 
                        FROM unnest(subentries) AS s 
                        WHERE CAST(s.amount AS text) ILIKE '%$search%'
                    )
                )";
            }

            $customer = $_SESSION['selected_customer'];
            $query .= " WHERE customer_name='$customer'";
            if (!empty($conditions)) {
                $query .= ' AND ' .implode(" AND ", $conditions);
            }

            $query .= " ORDER BY post_reference COLLATE \"numeric\" desc;";

            $result = pg_query($dbconn, $query) or die('Query failed: ' . pg_last_error());

            while ($row = pg_fetch_row($result, null, PGSQL_NUM)) {
                ?>
                <tr id="<?php echo htmlspecialchars($row[0])?>" style="border-bottom: 1px solid #ddd; line-height: 3em;">
                    <td style="text-align: center;"> <?php echo htmlspecialchars($row[0]) ?> </td>
                    <td style="text-align: center;"><?php echo htmlspecialchars($row[1]) ?></td>
                    <td style="text-align: center;">
                    <?php
                        printSubentries($row[0]);
                ?> </td>
                </tr>
            <?php }

            pg_close($dbconn);
            ?>
        </table>
        </main>

        <div class="booties"><a href="help.php" class="help-button">Need help?</a></div>
    </body>
</html>

