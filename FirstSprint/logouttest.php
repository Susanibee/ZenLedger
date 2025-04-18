<?php session_start();
session_unset();
session_destroy();
$Message = urlencode("Logged out successfully.");
header("Location:login.php?Message=".$Message);    
?>