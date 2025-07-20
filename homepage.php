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
    <title>Search Bus</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Search Bus</h2>
    <form method="post">
        <div class="form-group">
            <label for="source">Source</label>
            <input type="text" class="form-control" id="source" name="source" required>
        </div>
        <div class="form-group">
            <label for="destination">Destination</label>
            <input type="text" class="form-control" id="destination" name="destination" required>
        </div>
        <div class="form-group">
            <label for="travel_date">Travel Date</label>
            <input type="date" class="form-control" id="travel_date" name="travel_date" required>
        </div>
        <div class="form-group">
            <label for="passengers">Number of Passengers</label>
            <input type="number" class="form-control" id="passengers" name="passengers" min="1" required>
        </div>
        <button type="submit" class="btn btn-primary">Search</button>
    </form>

    <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && $result->num_rows > 0): ?>
        <h3 class="mt-5">Available Buses</h3>
        <table class="table table-striped mt-3">
            <thead>
            <tr>
                <th>Bus Name</th>
                <th>Departure</th>
                <th>Arrival</th>
                <th>Seats Available</th>
                <th>Price</th>
                <th>Book</th>
            </tr>
            </thead>
            <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <?php if ($row['seats_available'] >= $passengers): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['bus_name']) ?></td>
                        <td><?= htmlspecialchars($row['departure_time']) ?></td>
                        <td><?= htmlspecialchars($row['arrival_time']) ?></td>
                        <td><?= htmlspecialchars($row['seats_available']) ?></td>
                        <td><?= htmlspecialchars($row['price']) ?></td>
                        <td><a href="book_bus.php?bus_id=<?= $row['bus_id'] ?>&date=<?= $travel_date ?>&passengers=<?= $passengers ?>" class="btn btn-success">Book</a></td>
                    </tr>
                <?php endif; ?>
            <?php endwhile; ?>
            </tbody>
        </table>
    <?php elseif ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
        <p class="mt-5 text-danger">No buses available for the selected route and date with sufficient seats.</p>
    <?php endif; ?>
</div>
</body>
</html>
