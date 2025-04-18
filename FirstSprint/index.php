<?php session_start(); ?>
<!DOCTYPE html>
<html lang="">
    <head>
        <meta charset="utf-8">
        <title>ZenLedger</title>
    </head>
    <body>
        <header style="display: flex;flex-direction:row;">
             <a href="index.php">
            <p style="flex: 1;">ZenLedger</p></a>
            <?php if(isset($_SESSION['username'])) {?>
                <div style="flex: 1;text-align:right;">
                    <img src="https://placehold.co/40x40.png" />
                    <?php echo $_SESSION['username']?>
            <?php }?>
        </header>
        <main>
        <h1> Balance your books. Find your calm. </h1>
        <hr>
        <p>
            <?php
                if(isset($_GET['Message'])){
                    echo $_GET['Message'];
            }?>
        </p>
        <p>
            Experience what happens when mindfulness meets financial management
        </p>
        <?php 
        if(!isset($_SESSION["username"])) {
        ?>
        <button onclick="window.location.href='login.php';">
        Login
        </button>
        <?php }
        else {?>
        <form action="logouttest.php"><input value="Log out" type="submit"></form>
        <?php }?>
        </main>
        <footer></footer>
    </body>
</html>
