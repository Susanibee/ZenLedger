<?php session_start(); ?>
<!DOCTYPE html>
<html lang="">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="style/nonregisterstyle.css" rel="stylesheet" />
    <title>ZenLedger - Manual</title>
  </head>

  <body>
    <main>
    <?php if(isset($_SESSION["username"])) { ?>
        <?php include('snippets/logged-in-top-bar.php'); ?>
    <?php } else { ?>
        <?php include('snippets/guest-top-bar.php'); ?>
    <?php } ?>

        <h1> Manual </h1>
        <hr>
            <h2>All User Functionality</h2>
                <h3> Getting Started</h3>
                    <h4>Register</h4>
                    <p> A page that registers the user after filling in information. Username is auto-generated and works as follows:
                    First letter of first name, full last name, then the double digit month and double digit year of account creation. </p>
                    <p> When registered, an admin will be notified and manually activate your account for usage. </p>

                    <h4>Log In</h4>
                    <p> A page to log in as a registered user. If you fail to enter a correct password for an existing account,
                    the account will deactivate for security purposes. An admin can manually reactivate your account. </p>
                    <h4>Forgot Password?</h4>
                    <p> A page to reset a forgotten password. If you input the correct information alongside a new password,
                    your password will be changed. </p>

                <h3>Account Features</h3>
                    <h4>View</h4>
                        <p> A page to see all accounts on ZenLedger. In the future, you will be able to click on any account to view it's ledger.</p>

                <h3>Ledger</h3>
                    <h4>WIP</h4>
                    <p>Feature under construction.</p>

                <h3>Misc. Features</h3>
                    <h4>Calendar</h4>
                    <p> A complementary calendar included when signing in. It is found on the top bar and when clicked on, displays a pop up calendar. </p>

            <h2>Admin User Functionality</h2>
                <h3>Additional Account Features</h3>
                    <h4>Add</h4>
                        <p> A page to add an account on ZenLedger. Click the button after filling out the form to create an account.  </p>
                    <h4>Deactivate</h4>
                        <p> A page to activate or deactivate accounts on ZenLedger. Click the button corresponding to the account
                        whose status you would like to toggle.<br>Note: An account may only be deactivated if it has a balance of $0.</p>
                    <h4>Edit</h4>
                        <p> A page to edit an account's information on ZenLedger. Click the edit button corresponding to the account you want to edit.</p>

                <h3>Dashboard</h3>
                    <h4>Create User</h4>
                        <p> A section of the dashboard to create a user on ZenLedger. Click the button after filling out the form to create a user.</p>
                    <h4>Deactivate User</h4>
                        <p> A section of the dashboard to deactivate a user on ZenLedger. Click the button corresponding to the account 
                        whose status you want to toggle.</p>
                    <h4>Suspend User For Period of Time</h4>
                        <p> A section of the dashboard to suspend a user on ZenLedger for a period of time. Pick the date for the suspension,
                        then click the suspend button corresponding to the account you want to suspend.</p>
                    <h4>Email</h4>
                        <p> Work in progress. </p>
    </main>
    <footer>
    </footer>
</body>
</html>
