<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="container">
<h2>Signup</h2>

<form action="backend/auth.php" method="POST">
    <input type="text" name="name" placeholder="Name" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>

    <select name="role">
        <option value="member">Member</option>
        <option value="admin">Admin</option>
    </select>

    <button type="submit" name="signup">Signup</button>
</form>

</div>
</body>
</html>