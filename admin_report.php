<?php
session_start();
include 'config.php';
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit;
}

$query = "
    SELECT b.id AS booking_id, b.user_id, b.travel_date, b.total_price, b.booking_date, 
        p.name AS passenger_name, s.seat_number, bus.bus_name
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
    <table class="table table-bordered" id="bookingTable">
        <thead class="thead-dark">
            <tr>
                <th>Booking ID</th>
                <th>Bus Name</th>
                <th>Travel Date</th>
                <th>Passenger Name</th>
                <th>Seat Number</th>
                <th>Booking Date</th>
                <th>Total Price</th>
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
                    <td><?php echo htmlspecialchars($row['total_price']); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <button class="btn btn-success mb-3" onclick="downloadPDF()">Download PDF</button>

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
function downloadPDF() {
    const { jsPDF } = window.jspdf;
    let doc = new jsPDF('p', 'pt', 'a4');
    let table = document.querySelector("#bookingTable");
    html2canvas(table).then(canvas => {
        let imgData = canvas.toDataURL("image/png");
        let imgWidth = 500;
        let imgHeight = (canvas.height * imgWidth) / canvas.width;
        doc.text("Booking Management", 40, 40);
        doc.addImage(imgData, 'PNG', 40, 60, imgWidth, imgHeight);
        doc.save("Booking_Report.pdf");
    });
}
</script>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
