<?php 
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['customer_name']) && !empty($_GET['customer_name'])) {
    $_SESSION['selected_customer'] = $_GET['customer_name'];  
    header("Location: customer-hello.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>ZenLedger</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link href="style/nonregisterstyle.css" rel="stylesheet">


<!--I found this pretty modal popup online!-->
        <style>
            
            .modal {
                display: none; 
                position: fixed;
                z-index: 1;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                overflow: auto;
                background-color: rgb(0,0,0); 
                background-color: rgba(0,0,0,0.4); 
            }


            .modal-content {
                background-color: #fefefe;
                margin: 15% auto;
                padding: 20px;
                border: 1px solid #888;
                width: 80%;
                max-width: 300px;
                text-align: center;
            }


            .close {
                color: #aaa;
                float: right;
                font-size: 28px;
                font-weight: bold;
            }

            .close:hover,
            .close:focus {
                color: black;
                text-decoration: none;
                cursor: pointer;
            }
        </style>


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


<?php
$conn = pg_connect("postgresql://zenteamrole:${{ secrets.pgpass }}@ep-raspy-smoke-a5pyv0mk-pooler.us-east-2.aws.neon.tech/zenledgerdb?sslmode=require");

if (!$conn) {
    die("Connection failed: " . pg_last_error());
}

$success_message = "Clarity blooms — a new customer has arrived.";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_customer_name'])) {
  $new_customer = trim($_POST['new_customer_name']);

  if (!empty($new_customer)) {
      $insert_query = "INSERT INTO customer_info (customer_name) VALUES ($1)";
      $result = pg_query_params($conn, $insert_query, [$new_customer]);

      if ($result) {
          header("Location: " . $_SERVER['PHP_SELF'] . "?added=1");
          exit();
      }
  }
}


$customers_result = pg_query($conn, "SELECT customer_name FROM customer_info ORDER BY customer_name");
?>
<div class="hello-block">

<button onclick="document.getElementById('newCustomerForm').style.display='block'" style="margin-top: 20px;">
        + New Customer
    </button>


    <form method="GET" action="">
    <label for="customer_select">Select a customer:</label>
    <select name="customer_name" id="customer_select">
        <?php
        while ($row = pg_fetch_assoc($customers_result)) {
            $name = htmlspecialchars($row['customer_name']);
            if($name == $_SESSION['selected_customer']){
                echo "<option selected=\"true\" value=\"$name\">$name</option>";
            }
            else {
                echo "<option value=\"$name\">$name</option>";
            }
        }
        ?>
    </select>
    <button type="submit">Go</button>
</form>




          </div>


    <form method="POST" id="newCustomerForm" style="display:none; margin-top: 10px;">
        <label for="new_customer_name">Enter name:</label>
        <input type="text" name="new_customer_name" required>
        <button type="submit">Add Customer</button>
    </form>
</div>



    <!--rest of the modal!-->


    <div id="successModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <p><?php echo $success_message; ?></p>
    </div>
</div>

<script>

<script>
    var modal = document.getElementById("successModal");
    var closeBtn = document.getElementsByClassName("close")[0];

    // Check if ?added=1 is in the URL
    if (window.location.search.includes("added=1")) {
        modal.style.display = "block";
    }

    closeBtn.onclick = function() {
        modal.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>




          <?php } ?>
        </main>
        <footer>
    <div class="booties"><a href="help.php" class="help-button">Need help?</a>
    </div>
    </footer>
    </body>
</html>
