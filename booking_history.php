<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'customer') {
    header("Location: index.php");
    exit;
}

include('config.php'); // Include your database connection

$userId = $_SESSION['id']; // Get the logged-in user's ID

// Query to get bookings for the logged-in user, including seat numbers and status
$query = "SELECT b.id, b.travel_date, b.total_price, b.source, b.destination, b.arrival_time, b.status, 
                 GROUP_CONCAT(s.seat_number ORDER BY s.seat_number) AS seat_numbers
          FROM bookings b
          LEFT JOIN booked_seats bs ON b.id = bs.booking_id
          LEFT JOIN seats s ON bs.seat_id = s.seat_id
          WHERE b.user_id = ?
          GROUP BY b.id, b.travel_date, b.total_price, b.source, b.destination, b.arrival_time, b.status";

$stmt = $conn->prepare($query);
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cancel_booking'])) {
    $bookingId = $_POST['booking_id'];
    
    // Update the booking status to 'Cancelled'
    $cancelQuery = "UPDATE bookings SET status = 'Cancelled' WHERE id = ?";
    $cancelStmt = $conn->prepare($cancelQuery);
    $cancelStmt->bind_param('i', $bookingId);
    $cancelStmt->execute();
    
    header("Location: booking_history.php"); // Refresh page after cancel
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking History</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        h2 {
            margin-bottom: 20px;
            text-align: center;
        }
        .table th, .table td {
            text-align: center;
            vertical-align: middle;
        }
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            background-color: #1e63d4;
        }
        .navbar a {
            color: white;
        }
        .card {
            display: flex;
            justify-content: center;
            margin-top: 50px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .card-body {
            padding: 30px;
        }
        .section-title {
            color: #1e63d4;
            margin-top: 30px;
            font-weight: bold;
        }
        .form-control,
        .btn {
            border-radius: 10px;
        }
        .btn-success {
            background-color: #1e63d4;
            color: white;
            border-radius: 10px;
            width: 100%;
            border: none;
            font-size: 1rem;
        }
        .btn-success:hover {
            background-color: #1e63d4;
            color: white;
        }
        .form-control {
            border-radius: 10px;
            box-shadow: inset 0 1px 5px rgba(0, 0, 0, 0.1);
            margin-top: 5px;
        }
    </style>
</head>

<body>

<?php include "navbar.php" ?>


<div class="container">
    <h2 class="mt-4 mb-4">Booking History</h2>

    <?php
    // Check if any bookings exist
    if ($result->num_rows > 0) {
        echo '<table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Travel Date</th>
                        <th>Total Price</th>
                        <th>Seat Numbers</th>
                        <th>Source</th>
                        <th>Destination</th>
                        <th>Arrival Time</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>';

        while ($row = $result->fetch_assoc()) {
            echo '<tr>
                    <td>' . $row['id'] . '</td>
                    <td>' . $row['travel_date'] . '</td>
                    <td>' . number_format($row['total_price'], 2) . '</td>
                    <td>' . ($row['seat_numbers'] ?: 'N/A') . '</td>
                    <td>' . $row['source'] . '</td>
                    <td>' . $row['destination'] . '</td>
                    <td>' . $row['arrival_time'] . '</td>
                    <td>' . $row['status'] . '</td>
                    <td>';

            // Only show cancel button if the booking is not cancelled
            if ($row['status'] != 'Cancelled') {
                echo '<form method="POST" action="">
                        <input type="hidden" name="booking_id" value="' . $row['id'] . '">
                        <button type="submit" name="cancel_booking" class="btn btn-danger w-100">Cancel Booking</button>
                    </form>';
            } else {
                echo '<button type="submit" name="cancel_booking" class="btn btn-danger disabled w-100">Cancelled</button>';
            }

            echo '</td>
                </tr>';
        }

        echo '</tbody>
            </table>';
    } else {
        echo '<div class="alert alert-warning" role="alert">No bookings found.</div>';
    }

    $stmt->close();
    $conn->close();
    ?>

</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
