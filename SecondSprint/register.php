<?php session_start();?>
<!DOCTYPE html>
<html>
<head>
    <title>ZenLedger - Register</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta charset="utf-8">
    <link href="style/nonregisterstyle.css" rel="stylesheet">
</head>
<body>
    <?php include('snippets/guest-top-bar.php'); ?>
    <div class="container">
        <div class="form-box">
            <h1 id="title">Register</h1>
            <hr>
            <form method="POST" action="action-register-user.php"> 
                <div class="input-group">
                    <div class="input-field" id="first">
                        <label for="first">First name: </label>
                        <input minlength="2" pattern="[a-zA-Z]+" 
                        type="text" name="first" required>
                    </div>
                    <div class="input-field" id="last">
                        <label for="last">Last name: </label>
                        <input minlength="2" pattern="[a-zA-Z]+"
                        type="text" name="last" required>
                    </div>
                    <div class="input-field" minlength="6" id="street">
                        <label for="street">Street address: </label>
                        <input type="text" name="street" required>
                    </div>
                    <div class="input-field" minlength="6" id="secondary">
                        <label for="secondary">Secondary address: </label>
                        <input type="text" name="secondary">
                    </div>
                    <div class="input-field" minlength="6" id="city">
                        <label for="city">City: </label>
                        <input type="text" name="city" required>
                    </div>
                    <div class="input-field" minlength="6" id="state">
                        <label for="state">State: </label>
                        <input type="text" name="state" required>
                    </div>
                    <div class="input-field" id="zip">
                        <label for="zip">Zip code: </label>
                        <input type="number" name="zip" required>
                    </div>
                    <div class="input-field" id="DOB">
                        <label for="dob">Date of birth: </label>
                        <input type="date" name="dob" required>
                    </div>
                    <div class="input-field" id="email">
                        <label for="email">Email: </label>
                        <input type="email" name="email" required>
                    </div>
                    <div class="input-field hidden" id="password">
                        <label for="password" >Password: </label>
                        <input type="password" name="password" 
                        pattern="^(?=.*[A-Za-z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z].{7,}$"
                        minlength="8" required>
                    </div>
                </div>
                    <input type="submit" class="submit-button" value="Register">
                </form>
                <p><a href="login.php">Already have an account? Login.</a></p>
        </div>
    </div>
    <div class="booties"><a href="help.php" class="help-button">Need help?</a>
    </div>
    <script src="snippets/calendar.js"></script> 
    </footer>
</body>
</html>
