<?php
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta charset="utf-8">
    <link href="style/nonregisterstyle.css" rel="stylesheet">
    <title>ZenLedger - Recover Forgotten Password</title>
</head>
<body>

    <div class="container">
        <?php include('snippets/guest-top-bar.php'); ?>
        <div class="form-box">
            <h1 id="title">Recover Forgotten Password</h1>
            <hr>
            <p>
            <?php
                if(isset($_GET['Message'])){
                    echo $_GET['Message'];
            }?>
            </p>
            <form method="POST" action="action-update-password.php"> 
                <div class="input-group">
                    <div class="input-field" id="last">
                        <label for="last">Last name: </label>
                        <input minlength="2" pattern="[a-zA-Z]+"
                        type="text" name="last" required>
                    </div>
                    <div class="input-field" id="zip">
                        <label for="zip">Zip code: </label>
                        <input type="number" name="zip" required>
                    </div>
                    <div class="input-field" id="email">
                        <label for="email">Email: </label>
                        <input type="email" name="email" required>
                    </div>
                    <div class="input-field hidden" id="password">
                        <label for="password" >New Password: </label>
                        <input type="password" name="password" 
                        pattern="^(?=.*[A-Za-z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z].{7,}$"
                        minlength="8" required>
                    </div>
                </div>
                    <input type="submit" class="submit-button" value="Recover">
                </form>
                <p><a href="login.php">Already have an account? Login.</a></p>
                <div class="booties"><a href="help.php" class="help-button">Need help?</a>
    </div>
    <script src="snippets/calendar.js"></script> 
    </footer>
        </div>
    </div>

<script>

</script>
</body>
</html>
