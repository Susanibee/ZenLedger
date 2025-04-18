<?php session_start();
if(isset($_SESSION["admin"])) {

$dbconn = pg_connect("postgresql://zenteamrole:npg_jqtelOpA2V6s@ep-raspy-smoke-a5pyv0mk-pooler.us-east-2.aws.neon.tech/zenledgerdb?sslmode=require")
or die('Could not connect: ' . pg_last_error());

// SQL query to read columns
$query = "select employee_street_address, employee_secondary_address, employee_city, employee_state, employee_zip_code, employee_phone from employee_personal_information where employee_username = '".$_POST["username"]."';";
$result = pg_query($dbconn, $query) or die('Query failed: ' . pg_last_error());
$info = pg_fetch_array($result, null, PGSQL_NUM);
?>
<form method="POST" action="update-info.php">
    <input type="hidden" name="username" value="<?php echo htmlspecialchars($_POST["username"]) ?>">

    <label for="street">Street Address:</label>
    <input type="text" name="street"
    value="<?php echo htmlspecialchars($info[0]);?>"
    >
    <br>
    <label for="secondary">Secondary Address:</label>
    <input type="text" name="secondary"
    value="<?php echo htmlspecialchars($info[1]);?>"
    >
    <br>
    <label for="city">City:</label>
    <input type="text" name="city"
    value="<?php echo htmlspecialchars($info[2]);?>"
    >
    <br>
    <label for="state">State:</label>
    <input type="text" name="state"
    value="<?php echo htmlspecialchars($info[3]);?>"
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

<?php }
else {
    echo "Unauthorized Page";
}
?>
