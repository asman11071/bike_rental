<?php
include 'config/db.php';

$rental_id = $_GET['id'];

// Step 1: get bike_id from rentals
$result = $conn->query("SELECT bike_id FROM rentals WHERE id=$rental_id");
$row = $result->fetch_assoc();
$bike_id = $row['bike_id'];

// Step 2: update return_time
$conn->query("UPDATE rentals SET return_time=NOW() WHERE id=$rental_id");

// Step 3: make bike available again
$conn->query("UPDATE bikes SET available=TRUE WHERE id=$bike_id");

echo "Bike Returned Successfully!";
?>