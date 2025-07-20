<?php
include 'config.php';
session_start();

if (!isset($_SESSION['id']) || $_SESSION['role'] != 'customer') {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['id'];
$bus_id = $_POST['bus_id'] ?? null;
$travel_date = $_POST['travel_date'] ?? null;
$selected_seats = $_POST['selected_seats'] ?? null;
$passenger_count = $_POST['passenger_count'] ?? null;

if (!$bus_id || !$travel_date || !$selected_seats || !$passenger_count) {
    die("Incomplete booking information.");
}

// Get the arrival time from the bus details
$bus_query = $conn->prepare("SELECT price, bus_name, source, destination, arrival_time FROM buses WHERE bus_id = ?");
$bus_query->bind_param("i", $bus_id);
$bus_query->execute();
$bus_result = $bus_query->get_result();
$bus_data = $bus_result->fetch_assoc();
$bus_price = $bus_data['price'] ?? 0;
$bus_name = $bus_data['bus_name'] ?? "Unknown Bus";
$source = $bus_data['source'];
$destination = $bus_data['destination'];
$arrival_time = $bus_data['arrival_time'];  // Fetching arrival time










$total_price = $bus_price * $passenger_count;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_booking'])) {
    $conn->begin_transaction();
    try {
 // Insert booking into the database, including arrival time
$insert_booking = $conn->prepare("INSERT INTO bookings (user_id, bus_id, travel_date, total_price, source, destination, arrival_time) VALUES (?, ?, ?, ?, ?, ?, ?)");
$insert_booking->bind_param("iisdsss", $user_id, $bus_id, $travel_date, $total_price, $source, $destination, $arrival_time);
$insert_booking->execute();
$booking_id = $conn->insert_id;


        // Insert passenger details
        $passenger_names = $_POST['passenger_name'];
        $passenger_ages = $_POST['passenger_age'];
        $passenger_genders = $_POST['passenger_gender'];
        $passenger_emails = $_POST['passenger_email'];
        $passenger_contacts = $_POST['passenger_contact'];

        $insert_passenger = $conn->prepare("INSERT INTO passengers (booking_id, name, age, gender, email, contact_number) VALUES (?, ?, ?, ?, ?, ?)");

        foreach ($passenger_names as $index => $name) {
            $age = $passenger_ages[$index];
            $gender = $passenger_genders[$index];
            $email = $passenger_emails[$index];
            $contact_number = $passenger_contacts[$index];

            $insert_passenger->bind_param("isisss", $booking_id, $name, $age, $gender, $email, $contact_number);
            $insert_passenger->execute();
        }

        // Process payment
        $cardholder_name = $_POST['cardholder'];
        $card_number = $_POST['cardNumber'];
        $expiry_date = $_POST['expiryDate'];
        $cvv = $_POST['cvv'];
        $card_type = $_POST['cardType'];

        $insert_payment = $conn->prepare("INSERT INTO payments (booking_id, cardholder_name, card_number, expiry_date, cvv, card_type, payment_status) VALUES (?, ?, ?, ?, ?, ?, 'Completed')");
        $insert_payment->bind_param("isssss", $booking_id, $cardholder_name, $card_number, $expiry_date, $cvv, $card_type);
        $insert_payment->execute();




        // Insert booked seats
        $seats = explode(',', $selected_seats);
        $insert_seat = $conn->prepare("INSERT INTO booked_seats (booking_id, seat_id, bus_id, travel_date, is_booked) VALUES (?, ?, ?, ?, 1)");
        foreach ($seats as $seat_id) {
            $seat_id = intval($seat_id);
            $insert_seat->bind_param("iiis", $booking_id, $seat_id, $bus_id, $travel_date);
            $insert_seat->execute();
        }

        $conn->commit();
        echo "<script>window.location.href = 'success.php?booking_id=" . $booking_id . "';</script>";
    } catch (Exception $e) {
        $conn->rollback();
        echo "<script>alert('Booking failed. Please try again.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Checkout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }



        .form-container,
        .payment-container {
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
            padding: 30px;
            border-radius: 15px;
            background-color: white;
            margin-bottom: 30px;
        }

        .form-control {
            border-radius: 10px;
            padding: 10px;
            box-shadow: inset 0 1px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 5px;
            margin-top: 25px;
        }


        .btn-dark {
            margin-top: 20px;
            width: 100%;
            background-color: #1e63d4;
            color: white;
            font-size: 1.2rem;
            border: none;
        }

        .btn-dark:hover {
            background-color: #1e63d4;
            color: white;
        }

        .bus-table-card {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        footer {
            background-color: #1978a0 !important;
            color: #fff;
            padding: 20px 0;
            text-align: center;
            margin-top: 30px;
        }

        footer p {
            margin-bottom: 0;
        }

        /* Mobile styles */
        @media (max-width: 576px) {

            .form-container,
            .payment-container {
                padding: 20px;
            }

            .btn-dark {
                width: 100%;
                font-size: 1rem;
            }

            .bus-table-card {
                margin-top: 30px;
            }
        }
    </style>

</head>

<body>
    <?php include "navbar.php" ?>

    <div class="container mt-4">

        <div class="row">
            <!-- Booking Form -->
            <div class="col-md-6">
                <div class="form-container">
                    <h4>Booking Details</h4>

                    <form method="POST" action="">
                        <input type="hidden" name="bus_id" value="<?= $bus_id ?>">
                        <input type="hidden" name="travel_date" value="<?= $travel_date ?>">
                        <input type="hidden" name="selected_seats" value="<?= $selected_seats ?>">
                        <input type="hidden" name="passenger_count" value="<?= $passenger_count ?>">
                        <div class="passenger-details">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="passenger_name[]" id="passenger_name_<?= $i ?>" placeholder="Enter your full name" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input type="email" class="form-control" name="passenger_email[]" placeholder="Enter your email" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input type="number" class="form-control" name="passenger_age[]" id="passenger_age_<?= $i ?>" min="0" placeholder="Enter your Age" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">

                                        <div class="form-group">
                                            <input type="text" class="form-control" name="passenger_contact[]" placeholder="Enter your contact number" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <select class="form-control" name="passenger_gender[]" id="passenger_gender_<?= $i ?>" required>
                                        <option value="">Select Gender</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                </div>
                                <h4 class="mt-4">Payment Details</h4>

                                <div class="form-group">
                                    <select class="form-control" name="cardType" id="cardType">
                                        <option value="Credit Card">Credit Card</option>
                                        <option value="Debit Card">Debit Card</option>
                                    </select>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">

                                        <div class="form-group">
                                            <input type="text" class="form-control" name="cardholder" id="cardholder" placeholder="Enter cardholder's name" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">

                                        <div class="form-group">
                                            <input type="text" class="form-control" name="cardNumber" id="cardNumber" placeholder="Enter card number" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="expiryDate" id="expiryDate" placeholder="MM/YY" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="cvv" id="cvv" placeholder="Enter CVV" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" name="confirm_booking" class="btn btn-dark btn-block" style="            background-color: #1e63d4;
            color: white;border:none;
">Confirm Booking</button>
                    </form>
                </div>
            </div>

        </div>
        <div class="col-md-6">
            <div class="bus-table-card mb-4">
                <h3 class="mb-4">Bus Details
                    <hr>
                </h3>
                <table class="table table-bordered">
                    <tr>
                        <th>Bus Name</th>
                        <td><?= htmlspecialchars($bus_name) ?></td>
                    </tr>
                    <tr>
                        <th>Travel Date:</th>
                        <td><?= htmlspecialchars($travel_date) ?></td>
                    </tr>
                    <tr>
                        <th>Total Price:</th>
                        <td> ₹<?= htmlspecialchars($total_price) ?></td>
                    </tr>
                    <tr>
    <th>Source</th>
    <td><?= htmlspecialchars($source) ?></td>
</tr>
<tr>
    <th>Destination</th>
    <td><?= htmlspecialchars($destination) ?></td>
</tr>

                </table>
            </div>
        </div>
    </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>