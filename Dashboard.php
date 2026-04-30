<?php
session_start();

if(!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

include 'config/db.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Bike Rental Dashboard</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f6fa;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            margin: auto;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #2f3640;
        }

        .logout {
            float: right;
            text-decoration: none;
            background: #e84118;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
        }

        .card {
            background: white;
            padding: 20px;
            margin-top: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .bike {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        .btn {
            text-decoration: none;
            padding: 6px 12px;
            border-radius: 5px;
            color: white;
        }

        .rent {
            background: #44bd32;
        }

        .return {
            background: #0097e6;
        }

    </style>
</head>

<body>

<div class="container">

    <a class="logout" href="logout.php">Logout</a>

    <h1>🚴 Bike Rental System</h1>

    <!-- Available Bikes -->
    <div class="card">
        <h2>Available Bikes</h2>

        <?php
        $result = $conn->query("SELECT * FROM bikes WHERE available=TRUE");

        while($row = $result->fetch_assoc()) {
            echo "<div class='bike'>";
            echo "<span>".$row['name']." - ₹".$row['price']."</span>";
            echo "<a class='btn rent' href='rent.php?id=".$row['id']."'>Rent</a>";
            echo "</div>";
        }
        ?>
    </div>

    <!-- Your Rentals -->
    <div class="card">
        <h2>Your Rentals</h2>

        <?php
        $user_id = $_SESSION['user_id'];

        $result = $conn->query("
        SELECT rentals.id, bikes.name 
        FROM rentals 
        JOIN bikes ON rentals.bike_id = bikes.id 
        WHERE return_time IS NULL AND user_id = $user_id
        ");

        while($row = $result->fetch_assoc()) {
            echo "<div class='bike'>";
            echo "<span>".$row['name']."</span>";
            echo "<a class='btn return' href='return.php?id=".$row['id']."'>Return</a>";
            echo "</div>";
        }
        ?>
    </div>

</div>

</body>
</html>