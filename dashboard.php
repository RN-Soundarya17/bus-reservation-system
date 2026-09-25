<?php

session_start();

require_once "../config/db_connection.php";

// Check admin login
if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit();
}

// Get total users
$user_result = $conn->query("SELECT COUNT(*) AS total FROM users");
$total_users = $user_result->fetch_assoc()["total"];

// Get total buses
$bus_result = $conn->query("SELECT COUNT(*) AS total FROM buses");
$total_buses = $bus_result->fetch_assoc()["total"];

// Get total routes
$route_result = $conn->query("SELECT COUNT(*) AS total FROM routes");
$total_routes = $route_result->fetch_assoc()["total"];

// Get total confirmed bookings
$booking_result = $conn->query(
    "SELECT COUNT(*) AS total FROM bookings WHERE status = 'Confirmed'"
);
$total_bookings = $booking_result->fetch_assoc()["total"];

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Dashboard - Bus Reservation</title>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        body {
            background: #f4f7fb;
            min-height: 100vh;
        }

        /* Navbar */

        .navbar {
            background: #222;
            color: white;
            padding: 16px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar h2 {
            font-size: 22px;
        }

        .navbar-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .navbar-right span {
            font-size: 15px;
        }

        .logout-btn {
            text-decoration: none;
            background: #dc3545;
            color: white;
            padding: 9px 15px;
            border-radius: 6px;
        }

        .logout-btn:hover {
            background: #b02a37;
        }

        /* Main */

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 35px auto;
        }

        .welcome {
            margin-bottom: 25px;
        }

        .welcome h1 {
            color: #222;
            margin-bottom: 8px;
        }

        .welcome p {
            color: #666;
        }

        /* Statistics */

        .stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 35px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.08);
            text-align: center;
        }

        .stat-card h3 {
            color: #666;
            margin-bottom: 12px;
            font-size: 16px;
        }

        .stat-card .number {
            font-size: 32px;
            font-weight: bold;
            color: #007bff;
        }

        /* Management */

        .management h2 {
            margin-bottom: 20px;
            color: #222;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .menu-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.08);
        }

        .menu-card h3 {
            margin-bottom: 10px;
            color: #222;
        }

        .menu-card p {
            color: #777;
            margin-bottom: 18px;
            line-height: 1.5;
        }

        .menu-card a {
            display: inline-block;
            text-decoration: none;
            background: #007bff;
            color: white;
            padding: 10px 16px;
            border-radius: 6px;
        }

        .menu-card a:hover {
            background: #0056b3;
        }

        /* Responsive */

        @media (max-width: 900px) {

            .stats {
                grid-template-columns: repeat(2, 1fr);
            }

            .menu-grid {
                grid-template-columns: repeat(2, 1fr);
            }

        }

        @media (max-width: 600px) {

            .navbar {
                flex-direction: column;
                gap: 12px;
            }

            .stats {
                grid-template-columns: 1fr;
            }

            .menu-grid {
                grid-template-columns: 1fr;
            }

        }

    </style>

</head>

<body>

    <!-- Navbar -->

    <div class="navbar">

        <h2>Bus Reservation Admin</h2>

        <div class="navbar-right">

            <span>
                Welcome, <?php echo htmlspecialchars($_SESSION["admin_name"]); ?>
            </span>

            <a href="logout.php" class="logout-btn">
                Logout
            </a>

        </div>

    </div>


    <!-- Main Content -->

    <div class="container">

        <div class="welcome">

            <h1>Admin Dashboard</h1>

            <p>
                Manage buses, routes, bookings and users from here.
            </p>

        </div>


        <!-- Statistics -->

        <div class="stats">

            <div class="stat-card">

                <h3>Total Users</h3>

                <div class="number">
                    <?php echo $total_users; ?>
                </div>

            </div>


            <div class="stat-card">

                <h3>Total Buses</h3>

                <div class="number">
                    <?php echo $total_buses; ?>
                </div>

            </div>


            <div class="stat-card">

                <h3>Total Routes</h3>

                <div class="number">
                    <?php echo $total_routes; ?>
                </div>

            </div>


            <div class="stat-card">

                <h3>Confirmed Bookings</h3>

                <div class="number">
                    <?php echo $total_bookings; ?>
                </div>

            </div>

        </div>


        <!-- Management -->

        <div class="management">

            <h2>Management</h2>

            <div class="menu-grid">


                <div class="menu-card">

                    <h3>Add Bus</h3>

                    <p>
                        Add a new bus with bus number, type and seat capacity.
                    </p>

                    <a href="add_bus.php">
                        Add Bus
                    </a>

                </div>


                <div class="menu-card">

                    <h3>Manage Buses</h3>

                    <p>
                        View, edit or delete existing buses.
                    </p>

                    <a href="manage_bus.php">
                        Manage Buses
                    </a>

                </div>


                <div class="menu-card">

                    <h3>Add Route</h3>

                    <p>
                        Create a route with source, destination, date, time and price.
                    </p>

                    <a href="add_route.php">
                        Add Route
                    </a>

                </div>


                <div class="menu-card">

                    <h3>Manage Routes</h3>

                    <p>
                        View, edit or delete existing bus routes.
                    </p>

                    <a href="manage_routes.php">
                        Manage Routes
                    </a>

                </div>


                <div class="menu-card">

                    <h3>Bookings</h3>

                    <p>
                        View all customer bookings and their booking status.
                    </p>

                    <a href="bookings.php">
                        View Bookings
                    </a>

                </div>


                <div class="menu-card">

                    <h3>Users</h3>

                    <p>
                        View registered users in the bus reservation system.
                    </p>

                    <a href="users.php">
                        View Users
                    </a>

                </div>

            </div>

        </div>

    </div>

</body>

</html>