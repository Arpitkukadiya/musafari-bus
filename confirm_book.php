<?php
include 'config.php';
session_start();

if (!isset($_SESSION['id']) || $_SESSION['role'] != 'customer') {
    header("Location: index.php");
    exit;
}

$bus_id = $_POST['bus_id'] ?? null;
$travel_date = $_POST['travel_date'] ?? null;
$selected_seats = $_POST['selected_seats'] ?? null;

if (!$bus_id || !$travel_date || !$selected_seats) {
    die("Incomplete seat selection.");
}

// Decode the selected seats (JSON string to array)
$selected_seats = json_decode($selected_seats, true);

// Check if the selected seats exist in the seats table
$seat_ids = implode(",", array_map('intval', $selected_seats)); // Make sure the seat IDs are integers
$seat_check_query = $conn->prepare("SELECT seat_id FROM seats WHERE bus_id = ? AND seat_id IN ($seat_ids)");
$seat_check_query->bind_param("i", $bus_id);
$seat_check_query->execute();
$seat_check_result = $seat_check_query->get_result();

$available_seats = [];
while ($row = $seat_check_result->fetch_assoc()) {
    $available_seats[] = $row['seat_id'];
}

if (count($available_seats) !== count($selected_seats)) {
    die("One or more of the selected seats do not exist for this bus.");
}

// Proceed with booking if all seats are valid
foreach ($selected_seats as $seat_id) {
    // Insert into booked_seats table
    $booking_query = $conn->prepare("INSERT INTO booked_seats (seat_id, bus_id, travel_date, user_id) VALUES (?, ?, ?, ?)");
    $booking_query->bind_param("iiis", $seat_id, $bus_id, $travel_date, $_SESSION['id']);
    if (!$booking_query->execute()) {
        die("Booking failed for seat ID: $seat_id");
    }
}

// Redirect to a confirmation page or success page
header("Location: booking_success.php");
exit;
?>
