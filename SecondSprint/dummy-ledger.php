<?php session_start(); ?>
<!DOCTYPE html>
<html lang="">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="style/nonregisterstyle.css" rel="stylesheet" />
    <title>ZenLedger - Ledger</title>
    <style>
      /* calendar styling */
      .calendar-popup {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: white;
        border: 1px solid #ccc;
        padding: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        z-index: 1000;
      }
      .calendar-popup .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
      }
      .calendar-popup .header button {
        background-color: #f0f0f0;
        border: none;
        padding: 5px 10px;
        cursor: pointer;
      }
      .calendar-popup .calendar {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 5px;
        text-align: center;
        margin-top: 10px;
      }
      .calendar-popup .calendar .day {
        padding: 10px;
        cursor: pointer;
      }
      .calendar-popup .calendar .day:hover {
        background-color: #f0f0f0;
      }

      .overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 999;
      }

      /* end calendar styling */
    </style>
  </head>

  <body>
    <main>
    <?php
    if(isset($_SESSION["username"]))
    {
    ?>
      <div class="name-left"><?php echo $_SESSION["username"]; ?></div>
      <div class="top-bar">
        <a href="index.php">
        <div class="logo">
          <img src="images\zenlogo.png" class="zenlogo-picture" />
        </div>
        </a>

        <button
          onclick="window.location.href='/accounts.php';"
          class="chart-button"
        >
          Accounts
        </button>

        <button
          class="ledger-button"
        >
          Ledger
        </button>

        <?php if(isset($_SESSION["admin"])) {
        ?>
        <button
          onclick="window.location.href='/admin-dashboard.php';"
          class="placeholder-button"
        >
          Dashboard
        </button>

        <?php } ?>

        <button class="calendar-button" id="calendar-button">Calendar</button>

          <img
            src="/images/turtlesallthewaydown.png"
            alt="Profile Image"
            class="headshot"
          />
        <a href='/action-logout.php'>
        <button
          onclick="window.location.href='/action-logout.php';"
          class="sign-out-button"
        >
          Log Off
        </button>
        </a>
      </div>

    <!-- Overlay for the popup -->
    <div class="overlay" id="overlay"></div>

    <!-- Calendar Popup -->
    <div class="calendar-popup" id="calendar-popup">
      <div class="header">
        <button id="prev-month">Prev</button>
        <span id="calendar-month-year"></span>
        <button id="next-month">Next</button>
      </div>
      <div class="calendar" id="calendar-days"></div>
    </div>

    <!-- calendar script-->
    <script>
      const calendarButton = document.getElementById("calendar-button");
      const calendarPopup = document.getElementById("calendar-popup");
      const overlay = document.getElementById("overlay");
      const prevMonthButton = document.getElementById("prev-month");
      const nextMonthButton = document.getElementById("next-month");
      const monthYearDisplay = document.getElementById("calendar-month-year");
      const calendarDays = document.getElementById("calendar-days");

      let currentDate = new Date();

      // Function to show or hide the calendar
      function toggleCalendar() {
        calendarPopup.style.display =
          calendarPopup.style.display === "block" ? "none" : "block";
        overlay.style.display =
          overlay.style.display === "block" ? "none" : "block";
      }

      // Function to update the calendar display
      function updateCalendar() {
        const month = currentDate.getMonth();
        const year = currentDate.getFullYear();
        monthYearDisplay.textContent = `${currentDate.toLocaleString(
          "default",
          {
            month: "long",
          }
        )} ${year}`;
        const firstDay = new Date(year, month, 1);
        const lastDate = new Date(year, month + 1, 0).getDate();
        const firstDayOfWeek = firstDay.getDay();
        const days = [];

        // Add empty cells for days before the 1st day of the month
        for (let i = 0; i < firstDayOfWeek; i++) {
          days.push("");
        }

        // Add days of the current month
        for (let i = 1; i <= lastDate; i++) {
          days.push(i);
        }

        // Render the days
        calendarDays.innerHTML = "";
        days.forEach((day) => {
          const dayCell = document.createElement("div");
          dayCell.className = "day";
          dayCell.textContent = day;
          calendarDays.appendChild(dayCell);
        });
      }

      // Event listeners for the month navigation buttons
      prevMonthButton.addEventListener("click", () => {
        currentDate.setMonth(currentDate.getMonth() - 1);
        updateCalendar();
      });
      nextMonthButton.addEventListener("click", () => {
        currentDate.setMonth(currentDate.getMonth() + 1);
        updateCalendar();
      });

      // Show the calendar popup when the calendar button is clicked
      calendarButton.addEventListener("click", toggleCalendar);
      overlay.addEventListener("click", toggleCalendar);

      // Initialize calendar
      updateCalendar();
    </script>
    <!--end calendar script-->
    <?php
    }
    else {
    ?>
    <div class="top-bar">

        <a href="index.php">
        <div class="logo" >
            <img src="images\zenlogo.png" class="zenlogo-picture" />
        </div>
        </a>

        <div class="top-buttons">

          <button  class="nav-button-support" > Support </button>
          <button  class="nav-button-about" > About </button>

          <button class="sign-in-button" onclick="window.location.href='login.php';">
              Login
          </button>
        </div>
    </div>
    <?php } ?>

        <h1> Ledger </h1>
        <hr>
        <p> Nothing here. . . </p>
    </main>
</body>
</html>
