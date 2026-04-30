<?php
session_start();
include 'config/db.php';

/* REGISTER */
if(isset($_POST['register'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $check = $conn->query("SELECT * FROM users WHERE email='$email'");

    if($check->num_rows > 0) {
        $reg_error = "User already registered. Please login.";
    } else {
        $conn->query("INSERT INTO users (name,email,password) VALUES ('$name','$email','$password')");
        $success = "Registration successful! Please login.";
    }
}

/* LOGIN */
if(isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $result = $conn->query("SELECT * FROM users WHERE email='$email' AND password='$password'");

    if($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        header("Location: app.php");
        exit();
    } else {
        $error = "Invalid email or password";
    }
}

/* LOGOUT */
if(isset($_GET['logout'])) {
    session_destroy();
    header("Location: app.php");
    exit();
}

/* RENT */
if(isset($_GET['rent']) && isset($_SESSION['user_id'])) {
    $bike_id = $_GET['rent'];
    $user_id = $_SESSION['user_id'];

    $conn->query("INSERT INTO rentals (user_id, bike_id, rent_time) VALUES ($user_id, $bike_id, NOW())");
    $conn->query("UPDATE bikes SET available=FALSE WHERE id=$bike_id");

    header("Location: app.php");
    exit();
}

/* RETURN */
if(isset($_GET['return']) && isset($_SESSION['user_id'])) {
    $rental_id = $_GET['return'];

    $res = $conn->query("SELECT bike_id FROM rentals WHERE id=$rental_id");
    $row = $res->fetch_assoc();
    $bike_id = $row['bike_id'];

    $conn->query("UPDATE rentals SET return_time=NOW() WHERE id=$rental_id");
    $conn->query("UPDATE bikes SET available=TRUE WHERE id=$bike_id");

    header("Location: app.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Bike Rental System</title>

<style>
* { margin:0; padding:0; box-sizing:border-box; }

body {
    font-family: 'Segoe UI', sans-serif;
    background: linear-gradient(135deg, #0f2027, #2c5364);
    color: white;
}

h1 {
    text-align:center;
    padding:20px;
}

.container { width:85%; margin:auto; }

.card {
    background: rgba(255,255,255,0.08);
    backdrop-filter: blur(15px);
    border-radius:15px;
    padding:20px;
    margin:20px 0;
    box-shadow:0 10px 30px rgba(0,0,0,0.3);
}

.card:hover { transform:translateY(-5px); }

input {
    width:90%;
    padding:10px;
    margin:8px;
    border-radius:8px;
    border:none;
}

button {
    padding:10px 20px;
    border:none;
    border-radius:8px;
    background: linear-gradient(45deg,#00c6ff,#0072ff);
    color:white;
    cursor:pointer;
}

button:hover {
    transform:scale(1.05);
    box-shadow:0 0 10px #00c6ff;
}

.bike {
    display:flex;
    justify-content:space-between;
    padding:12px;
    margin:10px 0;
    background: rgba(255,255,255,0.1);
    border-radius:10px;
}

.bike:hover { background: rgba(255,255,255,0.2); }

a {
    text-decoration:none;
    padding:6px 14px;
    border-radius:6px;
}

.rent {
    background: linear-gradient(45deg,#00e676,#00c853);
    color:black;
}

.return {
    background: linear-gradient(45deg,#ff5252,#d50000);
}

.logout {
    position:fixed;
    top:20px;
    right:30px;
    background:#ff1744;
    padding:10px 15px;
    border-radius:8px;
    z-index:1000;
}

.center { text-align:center; }

.empty {
    text-align:center;
    opacity:0.7;
    padding:15px;
}

.error { color:#ff5252; }
.success { color:#00e676; }

</style>
</head>

<body>

<h1>🚴 Bike Rental System</h1>

<?php if(!isset($_SESSION['user_id'])) { ?>

<div class="center">

<div class="card">
<h2>Register</h2>

<?php 
if(isset($reg_error)) echo "<p class='error'>$reg_error</p>"; 
if(isset($success)) echo "<p class='success'>$success</p>"; 
?>

<form method="POST">
<input type="text" name="name" placeholder="Name" required><br>
<input type="email" name="email" placeholder="Email" required><br>
<input type="password" name="password" placeholder="Password" required><br>
<button name="register">Register</button>
</form>
</div>

<div class="card">
<h2>Login</h2>

<?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>

<form method="POST">
<input type="email" name="email" placeholder="Email" required><br>
<input type="password" name="password" placeholder="Password" required><br>
<button name="login">Login</button>
</form>
</div>

</div>

<?php } else { ?>

<div class="container">

<a class="logout" href="?logout=true">Logout</a>

<div class="card">
<h2>Available Bikes</h2>

<?php
$result = $conn->query("SELECT * FROM bikes WHERE available=TRUE");

if($result->num_rows == 0){
    echo "<div class='empty'>No bikes available 😢</div>";
}

while($row = $result->fetch_assoc()) {
    echo "<div class='bike'>";
    echo "<span>".$row['name']." - ₹".$row['price']."</span>";
    echo "<a class='rent' href='?rent=".$row['id']."'>Rent</a>";
    echo "</div>";
}
?>
</div>

<div class="card">
<h2>Your Rentals</h2>

<?php
$user_id = $_SESSION['user_id'];

$result = $conn->query("
SELECT rentals.id, bikes.name 
FROM rentals 
JOIN bikes ON rentals.bike_id = bikes.id 
WHERE return_time IS NULL AND user_id=$user_id
");

if($result->num_rows == 0){
    echo "<div class='empty'>No active rentals 🚀</div>";
}

while($row = $result->fetch_assoc()) {
    echo "<div class='bike'>";
    echo "<span>".$row['name']."</span>";
    echo "<a class='return' href='?return=".$row['id']."'>Return</a>";
    echo "</div>";
}
?>
</div>

</div>

<?php } ?>

</body>
</html>