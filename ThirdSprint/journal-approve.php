<?php
session_start();

if (!isset($_SESSION["admin"]) && !isset($_SESSION["username"])) {
    echo "Unauthorized Page";
    exit;
}

if (empty($_SESSION["selected_customer"])) {
    header("Location: index.php"); 
    exit;
}
include("snippets/cosmic-message.php");

$dbconn = pg_connect("postgresql://zenteamrole:npg_I7ZNn1hVqjtA@ep-raspy-smoke-a5pyv0mk-pooler.us-east-2.aws.neon.tech/zenledgerdb?sslmode=require") or die('Could not connect: ' . pg_last_error());
include("snippets/project-utils.php");
?>


<!DOCTYPE html>
<html>
<head>
    <title>ZenLedger - Approve Journal Entries</title>
    <link href="style/nonregisterstyle.css" rel="stylesheet" />
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>
<body>
    <main>
        <?php include('snippets/logged-in-top-bar.php'); ?>
        <div class="cosmic-container">
    <h1>Approve Journal Entries</h1>
    <p class="cosmic-message"><?php echo $cosmic_message; ?></p>
</div>
        <hr>
        <div class="helper">
                  <img src="images/zenledger logo.png" class="background-logo" />
              </div>
        <?php include("snippets/journal-tab-bar.php"); ?>
        <table style="margin-top: 1em; width:100%">
            <tr><th>PR</th><th>Date</th><th>Subentries</th><th>Action</th><th>Status</th></tr>
            <?php
            $customer = $_SESSION['selected_customer'];
            $query = "SELECT post_reference, date, subentries, is_rejected, reject_comment FROM journal_entries WHERE customer_name='$customer' AND is_approved = FALSE ";
            $query .= " ORDER BY post_reference COLLATE \"numeric\" desc;";
            $result = pg_query($dbconn, $query) or die('Query failed: ' . pg_last_error());
            $row_i = 0;
            while ($row = pg_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>{$row['post_reference']}</td>";
                echo "<td>{$row['date']}</td>";
                echo "<td>";
                printSubentries($row['post_reference']);
                echo "</td>";
                echo "<td style=\"text-align:center; display:block;\">";
                echo "<button name='action' onclick=\"window.location.href='journal-entry-edit.php?number=$row[post_reference]';\">Edit</button>";
                echo "<form action='action-approve-journal.php' method='POST'>";
                echo "<input type='hidden' name='post_reference' value='{$row['post_reference']}'>";
                echo "<button name='action' value='approve'>Approve</button>";
                echo "<button name='action' value='reject'>Reject</button>";
                echo "<textarea name='comments' placeholder='Rejection reason'>";
                if($row['is_rejected']) {
                    echo htmlspecialchars($row['reject_comment']);
                }
                echo "</textarea>";
                echo "</form>";
                echo "</td>";
                echo "<td>";
                if(pg_field_is_null($result, $row_i, 'is_rejected') == 1 || !$row['is_rejected'])
                {
                    echo "Unapproved";
                }
                else
                {
                    echo "Rejected";
                }
                echo "</td>";
                echo "</tr>";
                $row_i = $row_i + 1;
            }
            ?>
        </table>
    </main>
    <div class="booties"><a href="help.php" class="help-button">Need help?</a>
    </div>
    </footer>
</body>
</html>
