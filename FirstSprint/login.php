<!DOCTYPE html>
<html lang="">
  <head>
    <meta charset="utf-8">
    <title>ZenLedger - Login</title>
    </head>
    <body>
        <a href="index.php"><header>ZenLedger</header></a>
        <main>
        <h1> Login </h1>
        <hr>
        <p>
            <?php
                if(isset($_GET['Message'])){
                    echo $_GET['Message'];
            }?>
        </p>
        <form action="login-action.php" method="POST" class="form-example">
            <div class="form-example">
                <label for="username">Username: </label>
                <input type="text" name="username" id="username" required />
            </div>
            <div class="form-example">
                <label for="password">Password: </label>
                <input id="password-input" type="password" name="password" id="password" required />
            </div>
            <div class="form-example">
                <input id="submit-button" type="submit" value="Login" />
            </div>
        </form>
        </main>
        <footer>
            <p><a href="forgot-password.php">Forgot password?</a></p>
            <p><a href="register.php">Don't have an account? Sign up.</a></p>
        </footer>
    </body>
</html>
