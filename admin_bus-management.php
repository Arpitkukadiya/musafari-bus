<?php
session_start();
include 'config.php';
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit;
}

if (isset($_GET['delete'])) {
    $bus_id = $_GET['delete'];
    $conn->query("DELETE FROM buses WHERE bus_id=$bus_id");
    header("Location: admin_bus-management.php");
}

// Fetch all buses
$result = $conn->query("SELECT buses.*, users.name AS user_name FROM buses LEFT JOIN users ON buses.traveler_id = users.id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bus Management</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include "admin_navbar.php"; ?>
<div class="content">

<div class="container">
    <h2 class="mb-4">Bus Management</h2>
    <table class="table table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Bus Name</th>
                <th>Traveler Name</th>
                <th>Source</th>
                <th>Destination</th>
                <th>Departure Time</th>
                <th>Arrival Time</th>
                <th>Seats Available</th>
                <th>Price</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['bus_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['bus_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['user_name'] ? $row['user_name'] : 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($row['source']); ?></td>
                    <td><?php echo htmlspecialchars($row['destination']); ?></td>
                    <td><?php echo htmlspecialchars($row['departure_time']); ?></td>
                    <td><?php echo htmlspecialchars($row['arrival_time']); ?></td>
                    <td><?php echo htmlspecialchars($row['seats_available']); ?></td>
                    <td>₹<?php echo htmlspecialchars($row['price']); ?></td>
                    <td>
                        <a href="?delete=<?php echo $row['bus_id']; ?>" class="btn btn-danger btn-sm"
                           onclick="return confirm('Are you sure you want to delete this bus?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>