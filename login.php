<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="container">
<h2>Login</h2>

<form action="backend/auth.php" method="POST">
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>

    <button type="submit" name="login">Login</button>
</form>

<p>New user? <a href="signup.php">Sign up</a></p>
</div>

</body>
</html>