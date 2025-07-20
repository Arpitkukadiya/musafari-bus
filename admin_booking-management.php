<?php
session_start();
include 'config.php';
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit;
}

$query = "
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
    ORDER BY b.booking_date DESC;
";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Booking Management</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include "admin_navbar.php"; ?>
<div class="content">

<div class="container">
    <h3 class="mb-4">Booking Management</h3>
    <table class="table table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>Booking ID</th>
                <th>Bus Name</th>
                <th>Travel Date</th>
                <th>Passenger Name</th>
                <th>Seat Number</th>
                <th>Booking Date</th>
                <th>Total Price</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['booking_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['bus_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['travel_date']); ?></td>
                    <td><?php echo htmlspecialchars($row['passenger_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['seat_number']); ?></td>
                    <td><?php echo htmlspecialchars($row['booking_date']); ?></td>
                    <td>₹<?php echo htmlspecialchars($row['total_price']); ?></td>
                    <td>
                        <a href='delete_booking.php?id=<?php echo $row['booking_id']; ?>' class='btn btn-danger btn-sm' 
                           onclick="return confirm('Are you sure you want to delete this booking?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>