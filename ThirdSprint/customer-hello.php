<?php
session_start();


if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}


if (empty($_SESSION['selected_customer'])) {
    header("Location: index.php");
    exit();
}


$selected_customer = $_SESSION['selected_customer'];

$bold_name = "<strong>" . htmlspecialchars($selected_customer, ENT_QUOTES, 'UTF-8') . "</strong>";
$affirmations = [
    "You are now in energetic alignment with {$bold_name}’s financial aura.",
    "Tuning into the fiscal frequency of {$bold_name}.",
    "Channeling the abundance field surrounding {$bold_name}’s wealth journey.",
    "You have entered the sacred accounting space of {$bold_name}.",
    "Now witnessing the manifested abundance of {$bold_name}.",
    "Your third eye now gazes upon the prosperity path of {$bold_name}.",
    "Floating gently through the monetary meridians of {$bold_name}.",
    "Harmonizing with the soul of {$bold_name}’s balance sheet."
];


$cosmic_message = $affirmations[array_rand($affirmations)];
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>ZenLedger</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="style/nonregisterstyle.css" rel="stylesheet">
</head>
<body>

<?php include('snippets/logged-in-top-bar.php'); ?>

<div class="cosmic-container">
<h1> Main Dashboard </h1>


<p class="cosmic-message"><?php echo $cosmic_message; ?></p>
</div>

<hr>
<div class="helper">
                  <img src="images/zenledger logo.png" class="background-logo" />
              </div>

<div class="cosmic-options">
    <p>Placeholder for now</p>
</div>

</body>
</html>