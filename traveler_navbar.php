<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

<style>
    body {
        font-family: 'Arial', sans-serif;
        background-color: #f4f5f7;
        margin: 0;
    }

    .sidebar {
        background: linear-gradient(135deg, #2d6a4f, #1b5e20); /* Gradient background */
        color: white;
        width: 250px;
        height: 100vh;
        position: fixed;
        top: 0;
        left: 0;
        padding-top: 30px;
        box-shadow: 4px 0 15px rgba(0, 0, 0, 0.2);
        transition: width 0.3s;
        z-index: 100;
    }

    .sidebar h3 {
        text-align: center;
        font-size: 22px;
        margin-bottom: 30px;
        font-weight: 600;
    }

    .sidebar a {
        color: white;
        text-decoration: none;
        display: block;
        padding: 15px 20px;
        font-size: 18px;
        border-radius: 5px;
        margin: 5px 0;
        transition: background-color 0.3s, padding-left 0.3s;
    }

    .sidebar a:hover {
        background-color: #388e3c;
        padding-left: 30px; /* Indent the link on hover */
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .sidebar a.active {
        background-color: #1b5e20;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .sidebar i {
        margin-right: 15px;
        font-size: 20px;
    }

    .content {
        margin-left: 250px;
        padding: 30px;
        transition: margin-left 0.3s;
    }

    .sidebar a.logout {
        background-color: #e74c3c; /* Red logout button */
        margin-top: auto;
        font-weight: bold;
        color: white;
    }

    .sidebar a.logout:hover {
        background-color: #c0392b;
    }
</style>

<!-- Sidebar -->
<div class="sidebar">
    <h3>Agent Panel</h3>
    <a href="traveller_dashboard.php"><i class="fas fa-tachometer-alt"></i>Dashboard</a>
    <a href="traveler_bus-management.php"><i class="fas fa-bus"></i>Manages Bus</a>
    <a href="traveler_booking-management.php"><i class="fas fa-calendar-check"></i>Manages Booking</a>
    <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i>Logout</a>
</div>
