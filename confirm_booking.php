<?php
include 'config.php';
session_start();

if (!isset($_SESSION['id']) || $_SESSION['role'] != 'customer') {
    header("Location: index.php");
    exit;
}

$bus_id = $_POST['bus_id'];
$travel_date = $_POST['travel_date'];
$selected_seats = explode(',', $_POST['selected_seats']);

if (!$bus_id || !$travel_date || empty($selected_seats)) {
    die("Invalid booking data.");
}

// Insert booked seats
$conn->begin_transaction();
try {
    $booking_query = $conn->prepare("
        INSERT INTO booked_seats (seat_id, bus_id, travel_date)
        VALUES (?, ?, ?)
    ");
    foreach ($selected_seats as $seat_id) {
        $booking_query->bind_param("iis", $seat_id, $bus_id, $travel_date);
        $booking_query->execute();
    }
    $conn->commit();
    echo "Booking successful!";
} catch (Exception $e) {
    $conn->rollback();
    echo "Booking failed: " . $e->getMessage();
}
?>
