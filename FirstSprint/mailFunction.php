<form method="POST" action="">
Email: <input type="text" name="email"><br>
Subject: <input type="text" name="subject" cols="40"><br>
Message: <textarea input type="text" name="message" rows="10" cols="40"></textarea><br>
<input type="submit" value="Send">
</form>

<?php
$email = $_POST['email'];
$subject = $_POST['subject'];
$message = $_POST['message'];

mail($email, $subject, $message);
?>