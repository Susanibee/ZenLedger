<?php
session_start();

if (!isset($_SESSION["admin"])) {
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
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>ZenLedger - Edit Personal Info</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="style/nonregisterstyle.css" rel="stylesheet" />
</head>
<body>

        <?php include('snippets/logged-in-top-bar.php'); ?>
    <main>
    <div class="cosmic-container">
    <h1>Edit Personal Info</h1>
    <p class="cosmic-message"><?php echo $cosmic_message; ?></p>
</div>
        <hr>

        <div class="helper">
                  <img src="images/zenledger logo.png" class="background-logo" />
              </div>

        <h2> <?php echo $_POST['username']?> </h2>

        <?php
        $dbconn = pg_connect("postgresql://zenteamrole:npg_I7ZNn1hVqjtA@ep-raspy-smoke-a5pyv0mk-pooler.us-east-2.aws.neon.tech/zenledgerdb?sslmode=require")
        or die('Could not connect: ' . pg_last_error());

        // SQL query to read columns
        $query = "select employee_street_address, employee_secondary_address, employee_city, employee_state, employee_zip_code, employee_phone from employee_personal_information where employee_username = '".$_POST["username"]."';";
        $result = pg_query($dbconn, $query) or die('Query failed: ' . pg_last_error());
        $info = pg_fetch_array($result, null, PGSQL_NUM);
        ?>
        <form method="POST" action="action-update-personal-info.php">
            <input type="hidden" name="username" value="<?php echo htmlspecialchars($_POST["username"]) ?>">

            <label for="street">Street Address:</label>
            <input pattern="[a-zA-Z0-9 ]+" type="text" name="street"
            value="<?php echo htmlspecialchars($info[0]);?>"
            required
            >
            <br>
            <label for="secondary">Secondary Address:</label>
            <input pattern="[a-zA-Z0-9 ]+" type="text" name="secondary"
            value="<?php echo htmlspecialchars($info[1]);?>"
            >
            <br> <label for="city">City:</label>
            <input pattern="[a-zA-Z0-9 ]+" type="text" name="city"
            value="<?php echo htmlspecialchars($info[2]);?>"
            required
            >
            <br>
            <label for="state">State:</label>
            <input pattern="[a-zA-Z0-9 ]+" type="text" name="state"
            value="<?php echo htmlspecialchars($info[3]);?>"
            required
            >
            <br>
            <label for="zip">Zip Code:</label>
            <input type="number" name="zip"
            value="<?php echo htmlspecialchars($info[4]);?>"
            >
            <br>
            <label for="phone">Phone Number:</label>
            <input type="number" name="phone"
            value="<?php echo htmlspecialchars($info[5]);?>"
            >
            <br>

            <input type="submit" name="update" value="Update">
        </form>

        <a href="admin-dashboard.php"> <button class="help-button" title="Return to Dashboard">Return to Dashboard</button> </a>
    </main>
    <div class="booties"><a href="help.php" class="help-button">Need help?</a>
    </div>
    <script src="snippets/calendar.js"></script> 
    </footer>
</body>
</html>

