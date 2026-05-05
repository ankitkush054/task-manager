<?php
session_start();
include 'db.php';

if (isset($_POST['signup'])) {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $pass = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = $_POST['role'];

    $conn->query("INSERT INTO users (name,email,password,role)
                  VALUES ('$name','$email','$pass','$role')");

    header("Location: ../login.php");
}

if (isset($_POST['login'])) {

    $email = $_POST['email'];
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM users WHERE email='$email'");
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {

        $_SESSION['user'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        header("Location: ../index.php");
    } else {
        echo "Login failed";
    }
}
?>