<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

<style>
    body {
        font-family: 'Arial', sans-serif;
        background-color: #f4f5f7;
        margin: 0;
    }

    .sidebar {
        background-color: #2d6a4f; /* Green background */
        color: white;
        width: 280px;
        height: 100vh;
        position: fixed;
        padding-top: 20px;
        box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        transition: width 0.3s;
    }

    .sidebar a {
        color: white;
        text-decoration: none;
        display: block;
        padding: 15px;
        font-size: 18px;
        border-bottom: 1px solid #4b4f56;
    }

    .sidebar a:hover {
        background-color: #388e3c; /* Darker green on hover */
        border-radius: 5px;
    }

    .sidebar .active {
        background-color: #1b5e20; /* Active link color */
    }

    .sidebar i {
        margin-right: 10px;
    }

    .content {
        margin-left: 260px;
        padding: 30px;
    }
</style>

<!-- Sidebar -->
<div class="sidebar">
    <h3 class="text-center text-white">Admin Panel</h3><br>
    <a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i>Dashboard</a>
    <a href="admin_user-management.php"><i class="fas fa-users"></i>Manages users</a>
    <a href="admin_traveller-management.php"><i class="fas fa-user-tie"></i>Manage Agent</a>
    <a href="admin_bus-management.php"><i class="fas fa-bus"></i>Manages Bus</a>
    <a href="admin_booking-management.php"><i class="fas fa-calendar-check"></i>Manages Booking</a>
    <a href="admin_report.php"><i class="fas fa-calendar-check"></i>Manages Reports</a>
    <a href="logout.php" class="text-danger"><i class="fas fa-sign-out-alt"></i>Logout</a>
</div>
