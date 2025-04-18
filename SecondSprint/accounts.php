<?php session_start();
if(isset($_SESSION["username"]))
{
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
        <h1> Accounts </h1>
        <hr>
        <?php
        if(isset($_SESSION["admin"]))
        {
        ?>
            <a href="accounts-add.php"  ><span class="tab-header">Add</span></a>
            <a href="accounts-deactivate.php"><span class="tab-header">Deactivate</span></a>
            <a href="accounts-edit.php"><span class="tab-header">Edit</span></a>
        <?php
        }
        else {
        echo("<script>window.top.location='/accounts-view.php'</script>");
        } ?>
        <a href="accounts-view.php"><span class="tab-header">View</span></a>
        </main>
        <footer>
    <div class="booties"><a href="help.php" class="help-button">Need help?</a>
    </div>
    <script src="snippets/calendar.js"></script> 
    </footer>
    </body>
</html>
<?php
}
else {
    echo("Error: Unauthorized Page");
}
?>
