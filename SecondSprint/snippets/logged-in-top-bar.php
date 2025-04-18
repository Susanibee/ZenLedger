<div class="name-left"><?php echo $_SESSION["username"]; ?></div>
<div class="top-bar">
    <a href="index.php">
    <div class="logo">
        <img src="images\zenlogo.png" class="zenlogo-picture" />
    </div>
    </a>

    <button onclick="window.location.href='/accounts.php';" class="chart-button" > Accounts </button>
    <button class="ledger-button" > Ledger </button>

    <?php if(isset($_SESSION["admin"])) { ?>
    <button onclick="window.location.href='/admin-dashboard.php';" class="placeholder-button" > Dashboard </button>
    <?php } ?>

    <button class="calendar-button" id="calendar-button">Calendar</button>
    <img src="/images/turtlesallthewaydown.png" alt="Profile Image" class="headshot" />
    <a href='/action-logout.php'> <button onclick="window.location.href='/action-logout.php';" class="sign-out-button" > Log Off </button> </a>
    <script src="snippets/calendar.js"></script> <!-- PAGE NEEDS AN id="calendar-button" for script to work-->
</div>

