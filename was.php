<?php
include 'config.php';
session_start();

if (!isset($_SESSION['id']) || $_SESSION['role'] != 'traveler') {
    header("Location: index.php");
    exit;
}

$traveler_id = $_SESSION['id'];

// Add bus
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_bus'])) {
    $bus_name = $_POST['bus_name'];
    $source = $_POST['source'];
    $destination = $_POST['destination'];
    $departure_time = $_POST['departure_time'];
    $arrival_time = $_POST['arrival_time'];
    $seats_available = $_POST['seats_available'];
    $price = $_POST['price'];

    $stmt = $conn->prepare("INSERT INTO buses (bus_name, source, destination, departure_time, arrival_time, seats_available, price, traveler_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssiii", $bus_name, $source, $destination, $departure_time, $arrival_time, $seats_available, $price, $traveler_id);
    $stmt->execute();
    $stmt->close();
}

// Edit bus
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_bus'])) {
    $bus_id = $_POST['bus_id'];
    $bus_name = $_POST['bus_name'];
    $source = $_POST['source'];
    $destination = $_POST['destination'];
    $departure_time = $_POST['departure_time'];
    $arrival_time = $_POST['arrival_time'];
    $seats_available = $_POST['seats_available'];
    $price = $_POST['price'];

    $stmt = $conn->prepare("UPDATE buses SET bus_name = ?, source = ?, destination = ?, departure_time = ?, arrival_time = ?, seats_available = ?, price = ? WHERE bus_id = ? AND traveler_id = ?");
    $stmt->bind_param("sssssiisi", $bus_name, $source, $destination, $departure_time, $arrival_time, $seats_available, $price, $bus_id, $traveler_id);
    $stmt->execute();
    $stmt->close();
}

// Delete bus
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_bus'])) {
    $bus_id = $_POST['bus_id'];
    $stmt = $conn->prepare("DELETE FROM buses WHERE bus_id = ? AND traveler_id = ?");
    $stmt->bind_param("ii", $bus_id, $traveler_id);
    $stmt->execute();
    $stmt->close();
}

// Fetch traveler buses
$buses_query = $conn->prepare("SELECT * FROM buses WHERE traveler_id = ?");
$buses_query->bind_param("i", $traveler_id);
$buses_query->execute();
$buses_result = $buses_query->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Traveler Panel</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include "traveler_navbar.php"; ?>
<div class="content">

<div class="container">
    <h3 class="mb-3">Your Buses</h3>
    <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#addBusModal">Add New Bus</button>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Bus Name</th>
                <th>Source</th>
                <th>Destination</th>
                <th>Departure Time</th>
                <th>Arrival Time</th>
                <th>Seats Available</th>
                <th>Price (₹ per seat)</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($bus = $buses_result->fetch_assoc()): ?>
                <tr>
                    <td><?= $bus['bus_name']; ?></td>
                    <td><?= $bus['source']; ?></td>
                    <td><?= $bus['destination']; ?></td>
                    <td><?= $bus['departure_time']; ?></td>
                    <td><?= $bus['arrival_time']; ?></td>
                    <td><?= $bus['seats_available']; ?></td>
                    <td>₹<?= $bus['price']; ?></td>
                    <td>
                        <button class="btn btn-warning" data-toggle="modal" data-target="#editBusModal<?= $bus['bus_id']; ?>">Edit</button>
                        <button class="btn btn-danger" data-toggle="modal" data-target="#deleteBusModal<?= $bus['bus_id']; ?>">Delete</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
 <!-- Edit Bus Modal -->
 <div class="modal fade" id="editBusModal<?php echo $bus['bus_id']; ?>" tabindex="-1" aria-labelledby="editBusModalLabel<?php echo $bus['bus_id']; ?>" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editBusModalLabel<?php echo $bus['bus_id']; ?>">Edit Bus</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form method="post">
                                    <div class="modal-body">
                                        <input type="hidden" name="bus_id" value="<?php echo $bus['bus_id']; ?>">
                                        <div class="form-group">
                                            <label for="bus_name">Bus Name</label>
                                            <input type="text" class="form-control" name="bus_name" value="<?php echo $bus['bus_name']; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="source">Source</label>
                                            <input type="text" class="form-control" name="source" value="<?php echo $bus['source']; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="destination">Destination</label>
                                            <input type="text" class="form-control" name="destination" value="<?php echo $bus['destination']; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="arrival_time">Arrival Time</label>
                                            <input type="time" class="form-control" name="arrival_time" value="<?php echo $bus['arrival_time']; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="departure_time">Departure Time</label>
                                            <input type="time" class="form-control" name="departure_time" value="<?php echo $bus['departure_time']; ?>" required>
                                        </div>
                                       
                                        <div class="form-group">
                                            <label for="seats_available">Seats Available</label>
                                            <input type="number" class="form-control" name="seats_available" value="<?php echo $bus['seats_available']; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="price">Price (per seat)</label>
                                            <input type="number" class="form-control" name="price" value="<?php echo $bus['price']; ?>" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button type="submit" name="update_bus" class="btn btn-primary">Update Bus</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Delete Bus Modal -->
                    <div class="modal fade" id="deleteBusModal<?php echo $bus['bus_id']; ?>" tabindex="-1" aria-labelledby="deleteBusModalLabel<?php echo $bus['bus_id']; ?>" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deleteBusModalLabel<?php echo $bus['bus_id']; ?>">Delete Bus</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form method="post">
                                    <div class="modal-body">
                                        <p>Are you sure you want to delete this bus?</p>
                                        <input type="hidden" name="bus_id" value="<?php echo $bus['bus_id']; ?>">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button type="submit" name="delete_bus" class="btn btn-danger">Delete Bus</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    </div>

                    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
