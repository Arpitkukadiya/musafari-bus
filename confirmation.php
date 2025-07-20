<?php
include 'config.php';
session_start();

if (!isset($_SESSION['id']) || $_SESSION['role'] != 'customer') {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve booking details from the POST request
    $bus_id = $_POST['bus_id'];
    $travel_date = $_POST['travel_date'];
    $selected_seats = $_POST['selected_seats'];
    $total_amount = $_POST['total_amount'];
    $full_name = $_POST['full_name'];
    $contact_number = $_POST['contact_number'];
    $email = $_POST['email'];
    $user_id = $_SESSION['id'];

    // Validate inputs
    if (empty($bus_id) || empty($travel_date) || empty($selected_seats) || empty($full_name) || empty($contact_number) || empty($email)) {
        die("All fields are required.");
    }

    // Begin transaction to ensure atomicity
    $conn->begin_transaction();

    try {
        // Insert booking details into the bookings table
        $booking_query = $conn->prepare("
            INSERT INTO bookings (user_id, bus_id, full_name, contact_number, email, selected_seats, booking_date)
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");
        $booking_query->bind_param("iissss", $user_id, $bus_id, $full_name, $contact_number, $email, $selected_seats);
        $booking_query->execute();

        $booking_id = $conn->insert_id;

        $seats = explode(",", $selected_seats);
        $seat_query = $conn->prepare("
            INSERT INTO booked_seats (booking_id, seat_id, bus_id, travel_date, is_booked)
            VALUES (?, ?, ?, ?, 1)
        ");

        foreach ($seats as $seat_id) {
            $seat_query->bind_param("iiis", $booking_id, $seat_id, $bus_id, $travel_date);
            $seat_query->execute();
        }

        $conn->commit();

        echo "<div style='text-align: center; margin-top: 50px;'>
                <h2>Booking Confirmed!</h2>
                <p>Booking ID: $booking_id</p>
                <p>Thank you, $full_name! Your booking for the bus <strong>#{$bus_id}</strong> has been confirmed.</p>
                <p>Travel Date: $travel_date</p>
                <p>Seats: $selected_seats</p>
                <p>Total Amount Paid: ₹$total_amount</p>
                <a href='index.php' class='btn btn-primary'>Go to Homepage</a>
              </div>";
    } catch (Exception $e) {
        $conn->rollback();
        die("Error processing booking: " . $e->getMessage());
    }
} else {
    die("Invalid request.");
}
?>
