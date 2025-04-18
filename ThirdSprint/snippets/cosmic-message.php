<?php
if (!isset($_SESSION['selected_customer'])) {
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
    "Harmonizing with the soul of {$bold_name}’s balance sheet.",
    "Your cash karma is aligning. {$bold_name} radiates abundance.",
    "The universe balances {$bold_name}'s books and chakras.",
    "Debits and credits swirl in divine symmetry around {$bold_name}.",
    "Manifesting money moons and ledger lines for {$bold_name}.",
    "{$bold_name} enters a new fiscal frequency.",
    "The stars approve {$bold_name}'s journal entries. Mercury is in retroactive credit.",
    "Financial flow achieved. {$bold_name} has reached peak cash-consciousness.",
    "You are now exiting the Audit Plane. Safe travels, {$bold_name}.",
    "Profit is just another word for energetic alignment, {$bold_name}.",
    "{$bold_name}'s bank statements are merely scrolls of financial fate.",
    "{$bold_name}'s financial crystals charged. Balance sheets balanced.",
    "{$bold_name} is spending with intention while investing with intuition.",
    "{$bold_name} channels inner wealth and outer net worth.",
    "{$bold_name}'s ROI is written in the stars.",
    "Cosmic accounting complete. Zen flows freely through {$bold_name}.",
    "{$bold_name} is seen. We are solvent. We are stardust and stock options.",
    "Breathe in {$bold_name}'s monetary abundance. Exhale unnecessary expenses."
];

$cosmic_message = $affirmations[array_rand($affirmations)];
?>