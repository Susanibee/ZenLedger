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
<html lang="">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="style/nonregisterstyle.css" rel="stylesheet" />
    <title>ZenLedger - Dashboard</title>
  </head>
  <body>
    <main>
            <?php include('snippets/logged-in-top-bar.php'); ?>
            <div class="cosmic-container">
    <h1>Admin Dashboard</h1>
    <p class="cosmic-message"><?php echo $cosmic_message; ?></p>
</div>
            <hr>
            <div class="helper">
                  <img src="images/zenledger logo.png" class="background-logo" />
              </div>

            
            <h2>User Management</h2>
            <button onclick="document.getElementById('createModal').style.display='block'">
                Create New User
            </button>

            <!-- Create User Modal -->
            <div id="createModal" style="display: none; margin-top: 20px; border: 1px solid #ccc; padding: 20px;">
                <h3>Create New User</h3>
                <form method="POST" action="action-admin-register-user.php">
                    <div style="margin-bottom: 15px;">
                        <!-- Kindof a dumb solution, but i don't think we need anything too complex for this -->
                        <p>Input user first inital, full last name, and current month and year as: MMYY</p>
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

            <table style="margin-top: 20px; width: 100%; border-collapse: collapse;">
                <tr style="border-bottom: 1px solid #000;">
                    <th>Username</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                <?php 
                // Connecting, selecting database
                $dbconn = pg_connect("postgresql://zenteamrole:${{ secrets.pgpass }}@ep-raspy-smoke-a5pyv0mk-pooler.us-east-2.aws.neon.tech/zenledgerdb?sslmode=require")
                    or die('Could not connect: ' . pg_last_error());
                
                
                // SQL query to read columns
                $query = "select employee_username, employee_role, is_activated from employee_personal_information order by employee_role, employee_username;";
                
                $result = pg_query($dbconn, $query) or die('Query failed: ' . pg_last_error());
                
                
                while ($row = pg_fetch_row($result, null, PGSQL_NUM)) { ?>
                <tr style="border-bottom: 1px solid #ddd; line-height: 3em;">
                    <td style="text-align: center;"><?php echo htmlspecialchars($row[0]) ?></td>
                    <td style="text-align: center;"><?php echo htmlspecialchars($row[1]) ?></td>
                    <td style="text-align: center;"><?php echo $row[2] === "true" ? 'Active' : 'Inactive' ?></td>
                    <td>
                        <?php 
                        if($_SESSION["username"] !== $row[0]) {
                        ?>
                            <form style="display: inline;" method="POST" action="action-toggle-user-active-status.php">
                                <input type="hidden" name="username" value="<?php echo $row[0]?>">
                                <input type="hidden" name="is_activated" value="<?php echo $row[2]?>">
                                <input type="submit" name="toggle_status" 
                                    value="<?php echo ($row[2] === 'true') ? 'Deactivate' : 'Activate' ?>">
                            </form>
                            <br>
                            <form style="display: inline;" method="POST" action="action-suspend-user-for-period.php">
                                <input type="hidden" name="username" value="<?php echo $row[0] ?>">
                                <label>Start:</label><input type="date" name="suspend_start" required>
                                <label>End:</label><input type="date" name="suspend_end" required>
                                <input type="submit" name="suspend" value="Suspend">
                            </form>
                            <br>
                        <?php } ?>
                        <form style="display: inline;" action="admin-edit-personal-info.php" method="POST">
                            <input type="hidden" name="username" value="<?php echo $row[0] ?>">
                            <input type="submit" value="Update Personal Information" style="margin-right: 5px;">
                            <br>
                        </form>
                    </td>
                </tr>
                <?php } ?>
            </table>
            <hr>
            <h2>Email System</h2>
            <form method="POST" action="mail.php">
                Email: <input type="text" name="send_email"><br>
                Subject: <input type="text" name="send_subect"><br>
                Message: <input type="text" name="send_message" rows="10" cols="40"><br>
                <input type="submit" value="Send">
            </form>
        </main>
        <div class="booties"><a href="help.php" class="help-button">Need help?</a>
    </div>
    <script src="snippets/calendar.js"></script> 
    </footer>
    </body>
    </html>
