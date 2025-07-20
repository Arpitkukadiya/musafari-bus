<?php
include 'config.php';
session_start();

if (!isset($_SESSION['id']) || $_SESSION['role'] != 'customer') {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['id'];
$bus_id = $_POST['bus_id'];
$travel_date = $_POST['travel_date'];
$total_amount = $_POST['total_amount'];
$selected_seats = explode(',', $_POST['selected_seats']);
$passenger_names = $_POST['passenger_name'];
$passenger_ages = $_POST['passenger_age'];
$passenger_genders = $_POST['passenger_gender'];
$seat_ids = $_POST['seat_id'];
$payment_method = $_POST['payment_method'];

// Start transaction
$conn->begin_transaction();
try {
    // Insert into bookings table
    $booking_query = $conn->prepare("INSERT INTO bookings (user_id, bus_id, travel_date, total_amount) VALUES (?, ?, ?, ?)");
    $booking_query->bind_param("iisd", $user_id, $bus_id, $travel_date, $total_amount);
    $booking_query->execute();
    $booking_id = $conn->insert_id;

    // Insert passenger details
    $passenger_query = $conn->prepare("INSERT INTO passengers (booking_id, name, age, gender, seat_id) VALUES (?, ?, ?, ?, ?)");
    foreach ($passenger_names as $index => $name) {
        $age = $passenger_ages[$index];
        $gender = $passenger_genders[$index];
        $seat_id = $seat_ids[$index];
        $passenger_query->bind_param("isisi", $booking_id, $name, $age, $gender, $seat_id);
        $passenger_query->execute();
    }

    // Insert payment details
    $payment_query = $conn->prepare("INSERT INTO payments (booking_id, payment_amount, payment_method, status) VALUES (?, ?, ?, 'completed')");
    $payment_query->bind_param("ids", $booking_id, $total_amount, $payment_method);
    $payment_query->execute();

    // Commit transaction
    $conn->commit();
    echo "Booking confirmed!";
} catch (Exception $e) {
    $conn->rollback();
    die("Error: " . $e->getMessage());
}
?>
