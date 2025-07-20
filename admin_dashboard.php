<?php
// Include the database connection file
include('config.php');
session_start();
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit;
}

// Query to get the number of bookings
$booking_count_query = "SELECT COUNT(*) AS total_bookings FROM bookings";
$result = $conn->query($booking_count_query);
$booking_count = $result->fetch_assoc()['total_bookings'];

// Query to get the number of buses
$bus_count_query = "SELECT COUNT(*) AS total_buses FROM buses";
$result = $conn->query($bus_count_query);
$bus_count = $result->fetch_assoc()['total_buses'];

// Query to get the number of customers
$customer_count_query = "SELECT COUNT(*) AS total_customers FROM users WHERE role = 'customer'";
$result = $conn->query($customer_count_query);
$customer_count = $result->fetch_assoc()['total_customers'];

$traveler_count_query = "SELECT COUNT(*) AS total_travelers FROM users WHERE role = 'traveler'";
$result = $conn->query($traveler_count_query);
$traveler_count = $result->fetch_assoc()['total_travelers'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f5f7;
            margin: 0;
        }
        .dashboard-card {
            margin-top: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .card-body {
            padding: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .card-title {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .card-text {
            font-size: 1.2rem;
        }
        .card-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: space-between;
        }
        .card-custom {
            flex: 1 1 calc(50% - 20px);
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        .card-custom:hover {
            transform: translateY(-10px);
        }
        .card-custom.green {
            background-color: #2d6a4f;
            color: white;
        }
        .card-custom.blue {
            background-color: #2575fc;
            color: white;
        }
        .card-custom.yellow {
            background-color: #f1c40f;
            color: white;
        }
        .card-custom.red {
            background-color: #e74c3c;
            color: white;
        }
    </style>
</head>
<body>
<?php include "admin_navbar.php" ?>
<div class="content">
<div class="container">
    <h2 class="mb-4 text-center">Admin Dashboard</h2>

    <div class="card-container">
        <!-- Total Customers Card -->
        <div class="card-custom green">
            <h5 class="card-title">Total Customers</h5>
            <p class="card-text"><?php echo $customer_count; ?> customers</p>
        </div>

        <!-- Total Traveler Users Card -->
        <div class="card-custom blue">
            <h5 class="card-title">Total Traveler Users</h5>
            <p class="card-text"><?php echo $traveler_count; ?> travelers</p>
        </div>

        <!-- Total Buses Card -->
        <div class="card-custom yellow">
            <h5 class="card-title">Total Buses</h5>
            <p class="card-text"><?php echo $bus_count; ?> buses</p>
        </div>

        <!-- Total Bookings Card -->
        <div class="card-custom red">
            <h5 class="card-title">Total Bookings</h5>
            <p class="card-text"><?php echo $booking_count; ?> bookings</p>
        </div>
    </div>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
</body>
</html>

<?php
// Close the connection
$conn->close();
?>
