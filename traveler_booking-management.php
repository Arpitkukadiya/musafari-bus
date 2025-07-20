<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'traveler') {
    header("Location: index.php");
    exit;
}

include 'config.php'; // Database connection file

$traveler_id = $_SESSION['id'];

// Fetch the buses added by the traveler
$buses_query = $conn->prepare("SELECT bus_id, bus_name FROM buses WHERE traveler_id = ?");
$buses_query->bind_param("i", $traveler_id);
$buses_query->execute();
$buses_result = $buses_query->get_result();

// Fetch bookings for the buses associated with the traveler
$bookings_query = "
    SELECT b.id AS booking_id, b.user_id, b.travel_date, b.total_price, b.booking_date, 
        p.name AS passenger_name, s.seat_number, bus.bus_name, 
        CASE 
            WHEN bs.is_booked = 1 THEN 'Booked' 
            ELSE 'Available' 
        END AS booking_status
    FROM bookings b
    JOIN passengers p ON b.id = p.booking_id
    JOIN booked_seats bs ON b.id = bs.booking_id
    JOIN seats s ON bs.seat_id = s.seat_id
    JOIN buses bus ON b.bus_id = bus.bus_id
    WHERE bus.traveler_id = ?
    ORDER BY b.booking_date DESC;
";

$bookings_stmt = $conn->prepare($bookings_query);
$bookings_stmt->bind_param("i", $traveler_id);
$bookings_stmt->execute();
$bookings_result = $bookings_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Bookings - Traveler Panel</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include "traveler_navbar.php" ?>

<div class="content">
    <div class="container">
        <h3 class="mb-4">Your Bus Bookings</h3>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Bus Name</th>
                        <th>Travel Date</th>
                        <th>Passenger Name</th>
                        <th>Seat Number</th>
                        <th>Booking Date</th>
                        <th>Total Price</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($bookings_result->num_rows > 0) {
                        while ($row = $bookings_result->fetch_assoc()) {
                            echo "<tr>
                                    <td>".$row['booking_id']."</td>
                                    <td>".$row['bus_name']."</td>
                                    <td>".$row['travel_date']."</td>
                                    <td>".$row['passenger_name']."</td>
                                    <td>".$row['seat_number']."</td>
                                    <td>".$row['booking_date']."</td>
                                    <td>".$row['total_price']."</td>
                                    <td>".$row['booking_status']."</td>
                                    <td><a href='delete_booking.php?id=".$row['booking_id']."' class='btn btn-danger'>Delete</a></td>
                                </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='9' class='text-center'>No bookings found for your buses.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
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
