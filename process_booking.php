<?php
include 'config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $bus_id = $_POST['bus_id'];
    $travel_date = $_POST['travel_date'];
    $passengers = $_POST['passengers'];
    $selected_seats = explode(',', $_POST['selected_seats']);
    $customer_id = $_SESSION['id'];

    // Insert bookings into the database
    $stmt = $conn->prepare("INSERT INTO bookings (bus_id, travel_date, seat_number, customer_id) VALUES (?, ?, ?, ?)");
    foreach ($selected_seats as $seat) {
        $stmt->bind_param("isis", $bus_id, $travel_date, $seat, $customer_id);
        $stmt->execute();
    }

    // Update seats available in the buses table
    $stmt = $conn->prepare("UPDATE buses SET seats_available = seats_available - ? WHERE bus_id = ?");
    $stmt->bind_param("ii", $passengers, $bus_id);
    $stmt->execute();

    header("Location: confirmation.php");
    exit;
} else {
    header("Location: search_bus.php");
    exit;
}
?>
