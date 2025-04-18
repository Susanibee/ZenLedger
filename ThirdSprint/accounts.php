<?php
session_start();


if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}


if (empty($_SESSION['selected_customer'])) {
    header("Location: index.php");
    exit();
}
include("snippets/cosmic-message.php");
?>

<!DOCTYPE html>
<html lang="">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="style/nonregisterstyle.css" rel="stylesheet" />
    <title>ZenLedger - Accounts</title>
    <style>
        [data-tab-info] { display: none; }
        .active[data-tab-info] { display: block; }
        .tab-header { cursor:pointer; margin: 4em; }
    </style>
  </head>
  <body>
    <main>
        <?php include('snippets/logged-in-top-bar.php'); ?>
        <div class="cosmic-container">
    <h1>Accounts</h1>
    <p class="cosmic-message"><?php echo $cosmic_message; ?></p>
</div>
        <hr>

        <div class="helper">
                  <img src="images/zenledger logo.png" class="background-logo" />
              </div>

        <div class="butterdish">
        <?php
        if(isset($_SESSION["admin"]))
        {
        ?>
            <a href="accounts-add.php"  class="journal-button">Add</a>
            <a href="accounts-deactivate.php" class="journal-button">Deactivate</a>
            <a href="accounts-edit.php" class="journal-button">Edit</a>
            <a href="accounts-changelog.php" class="journal-button">Changelog</a>
        <?php
        }
        else {
        echo("<script>window.top.location='/accounts-view.php'</script>");
        } ?>
        <a href="accounts-view.php" class="journal-button">View</a>
    </div>

    
        </main>
        <div class="booties"><a href="help.php" class="help-button">Need help?</a>
    </div>
    <script src="snippets/calendar.js"></script> 
    </footer>
    </body>
</html>
<?php
