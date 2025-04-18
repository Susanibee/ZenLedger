<?php session_start();  ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>ZenLedger</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link href="style/nonregisterstyle.css" rel="stylesheet">
    </head>
    <body>
        <main>
            <?php if(!isset($_SESSION['username'])) {?>
                <?php include('snippets/guest-top-bar.php'); ?>
                <p>
                    <?php
                        if(isset($_GET['Message'])){
                            echo $_GET['Message'];
                    }?>
                </p>
                <div class="table-wrapper">
                <div class="table">
                    <div class="tagline1">
                      Balance your books. <br />Find your calm. <br class="front-space" / >
                    
                    <span class="tagline2">
                      Experience what happens when mindfulness meets financial management.
                      
                    </div>

                    <div class="stickleaf">
                      <img src="images/stickleaf.png" alt="stickleaf" class="stickleaf" />
                    </div>

                </div>
            </div>
            <div class="regi-box">
                <a href="register.php" class="sign-up-button">Register
                </a>
          </div>


          <?php } else { ?>
              <?php include('snippets/logged-in-top-bar.php'); ?>
              <div class="helper">
                  <img src="images/zenledger logo.png" class="background-logo" />
              </div>
          <?php } ?>
        </main>
    <footer>
    <div class="booties"><a href="help.php" class="help-button">Need help?</a>
    </div>
    <script src="snippets/calendar.js"></script> 
    </footer>
    </body>
</html>
