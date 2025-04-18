<?php session_start();
    if(isset($_SESSION["admin"])) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <title>ZenLedger - Admin Dashboard</title>
    </head>
    <body>
        <a href="index.php"><header>ZenLedger</header></a>
        <main>
            <h1>Admin Dashboard</h1> <form action="logouttest.php"><input value="Log out" type="submit"></form>
            <hr>
            
            <h2>User Management</h2>
            <button onclick="document.getElementById('createModal').style.display='block'">
                Create New User
            </button>

            <table style="margin-top: 20px; width: 100%; border-collapse: collapse;">
                <tr style="border-bottom: 1px solid #000;">
                    <th>Username</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                <?php 
                // Connecting, selecting database
                $dbconn = pg_connect("postgresql://zenteamrole:npg_jqtelOpA2V6s@ep-raspy-smoke-a5pyv0mk-pooler.us-east-2.aws.neon.tech/zenledgerdb?sslmode=require")
                    or die('Could not connect: ' . pg_last_error());
                
                
                // SQL query to read columns
                $query = "select employee_username, employee_role, is_activated from employee_personal_information order by employee_role, employee_username;";
                
                $result = pg_query($dbconn, $query) or die('Query failed: ' . pg_last_error());
                
                
                while ($row = pg_fetch_row($result, null, PGSQL_NUM)) { ?>
                <tr style="border-bottom: 1px solid #ddd;">
                    <td style="text-align: center;"><?php echo htmlspecialchars($row[0]) ?></td>
                    <td style="text-align: center;"><?php echo htmlspecialchars($row[1]) ?></td>
                    <td style="text-align: center;"><?php echo $row[2] === "true" ? 'Active' : 'Inactive' ?></td>
                    <td>
                        <form style="display: inline;" action="edit-info.php" method="POST">
                            <input type="hidden" name="username" value="<?php echo $row[0] ?>">
                            <input type="submit" value="Edit" style="margin-right: 5px;">
                        </form>
                        <?php 
                        if($_SESSION["username"] !== $row[0]) {
                        ?>
                            <form style="display: inline;" method="POST" action="toggle-activation.php">
                                <input type="hidden" name="username" value="<?php echo $row[0]?>">
                                <input type="hidden" name="is_activated" value="<?php echo $row[2]?>">
                                <input type="submit" name="toggle_status" 
                                    value="<?php echo ($row[2] === 'true') ? 'Deactivate' : 'Activate' ?>">
                            </form>
                            <br>
                            <form style="display: inline;" method="POST">
                                <input type="hidden" name="username" value="<?php echo $row[0] ?>">
                                <label>Start:</label><input type="date" name="suspend_start" required>
                                <label>End:</label><input type="date" name="suspend_end" required>
                                <input type="submit" name="suspend" value="Suspend">
                            </form>
                        <?php } ?>
                    </td>
                </tr>
                <?php } ?>
            </table>

            <!-- Create User Modal -->
            <div id="createModal" style="display: none; margin-top: 20px; border: 1px solid #ccc; padding: 20px;">
                <h3>Create New User</h3>
                <form method="POST" action="create-account.php">
                    <!-- TODO(ANYONE): Implement Sprint 1, Objective 20 -->
                    <div style="margin-bottom: 15px;">
                        <label>Username:</label>
                        <input type="text" name="username" required style="display: block; width: 100%; padding: 5px;">
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label>Password:</label>
                        <input type="password" name="password" required 
                            pattern="^(?=.*[A-Za-z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z].{7,}$"
                            style="display: block; width: 100%; padding: 5px;">
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label>Role:</label>
                        <select name="role" required style="display: block; width: 100%; padding: 5px;">
                            <option value="Administrator">Admin</option>
                            <option value="Manager">Manager</option>
                            <option value="User">User</option>
                        </select>
                    </div>
                    <input type="submit" name="create_user" value="Create User" style="margin-right: 10px;">
                    <button type="button" onclick="document.getElementById('createModal').style.display='none'">
                        Cancel
                    </button>
                </form>
            </div>
            <hr>
            <h3>Email System</h3>
            <form method="POST" action="mail.php">
                Email: <input type="text" name="send_email"><br>
                Subject: <input type="text" name="send_subect"><br>
                Message: <input type="text" name="send_message" rows="10" cols="40"><br>
                <input type="submit" value="Send">
            </form>
        </main>
        <footer></footer>
    </body>
    </html>
<?php }
else {
    echo "Unauthorized Page";
}
?>
