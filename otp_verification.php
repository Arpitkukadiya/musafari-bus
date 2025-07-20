<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $entered_otp = $_POST['otp'];

    // Check if OTP matches
    if ($entered_otp == $_SESSION['otp']) {
        // OTP is correct, update user status as verified
        $email = $_SESSION['email'];

        // Update user's status to "verified" (optional, add a field like is_verified in users table)
        $stmt = $conn->prepare("UPDATE users SET is_verified = 1 WHERE email = ?");
        $stmt->bind_param("s", $email);

        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>Registration successful! Your account is now verified.</div>";
            header("Location: index.php"); // Redirect to login page
        } else {
            echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Invalid OTP. Please try again.</div>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="login1.css">
</head>
<body>
<div class="register-card">
    <div class="container">
        <h2 class="text-center">Verify Your OTP</h2>
        <form action="otp_verification.php" method="post">
            <div class="form-group">
                <label for="otp">Enter OTP</label>
                <input type="text" class="form-control" id="otp" name="otp" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Verify OTP</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>
