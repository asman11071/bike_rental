<?php
session_start();
include 'config/db.php';

$email = $_POST['email'];
$password = $_POST['password'];

$result = $conn->query("SELECT * FROM users WHERE email='$email' AND password='$password'");

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $_SESSION['user_id'] = $user['id'];

    header("Location: dashboard.php");
} else {
    echo "Wrong Email or Password";
}
?>