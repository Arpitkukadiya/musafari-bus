<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$bus_id = $_POST['bus_id'] ?? null;
$travel_date = $_POST['travel_date'] ?? null;
$selected_seats = $_POST['selected_seats'] ?? null;
$full_name = $_POST['full_name'] ?? null;
$email = $_POST['email'] ?? null;
$card_number = $_POST['card_number'] ?? null;
$cardholder_name = $_POST['cardholder_name'] ?? null;
$expiry_date = $_POST['expiry_date'] ?? null;
$cvv = $_POST['cvv'] ?? null;

if (!$bus_id || !$travel_date || !$selected_seats || !$full_name || !$email || !$card_number || !$cardholder_name || !$expiry_date || !$cvv) {
    die("All fields are required.");
}

// Insert booking details into `bookings` table
$query = "INSERT INTO bookings (user_id, bus_id, travel_date, selected_seats, total_price) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($query);
$total_price = 500 * count(explode(',', $selected_seats)); // Replace 500 with actual price calculation logic
$stmt->bind_param("iissd", $_SESSION['user_id'], $bus_id, $travel_date, $selected_seats, $total_price);
$stmt->execute();
$booking_id = $stmt->insert_id;

// Insert payment details
$payment_query = "INSERT INTO payments (booking_id, card_number, cardholder_name, expiry_date, cvv) VALUES (?, ?, ?, ?, ?)";
$payment_stmt = $conn->prepare($payment_query);
$payment_stmt->bind_param("issss", $booking_id, $card_number, $cardholder_name, $expiry_date, $cvv);
$payment_stmt->execute();

// Update payment status
$update_query = "UPDATE bookings SET payment_status = 'completed' WHERE booking_id = ?";
$update_stmt = $conn->prepare($update_query);
$update_stmt->bind_param("i", $booking_id);
$update_stmt->execute();

echo "Booking successful. Your payment has been processed.";
?>
