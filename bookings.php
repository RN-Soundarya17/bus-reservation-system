<?php

session_start();

require_once "../config/db_connection.php";

// Check admin login
if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit();
}

// Get all bookings
$result = $conn->query(
    "SELECT
        bk.id AS booking_id,
        bk.seat_number,
        bk.booking_date,
        bk.status,

        u.name AS user_name,
        u.email AS user_email,
        u.phone AS user_phone,

        p.passenger_name,
        p.age,
        p.gender,

        b.bus_number,
        b.bus_name,
        b.bus_type,

        r.source,
        r.destination,
        r.departure_time,
        r.arrival_time,
        r.journey_date,
        r.price

     FROM bookings bk

     INNER JOIN users u
        ON bk.user_id = u.id

     INNER JOIN passengers p
        ON bk.id = p.booking_id

     INNER JOIN buses b
        ON bk.bus_id = b.id

     INNER JOIN routes r
        ON bk.route_id = r.id

     ORDER BY bk.booking_date DESC"
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Bookings - Admin</title>

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

        .nav-links {
            display: flex;
            gap: 15px;
        }

        .nav-links a {
            text-decoration: none;
            color: white;
            padding: 9px 14px;
            border-radius: 6px;
        }

        .dashboard-btn {
            background: #007bff;
        }

        .logout-btn {
            background: #dc3545;
        }

        .dashboard-btn:hover {
            background: #0056b3;
        }

        .logout-btn:hover {
            background: #b02a37;
        }

        /* Container */

        .container {
            width: 95%;
            max-width: 1500px;
            margin: 35px auto;
        }

        .page-header {
            margin-bottom: 25px;
        }

        .page-header h1 {
            color: #222;
            margin-bottom: 8px;
        }

        .page-header p {
            color: #666;
        }

        /* Table */

        .table-container {
            background: white;
            padding: 20px;
            border-radius: 10px;

            box-shadow:
                0 3px 12px rgba(0, 0, 0, 0.08);

            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1500px;
        }

        th,
        td {
            padding: 13px 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            white-space: nowrap;
        }

        th {
            background: #f1f3f5;
            color: #333;
        }

        tr:hover {
            background: #f8f9fa;
        }

        /* Status */

        .status {
            padding: 6px 10px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
            display: inline-block;
        }

        .confirmed {
            background: #d4edda;
            color: #155724;
        }

        .cancelled {
            background: #f8d7da;
            color: #721c24;
        }

        /* Empty */

        .empty {
            text-align: center;
            padding: 40px;
            color: #777;
        }

        .empty h3 {
            margin-bottom: 10px;
        }

        /* Responsive */

        @media (max-width: 700px) {

            .navbar {
                flex-direction: column;
                gap: 12px;
            }

            .nav-links {
                flex-wrap: wrap;
                justify-content: center;
            }

        }

    </style>

</head>

<body>

    <!-- Navbar -->

    <div class="navbar">

        <h2>Bus Reservation Admin</h2>

        <div class="nav-links">

            <a
                href="dashboard.php"
                class="dashboard-btn"
            >
                Dashboard
            </a>

            <a
                href="logout.php"
                class="logout-btn"
            >
                Logout
            </a>

        </div>

    </div>


    <!-- Main Content -->

    <div class="container">

        <div class="page-header">

            <h1>All Bookings</h1>

            <p>
                View customer bookings and passenger details.
            </p>

        </div>


        <div class="table-container">

            <?php if ($result->num_rows > 0): ?>

                <table>

                    <thead>

                        <tr>

                            <th>Booking ID</th>

                            <th>User Name</th>

                            <th>Email</th>

                            <th>Phone</th>

                            <th>Passenger</th>

                            <th>Age</th>

                            <th>Gender</th>

                            <th>Bus</th>

                            <th>Bus Number</th>

                            <th>Route</th>

                            <th>Departure</th>

                            <th>Arrival</th>

                            <th>Journey Date</th>

                            <th>Seat</th>

                            <th>Price</th>

                            <th>Booking Date</th>

                            <th>Status</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php while ($booking = $result->fetch_assoc()): ?>

                            <tr>

                                <!-- Booking ID -->

                                <td>
                                    #<?php echo $booking["booking_id"]; ?>
                                </td>


                                <!-- User -->

                                <td>
                                    <?php echo htmlspecialchars(
                                        $booking["user_name"]
                                    ); ?>
                                </td>


                                <!-- Email -->

                                <td>
                                    <?php echo htmlspecialchars(
                                        $booking["user_email"]
                                    ); ?>
                                </td>


                                <!-- Phone -->

                                <td>
                                    <?php echo htmlspecialchars(
                                        $booking["user_phone"]
                                    ); ?>
                                </td>


                                <!-- Passenger -->

                                <td>
                                    <?php echo htmlspecialchars(
                                        $booking["passenger_name"]
                                    ); ?>
                                </td>


                                <!-- Age -->

                                <td>
                                    <?php echo $booking["age"]; ?>
                                </td>


                                <!-- Gender -->

                                <td>
                                    <?php echo htmlspecialchars(
                                        $booking["gender"]
                                    ); ?>
                                </td>


                                <!-- Bus -->

                                <td>
                                    <?php echo htmlspecialchars(
                                        $booking["bus_name"]
                                    ); ?>
                                </td>


                                <!-- Bus Number -->

                                <td>
                                    <?php echo htmlspecialchars(
                                        $booking["bus_number"]
                                    ); ?>
                                </td>


                                <!-- Route -->

                                <td>

                                    <?php
                                    echo htmlspecialchars(
                                        $booking["source"]
                                    );
                                    ?>

                                    →

                                    <?php
                                    echo htmlspecialchars(
                                        $booking["destination"]
                                    );
                                    ?>

                                </td>


                                <!-- Departure -->

                                <td>
                                    <?php echo date(
                                        "h:i A",
                                        strtotime(
                                            $booking["departure_time"]
                                        )
                                    ); ?>
                                </td>


                                <!-- Arrival -->

                                <td>
                                    <?php echo date(
                                        "h:i A",
                                        strtotime(
                                            $booking["arrival_time"]
                                        )
                                    ); ?>
                                </td>


                                <!-- Journey Date -->

                                <td>
                                    <?php echo date(
                                        "d M Y",
                                        strtotime(
                                            $booking["journey_date"]
                                        )
                                    ); ?>
                                </td>


                                <!-- Seat -->

                                <td>
                                    <?php echo htmlspecialchars(
                                        $booking["seat_number"]
                                    ); ?>
                                </td>


                                <!-- Price -->

                                <td>
                                    ₹<?php echo number_format(
                                        $booking["price"],
                                        2
                                    ); ?>
                                </td>


                                <!-- Booking Date -->

                                <td>
                                    <?php echo date(
                                        "d M Y h:i A",
                                        strtotime(
                                            $booking["booking_date"]
                                        )
                                    ); ?>
                                </td>


                                <!-- Status -->

                                <td>

                                    <?php if (
                                        $booking["status"] === "Confirmed"
                                    ): ?>

                                        <span class="status confirmed">
                                            Confirmed
                                        </span>

                                    <?php else: ?>

                                        <span class="status cancelled">
                                            Cancelled
                                        </span>

                                    <?php endif; ?>

                                </td>

                            </tr>

                        <?php endwhile; ?>

                    </tbody>

                </table>

            <?php else: ?>

                <div class="empty">

                    <h3>No bookings found.</h3>

                    <p>
                        Customer bookings will appear here.
                    </p>

                </div>

            <?php endif; ?>

        </div>

    </div>

</body>

</html>