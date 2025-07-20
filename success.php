<?php
// Database connection (update with your credentials)
$host = '127.0.0.1';
$username = 'root';
$password = '';
$dbname = 'booking_bus';

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if booking_id is set in the URL
if (isset($_GET['booking_id'])) {
    $booking_id = $_GET['booking_id'];
} else {
    echo "<h3>Booking ID is missing or invalid!</h3>";
    exit();
}

// Query to get booking, bus details, and seat number
$sql = "SELECT bs.booking_id, bs.travel_date, bs.is_booked, bs.seat_number, b.bus_name, b.source, b.destination, b.departure_time, b.arrival_time, b.price 
        FROM booked_seats bs 
        JOIN buses b ON bs.bus_id = b.bus_id 
        WHERE bs.booking_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $booking_id);
$stmt->execute();
$result = $stmt->get_result();
$booking_details = $result->fetch_assoc();

// Query to get passenger details for the booking
$passenger_sql = "SELECT name FROM passengers WHERE booking_id = ?";
$passenger_stmt = $conn->prepare($passenger_sql);
$passenger_stmt->bind_param('i', $booking_id);
$passenger_stmt->execute();
$passenger_result = $passenger_stmt->get_result();

// Fetch all passenger names
$passenger_names = [];
while ($row = $passenger_result->fetch_assoc()) {
    $passenger_names[] = $row['name'];
}

// Close the connection
$conn->close();

// If no booking found
if (!$booking_details) {
    echo "<h3>Booking not found</h3>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }

        .navbar {
            background-color: #1e63d4;
        }

        .navbar a {
            color: white;
        }

        .card {
            background-color: white;
            padding: 40px;
            margin-bottom: 30px;
            border-radius: 10px;
            box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.1);
        }

        .btn-custom {
            background-color: #1e63d4;
            color: white;
            font-size: 1.2rem;
        }

        .btn-custom:hover {
            background-color: #1e63d4;
            color: white;

        }

        .green-tick {
            font-size: 60px;
            color: #1e63d4;
            margin-bottom: 7px;
        }

        .confirmation-message {
            font-size: 1.4rem;
            color: #333;
            text-align: center;
        }

        .card-body table th {
            background-color: #f8f9fa;
            color: #555;
        }

        .card-body table td {
            color: #333;
        }

        .card-body table {
            margin-bottom: 25px;
        }

        .button-container {
            text-align: center;
        }

        .card-header {
            text-align: center;
            font-size: 1.5rem;
            color: #1e63d4;
        }

        @media (max-width: 767px) {
            .card-header {
                text-align: center;
                font-size: 1rem;
                color: #1e63d4;
            }

            .confirmation-message {
                font-size: 0.8rem;
                color: #333;
                text-align: center;
                margin-top: 10px;
            }

            .card {
                padding: 5px;
                border-radius: 10px;
            }

            h3 {
                font-size: 1.1rem;
            }

            .btn-custom {
                margin-bottom: 20px;
                font-size: 1rem;
            }

            .green-tick {
                font-size: 55px;
                color: #1e63d4;
                margin-bottom: 10px;
            }
        }
    </style>
</head>

<body>
    <?php include "navbar.php" ?>

    <!-- Booking Confirmation -->
    <div class="container mt-4">
        <div class="card">
            <div class="confirmation-message">
                <i class="fas fa-check-circle green-tick"></i>
                <h3>Booking Confirmed Successfully!</h3>
            </div>
            <div class="bus-info mt-4">
                <h3>Bus Information</h3>
                <table class="table table-bordered">
                    <tr>
                        <td><strong>Bus Name:</strong></td>
                        <td><?php echo htmlspecialchars($booking_details['bus_name']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Source:</strong></td>
                        <td><?php echo htmlspecialchars($booking_details['source']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Destination:</strong></td>
                        <td><?php echo htmlspecialchars($booking_details['destination']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Departure Time:</strong></td>
                        <td><?php echo htmlspecialchars($booking_details['departure_time']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Arrival Time:</strong></td>
                        <td><?php echo htmlspecialchars($booking_details['arrival_time']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Price:</strong></td>
                        <td>₹<?php echo number_format($booking_details['price'], 2); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Travel Date:</strong></td>
                        <td><?php echo htmlspecialchars($booking_details['travel_date']); ?></td>
                    </tr>
                </table>
                <a href="search_bus.php" class="btn btn-secondary w-100" style="background-color: #1e63d4;">Return to Homepage</a>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>

</html>
