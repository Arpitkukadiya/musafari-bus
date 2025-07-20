<?php
include 'config.php';
session_start();

if (!isset($_SESSION['id']) || $_SESSION['role'] != 'customer') {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $source = $_POST['source'];
    $destination = $_POST['destination'];
    $travel_date = $_POST['travel_date'];
    $passengers = $_POST['passengers'];

    // Prepare query to fetch buses for the given route
    $stmt = $conn->prepare("SELECT * FROM buses WHERE source = ? AND destination = ?");
    $stmt->bind_param("ss", $source, $destination);
    $stmt->execute();
    $result = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Buses</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .section-title {
            color: #1e63d4;
            margin-top: 30px;
            font-weight: bold;
            text-align: center;
        }

        .bus-card {
            padding: 20px;
            margin-top: 20px;
            border-radius: 25px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .card-title {
            font-size: 1.3rem;
            font-weight: bold;
            border-bottom: 1px solid #1e63d4;
            text-align: center;
            padding-bottom: 10px;
        }

        .card-text {
            font-size: 1.08rem;
            padding: 1px;
        }

        .btn-custom {
            background-color: #1e63d4;
            border: none;
            font-size: 1.1rem;
            border-radius: 20px;
            width: 100%;
        }

        .btn:hover {
            background-color: #1e63d4;
        }
    </style>
</head>

<body>
    <?php include "navbar.php" ?>
    <div class="container">
        <h3 class="section-title">Available Buses</h3>
        <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && $result->num_rows > 0): ?>
            <div class="row">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <?php
                    // Get the total number of seats in the bus
                    $bus_id = $row['bus_id'];
                    $seats_query = $conn->prepare("SELECT seats_available FROM buses WHERE bus_id = ?");
                    $seats_query->bind_param("i", $bus_id);
                    $seats_query->execute();
                    $seats_result = $seats_query->get_result();
                    $total_seats = $seats_result->fetch_assoc()['seats_available'];

                    // Get the number of booked seats for the selected travel date
                    $booked_seats_query = $conn->prepare("
                    SELECT COUNT(*) as booked_seats
                    FROM booked_seats 
                    WHERE bus_id = ? AND travel_date = ?");
                    $booked_seats_query->bind_param("is", $bus_id, $travel_date);
                    $booked_seats_query->execute();
                    $booked_seats_result = $booked_seats_query->get_result();
                    $booked_seats = $booked_seats_result->fetch_assoc()['booked_seats'];

                    // Calculate the remaining available seats
                    $remaining_seats = $total_seats - $booked_seats;
                    ?>
                    <div class="col-md-4 col-12 mb-4">
                        <div class="card bus-card">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($row['bus_name']) ?></h5>
                                <p class="card-text"><strong>Source:</strong> <?= htmlspecialchars($source) ?></p>
                                <p class="card-text"><strong>Destination:</strong> <?= htmlspecialchars($destination) ?></p>
                                <p class="card-text"><strong>Departure:</strong> <?= htmlspecialchars($row['departure_time']) ?></p>
                                <p class="card-text"><strong>Arrival:</strong> <?= htmlspecialchars($row['arrival_time']) ?></p>
                                <p class="card-text"><strong>Seats Available:</strong> <?= $remaining_seats > 0 ? $remaining_seats : 0 ?></p>
                                <p class="card-text"><strong>Price:</strong> ₹<?= htmlspecialchars($row['price']) ?></p>
                                <a href="select_seats.php?bus_id=<?= $row['bus_id'] ?>&date=<?= $travel_date ?>&passengers=<?= $passengers ?>"
                                    class="btn btn-primary">Book Now</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="text-danger">No buses available for the selected route and date with sufficient seats.</p>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
