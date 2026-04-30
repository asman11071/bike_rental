<?php
session_start();
include 'config/db.php';

$id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// insert rental
$conn->query("INSERT INTO rentals (user_id, bike_id, rent_time)
VALUES ($user_id, $id, NOW())");

// make bike unavailable
$conn->query("UPDATE bikes SET available=FALSE WHERE id=$id");

echo "Bike Rented Successfully!";
?>