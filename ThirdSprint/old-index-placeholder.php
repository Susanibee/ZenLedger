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
                <div style="width:100%; display: flex; align-items: center; justify-content: center; ">
                    <a href="register.php">
                    <button  style="margin:0;" onclick="window.location.href='register.php';"
                      class="sign-up-button" >
                      Register
                    </button>
                    </a>
              </div>
          <?php } else { ?>
              <?php include('snippets/logged-in-top-bar.php'); ?>
              <div class="helper">
                  <img src="images/zenledger logo.png" class="background-logo" />
              </div>
              <h1 class="ad-dash-screen">take a breath</h1>
            <hr>
          <?php } ?>
        </main>
        <footer>
    <div class="booties"><a href="help.php" class="help-button">Need help?</a>
    </div>
    </footer>
    </body>
</html>