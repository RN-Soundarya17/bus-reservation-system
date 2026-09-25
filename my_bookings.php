<?php

session_start();

require_once "config/db_connection.php";

// User must be logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Get all bookings of the logged-in user
$sql = "
    SELECT
        bk.id AS booking_id,
        bk.seat_number,
        bk.booking_date,
        bk.status,

        p.passenger_name,
        p.age,
        p.gender,

        b.bus_name,
        b.bus_number,
        b.bus_type,

        r.source,
        r.destination,
        r.departure_time,
        r.arrival_time,
        r.journey_date,
        r.price

    FROM bookings bk

    INNER JOIN passengers p
        ON bk.id = p.booking_id

    INNER JOIN buses b
        ON bk.bus_id = b.id

    INNER JOIN routes r
        ON bk.route_id = r.id

    WHERE bk.user_id = ?

    ORDER BY bk.booking_date DESC
";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $user_id);

$stmt->execute();

$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>My Bookings - Bus Reservation</title>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            color: #333;
        }


        /* Navbar */

        .navbar {
            background: #222;

            padding: 15px 40px;

            display: flex;

            justify-content: space-between;

            align-items: center;
        }

        .logo {
            color: white;

            font-size: 24px;

            font-weight: bold;
        }

        .nav-links a {
            color: white;

            text-decoration: none;

            margin-left: 20px;
        }

        .nav-links a:hover {
            text-decoration: underline;
        }


        /* Container */

        .container {
            width: 92%;

            max-width: 1200px;

            margin: 35px auto;
        }


        .page-title {
            text-align: center;

            margin-bottom: 30px;
        }

        .page-title h1 {
            margin-bottom: 8px;
        }

        .page-title p {
            color: #666;
        }


        /* Booking card */

        .booking-card {
            background: white;

            border-radius: 12px;

            box-shadow: 0 3px 15px rgba(0,0,0,0.08);

            margin-bottom: 25px;

            overflow: hidden;
        }


        .booking-header {
            background: #007bff;

            color: white;

            padding: 18px 25px;

            display: flex;

            justify-content: space-between;

            align-items: center;
        }

        .booking-header h2 {
            font-size: 20px;
        }


        .status {
            padding: 7px 15px;

            border-radius: 20px;

            font-size: 13px;

            font-weight: bold;
        }

        .confirmed {
            background: #d4edda;

            color: #155724;
        }

        .cancelled {
            background: #f8d7da;

            color: #721c24;
        }


        .booking-body {
            padding: 25px;
        }


        /* Route */

        .route {
            display: flex;

            justify-content: center;

            align-items: center;

            gap: 30px;

            margin-bottom: 25px;

            text-align: center;
        }

        .location h3 {
            margin-bottom: 5px;
        }

        .location p {
            color: #666;

            font-size: 14px;
        }

        .arrow {
            font-size: 28px;

            font-weight: bold;
        }


        /* Details */

        .details-grid {
            display: grid;

            grid-template-columns: repeat(4, 1fr);

            gap: 15px;
        }

        .detail-box {
            border: 1px solid #ddd;

            padding: 14px;

            border-radius: 8px;
        }

        .detail-box span {
            display: block;

            color: #777;

            font-size: 13px;

            margin-bottom: 5px;
        }

        .detail-box strong {
            font-size: 15px;
        }


        /* Price */

        .price {
            font-size: 22px;

            font-weight: bold;
        }


        /* Buttons */

        .actions {
            display: flex;

            gap: 12px;

            margin-top: 25px;
        }

        .btn {
            padding: 11px 18px;

            border-radius: 7px;

            text-decoration: none;

            font-weight: bold;

            text-align: center;
        }

        .ticket-btn {
            background: #007bff;

            color: white;
        }

        .cancel-btn {
            background: #dc3545;

            color: white;
        }

        .search-btn {
            background: #28a745;

            color: white;

            display: inline-block;

            margin-top: 15px;
        }

        .btn:hover {
            opacity: 0.85;
        }


        /* No bookings */

        .no-bookings {
            background: white;

            padding: 50px 20px;

            text-align: center;

            border-radius: 12px;

            box-shadow: 0 3px 12px rgba(0,0,0,0.08);
        }

        .no-bookings h2 {
            margin-bottom: 10px;
        }

        .no-bookings p {
            color: #666;
        }


        /* Mobile */

        @media (max-width: 900px) {

            .details-grid {
                grid-template-columns: repeat(2, 1fr);
            }

        }


        @media (max-width: 600px) {

            .navbar {
                padding: 15px;
            }

            .nav-links a {
                margin-left: 8px;

                font-size: 13px;
            }

            .booking-header {
                flex-direction: column;

                align-items: flex-start;

                gap: 10px;
            }

            .route {
                gap: 15px;
            }

            .details-grid {
                grid-template-columns: 1fr;
            }

            .actions {
                flex-direction: column;
            }

        }

    </style>

</head>

<body>


<!-- Navbar -->

<div class="navbar">

    <div class="logo">
        Bus Reservation
    </div>

    <div class="nav-links">

        <a href="index.php">
            Home
        </a>

        <a href="my_bookings.php">
            My Bookings
        </a>

        <a href="logout.php">
            Logout
        </a>

    </div>

</div>


<!-- Main -->

<div class="container">


    <div class="page-title">

        <h1>
            My Bookings
        </h1>

        <p>
            View your bus ticket bookings
        </p>

    </div>


    <?php if ($result->num_rows > 0): ?>


        <?php while ($booking = $result->fetch_assoc()): ?>


            <div class="booking-card">


                <!-- Header -->

                <div class="booking-header">

                    <h2>
                        Booking #<?php
                        echo $booking["booking_id"];
                        ?>
                    </h2>


                    <?php if ($booking["status"] === "Confirmed"): ?>

                        <span class="status confirmed">
                            ✓ Confirmed
                        </span>

                    <?php else: ?>

                        <span class="status cancelled">
                            ✕ Cancelled
                        </span>

                    <?php endif; ?>


                </div>


                <!-- Body -->

                <div class="booking-body">


                    <!-- Route -->

                    <div class="route">


                        <div class="location">

                            <h3>
                                <?php
                                echo htmlspecialchars(
                                    $booking["source"]
                                );
                                ?>
                            </h3>

                            <p>
                                <?php
                                echo date(
                                    "h:i A",
                                    strtotime(
                                        $booking["departure_time"]
                                    )
                                );
                                ?>
                            </p>

                        </div>


                        <div class="arrow">
                            →
                        </div>


                        <div class="location">

                            <h3>
                                <?php
                                echo htmlspecialchars(
                                    $booking["destination"]
                                );
                                ?>
                            </h3>

                            <p>
                                <?php
                                echo date(
                                    "h:i A",
                                    strtotime(
                                        $booking["arrival_time"]
                                    )
                                );
                                ?>
                            </p>

                        </div>


                    </div>


                    <!-- Details -->

                    <div class="details-grid">


                        <div class="detail-box">

                            <span>
                                Passenger
                            </span>

                            <strong>
                                <?php
                                echo htmlspecialchars(
                                    $booking["passenger_name"]
                                );
                                ?>
                            </strong>

                        </div>


                        <div class="detail-box">

                            <span>
                                Bus
                            </span>

                            <strong>
                                <?php
                                echo htmlspecialchars(
                                    $booking["bus_name"]
                                );
                                ?>
                            </strong>

                        </div>


                        <div class="detail-box">

                            <span>
                                Bus Number
                            </span>

                            <strong>
                                <?php
                                echo htmlspecialchars(
                                    $booking["bus_number"]
                                );
                                ?>
                            </strong>

                        </div>


                        <div class="detail-box">

                            <span>
                                Journey Date
                            </span>

                            <strong>
                                <?php
                                echo date(
                                    "d M Y",
                                    strtotime(
                                        $booking["journey_date"]
                                    )
                                );
                                ?>
                            </strong>

                        </div>


                        <div class="detail-box">

                            <span>
                                Seat Number
                            </span>

                            <strong>
                                Seat
                                <?php
                                echo htmlspecialchars(
                                    $booking["seat_number"]
                                );
                                ?>
                            </strong>

                        </div>


                        <div class="detail-box">

                            <span>
                                Bus Type
                            </span>

                            <strong>
                                <?php
                                echo htmlspecialchars(
                                    $booking["bus_type"]
                                );
                                ?>
                            </strong>

                        </div>


                        <div class="detail-box">

                            <span>
                                Booking Date
                            </span>

                            <strong>
                                <?php
                                echo date(
                                    "d M Y",
                                    strtotime(
                                        $booking["booking_date"]
                                    )
                                );
                                ?>
                            </strong>

                        </div>


                        <div class="detail-box">

                            <span>
                                Price
                            </span>

                            <strong class="price">
                                ₹<?php
                                echo number_format(
                                    $booking["price"],
                                    2
                                );
                                ?>
                            </strong>

                        </div>


                    </div>


                    <!-- Actions -->

                    <div class="actions">


                        <a
                            href="ticket.php?booking_id=<?php
                            echo $booking["booking_id"];
                            ?>"
                            class="btn ticket-btn"
                        >
                            🎫 View Ticket
                        </a>


                        <?php if ($booking["status"] === "Confirmed"): ?>

                            <a
                                href="cancel_booking.php?booking_id=<?php
                                echo $booking["booking_id"];
                                ?>"
                                class="btn cancel-btn"
                                onclick="return confirm(
                                    'Are you sure you want to cancel this booking?'
                                );"
                            >
                                Cancel Booking
                            </a>

                        <?php endif; ?>


                    </div>


                </div>

            </div>


        <?php endwhile; ?>


    <?php else: ?>


        <div class="no-bookings">

            <h2>
                No Bookings Yet
            </h2>

            <p>
                You have not booked any bus tickets yet.
            </p>

            <a
                href="index.php"
                class="btn search-btn"
            >
                Search Buses
            </a>

        </div>


    <?php endif; ?>


</div>


</body>

</html>

<?php

$stmt->close();

?>