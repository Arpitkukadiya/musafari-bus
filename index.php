<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT id, name, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $name, $hashed_password, $role);

    if ($stmt->num_rows > 0) {
        $stmt->fetch();
        // Verify password
        if (password_verify($password, $hashed_password)) {
            $_SESSION['id'] = $id;
            $_SESSION['name'] = $name;
            $_SESSION['role'] = $role;

            // Role-based redirection
            if ($role == 'admin') {
                header("Location: admin_dashboard.php");
            } elseif ($role == 'traveler') {
                header("Location: traveller_dashboard.php");
            } else {
                header("Location: search_bus.php");
            }
            exit;
        } else {
            echo "Invalid password!";
        }
    } else {
        echo "No user found with this email!";
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
    <title>Login</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="login1.css">
</head>

<body>

<div class="register-card">
<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="login-card">
        <h2 class="text-center mb-4" style="color: #2575fc;">Login</h2>
                <form action="" method="post">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-custom w-100">Login</button>
                </form>
                <p class="mt-4 text-center"style="font-size: large;">Don't have an account? <a href="register.php">Register here</a></p>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

</body>

</html>
