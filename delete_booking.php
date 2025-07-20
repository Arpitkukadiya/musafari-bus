<?php
include 'config.php'; // Database connection file

// Check if 'id' is provided in the URL
if (isset($_GET['id'])) {
    $booking_id = $_GET['id'];

    // Start a transaction to ensure both deletes happen together
    $conn->begin_transaction();

    try {
        // Step 1: Delete related passengers
        $deletePassengersQuery = "DELETE FROM passengers WHERE booking_id = ?";
        if ($stmt = $conn->prepare($deletePassengersQuery)) {
            $stmt->bind_param("i", $booking_id);
            $stmt->execute();
            $stmt->close();
        }

        // Step 2: Delete the booking
        $deleteBookingQuery = "DELETE FROM bookings WHERE id = ?";
        if ($stmt = $conn->prepare($deleteBookingQuery)) {
            $stmt->bind_param("i", $booking_id);
            $stmt->execute();
            $stmt->close();
        }

        // Commit the transaction
        $conn->commit();

        // Redirect to the admin booking management page after deletion
        header("Location: admin_booking-management.php");
        exit();

    } catch (Exception $e) {
        // Rollback transaction if something goes wrong
        $conn->rollback();
        echo "Error deleting booking: " . $e->getMessage();
    }
}

// Close the database connection
$conn->close();
?>
