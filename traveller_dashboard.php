<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'traveler') {
    header("Location: index.php");
    exit;
}

include 'config.php'; // Database connection file

$traveler_id = $_SESSION['id'];

// Fetch the count of buses added by the traveler
$buses_query = $conn->prepare("SELECT COUNT(*) AS bus_count FROM buses WHERE traveler_id = ?");
$buses_query->bind_param("i", $traveler_id);
$buses_query->execute();
$buses_result = $buses_query->get_result();
$buses_data = $buses_result->fetch_assoc();
$bus_count = $buses_data['bus_count'];

// Fetch the count of bookings for the traveler's buses
$bookings_query = "
    SELECT COUNT(*) AS booking_count 
    FROM bookings b
    JOIN buses bus ON b.bus_id = bus.bus_id
    WHERE bus.traveler_id = ?";
$bookings_stmt = $conn->prepare($bookings_query);
$bookings_stmt->bind_param("i", $traveler_id);
$bookings_stmt->execute();
$bookings_result = $bookings_stmt->get_result();
$bookings_data = $bookings_result->fetch_assoc();
$booking_count = $bookings_data['booking_count'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Traveler Dashboard</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include "traveler_navbar.php" ?>

<div class="content">
    <div class="container">
        <h3 class="mb-4">Traveler Dashboard</h3>

        <div class="row">
            <!-- Bus Count Card -->
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Your Buses</h5>
                        <p class="card-text">You have added <strong><?php echo $bus_count; ?></strong> buses.</p>
                    </div>
                </div>
            </div>

            <!-- Booking Count Card -->
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Your Bookings</h5>
                        <p class="card-text">You have <strong><?php echo $booking_count; ?></strong> bookings.</p>
                    </div>
                </div>
            </div>

           

        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
