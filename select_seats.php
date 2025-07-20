<?php
include 'config.php';
session_start();

if (!isset($_SESSION['id']) || $_SESSION['role'] != 'customer') {
    header("Location: index.php");
    exit;
}

$bus_id = $_GET['bus_id'] ?? null;
$travel_date = $_GET['date'] ?? null;
$passengers = $_GET['passengers'] ?? 1; // Default to 1 passenger if not provided

if (!$bus_id || !$travel_date) {
    die("Invalid bus or date.");
}

// Fetch the price for the selected bus
$bus_price_query = $conn->prepare("SELECT price, bus_name FROM buses WHERE bus_id = ?");
$bus_price_query->bind_param("i", $bus_id);
$bus_price_query->execute();
$bus_price_result = $bus_price_query->get_result();
$bus_data = $bus_price_result->fetch_assoc();
$bus_price = $bus_data['price'] ?? 0; // Default to 0 if no price is found
$bus_name = $bus_data['bus_name'] ?? 'Unknown Bus'; // Default to 'Unknown Bus' if no name is found

// Check if seats are already generated for this bus
$check_seats_query = $conn->prepare("SELECT COUNT(*) as seat_count FROM seats WHERE bus_id = ?");
$check_seats_query->bind_param("i", $bus_id);
$check_seats_query->execute();
$check_seats_result = $check_seats_query->get_result();
$seat_data = $check_seats_result->fetch_assoc();

if ($seat_data['seat_count'] == 0) {
    // Generate seats for the bus dynamically
    $rows = 11; // Number of rows
    $columns = ['A', 'B', 'C', 'D', 'E']; // Column labels
    $insert_query = "INSERT INTO seats (bus_id, seat_number, row_number, column_number) VALUES ";
    $values = [];

    $seats = []; // Array to store seat data
    for ($row = 1; $row <= $rows; $row++) {
        foreach ($columns as $index => $column) {
            // For rows 1-10, include all 5 columns; for row 11, include only A and B
            if ($row < 11 || ($row == 11 && $index < 2)) {
                $seat_number = $column . $row;
                $values[] = "($bus_id, '$seat_number', $row, " . ($index + 1) . ")";
                // Store seat number in the array
                $seats[$row][] = $seat_number;
            }
        }
    }

    $insert_query .= implode(", ", $values);
    $conn->query($insert_query);
}

// Fetch seats for the selected bus and date
$seat_query = $conn->prepare("
    SELECT s.seat_id, s.seat_number, s.row_number, s.column_number,
           IFNULL(bs.is_booked, 0) AS is_booked
    FROM seats s
    LEFT JOIN booked_seats bs 
    ON s.seat_id = bs.seat_id AND bs.travel_date = ?
    WHERE s.bus_id = ?
    ORDER BY s.row_number, s.column_number
");
$seat_query->bind_param("si", $travel_date, $bus_id);
$seat_query->execute();
$seats_result = $seat_query->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bus Seat Selection</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f5f7fa;
            font-family: 'Arial', sans-serif;
        }

        .seat-selection-container {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .seat-row {
            display: flex;
            justify-content: center;
            margin-bottom: 10px;
            gap: 10px;
        }

        .seat {
           width: 50px;
            height: 50px;
            border: 2px solid #ccc;
            border-radius: 5px;
            text-align: center;
            line-height: 50px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .seat:hover {
            transform: scale(1.1);
        }

        .selected {
            background-color: #28a745;
            border-color: #28a745;
            color: black;
        }

        .booked {
            background-color: #dc3545;
            color: white;
            border-color: #dc3545;
            cursor: not-allowed;
        }

        .empty {
            background-color: #f8f9fa;
        }

        .gap {
            width: 20px;
        }

        .booking-summary {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        .summary-header {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        footer {
            background-color: #1e63d4;
            color: white;
            text-align: center;
            padding: 15px 0;
            margin-top: 20px;
        }

        @media (max-width: 768px) {
            .seat {
                width: 40px;
                height: 40px;
                line-height: 40px;
                font-size: 12px;
            }

            .seat-row {
                gap: 5px;
            }
        }
    </style>
</head>
<body>
<?php include "navbar.php" ?>

    <div class="container my-5">
        <h2 class="text-center mb-4">Bus Seat Selection</h2>
        <div class="row">
            <!-- Seat Selection -->
            <div class="col-lg-8">
                <div class="seat-selection-container">
                    <?php 
                        $current_row = 0;
                        $seats_array = []; // Initialize the array for the seats
                        while ($seat = $seats_result->fetch_assoc()): 
                            if ($current_row != $seat['row_number']) {
                                if ($current_row != 0) echo '</div>'; // Close previous row
                                echo '<div class="seat-row">'; // Start new row
                                $current_row = $seat['row_number'];
                            }
                    ?>
                        <div class="seat <?= $seat['is_booked'] ? 'booked' : 'empty' ?>"
                             data-seat-id="<?= $seat['seat_id'] ?>"
                             onclick="selectSeat(this)">
                            <?= htmlspecialchars($seat['seat_number']) ?>
                        </div>
                    <?php endwhile; ?>
                    </div>
                </div>
            </div>

            <!-- Booking Summary -->
            <div class="col-lg-4">
                <div class="booking-summary">
                    <div class="summary-header">Booking Summary</div>
                    <p><strong>Bus Name:</strong> <?= htmlspecialchars($bus_name) ?></p>
                    <p><strong>Date:</strong> <?= htmlspecialchars($travel_date) ?></p>
                    <p><strong>Passengers:</strong> <?= htmlspecialchars($passengers) ?></p>
                    <hr>
                    <h5>Total Amount: ₹<span id="total_amount">0</span></h5>
                    <form action="booking_details.php" method="POST">
                        <input type="hidden" name="bus_id" value="<?= $bus_id ?>">
                        <input type="hidden" name="travel_date" value="<?= $travel_date ?>">
                        <input type="hidden" name="passenger_count" value="<?= $passengers ?>">
                        <input type="hidden" id="selected_seats" name="selected_seats">
                        <button type="submit" class="btn btn-primary w-100 mt-3">Proceed to Booking</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 Bus Booking System</p>
    </footer>

    <script>
        const selectedSeats = [];
        const maxPassengers = <?= (int)$passengers ?>;
        const busPrice = <?= (int)$bus_price ?>;

        function calculateTotal() {
            const totalAmount = selectedSeats.length * busPrice;
            document.getElementById('total_amount').textContent = totalAmount;
        }

        function selectSeat(seatDiv) {
            if (seatDiv.classList.contains('booked')) return;

            const seatId = seatDiv.getAttribute('data-seat-id');

            if (seatDiv.classList.contains('selected')) {
                seatDiv.classList.remove('selected');
                const index = selectedSeats.indexOf(seatId);
                if (index > -1) selectedSeats.splice(index, 1);
            } else {
                if (selectedSeats.length < maxPassengers) {
                    seatDiv.classList.add('selected');
                    selectedSeats.push(seatId);
                } else {
                    alert(`You can only select up to ${maxPassengers} seats.`);
                }
            }
            document.getElementById('selected_seats').value = selectedSeats.join(',');
            calculateTotal();
        }
    </script>
</body>
</html>
