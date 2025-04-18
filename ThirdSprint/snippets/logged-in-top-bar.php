<div class="name-left"> <span>Logged in as </span><b><?php echo $_SESSION["username"]; ?></b> </div>

<div class="dash-top-bar">
    <div class="dash-logo"> <a href="index.php"><img src="images/zenlogo.png" class="dash-zenlogo-picture" /> </a> </div>

    <div class="dash-top-nav">
        <a href="statements-sheet.php" class="accounts-anchor">Statements</a>

        <?php if(isset($_SESSION["admin"])) { ?>
            <a href="admin-dashboard.php" class="dashboard-anchor">Dashboard </a>
       <?php } else { ?>
          <a href="journal-entry.php" class="journal-anchor">Journal</a>
       <?php } ?>

      <a href="accounts.php" class="accounts-anchor">Accounts</a>

      <a href="ledger.php" class="ledger-anchor">Ledger</a>

      <a class="calendar-button" id="calendar-button">Calendar</a>

      <img src="/images/turtlesallthewaydown.png" alt="Profile Image" class="employee-picture" />

      <a href="action-logout.php" class=signout-anchor>Sign Out</a>

      <script src="snippets/calendar.js"></script> <!-- PAGE NEEDS AN id="calendar-button" for script to work-->

    </div>
</div>
