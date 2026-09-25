<?php

session_start();

require_once "config/db_connection.php";

// User must be logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Get booking ID
$booking_id = intval($_GET["booking_id"] ?? 0);

if ($booking_id <= 0) {
    die("Invalid booking ID.");
}


// Get booking, passenger, bus and route details
$sql = "
    SELECT
        bk.id AS booking_id,
        bk.seat_number,
        bk.booking_date,
        bk.status,

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

    INNER JOIN passengers p
        ON bk.id = p.booking_id

    INNER JOIN buses b
        ON bk.bus_id = b.id

    INNER JOIN routes r
        ON bk.route_id = r.id

    WHERE bk.id = ?
    AND bk.user_id = ?
";

$stmt = $conn->prepare($sql);

$user_id = $_SESSION["user_id"];

$stmt->bind_param(
    "ii",
    $booking_id,
    $user_id
);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Booking not found.");
}

$ticket = $result->fetch_assoc();

$stmt->close();


// Format values
$departure = date(
    "h:i A",
    strtotime($ticket["departure_time"])
);

$arrival = date(
    "h:i A",
    strtotime($ticket["arrival_time"])
);

$journey_date = date(
    "d M Y",
    strtotime($ticket["journey_date"])
);

$booking_date = date(
    "d M Y, h:i A",
    strtotime($ticket["booking_date"])
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Bus Ticket - Bus Reservation</title>

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
            width: 90%;
            max-width: 850px;
            margin: 40px auto;
        }


        /* Success */

        .success {
            text-align: center;
            margin-bottom: 25px;
        }

        .success-icon {
            font-size: 55px;
            margin-bottom: 10px;
        }

        .success h1 {
            margin-bottom: 8px;
        }

        .success p {
            color: #666;
        }


        /* Ticket */

        .ticket {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.12);
            overflow: hidden;
        }


        .ticket-header {
            background: #007bff;
            color: white;
            padding: 25px;
            text-align: center;
        }

        .ticket-header h2 {
            margin-bottom: 8px;
        }

        .ticket-header p {
            font-size: 14px;
        }


        .ticket-body {
            padding: 30px;
        }


        /* Booking ID */

        .booking-id {
            text-align: right;
            margin-bottom: 25px;
            font-size: 14px;
        }

        .booking-id strong {
            font-size: 18px;
        }


        /* Route */

        .route {
            display: flex;
            justify-content: space-between;
            align-items: center;

            background: #f7f7f7;

            padding: 20px;

            border-radius: 10px;

            margin-bottom: 25px;
        }

        .location {
            text-align: center;
            width: 40%;
        }

        .location h3 {
            margin-bottom: 7px;
        }

        .location p {
            color: #666;
        }

        .arrow {
            font-size: 28px;
            font-weight: bold;
        }


        /* Details */

        .details-title {
            margin-bottom: 15px;
        }

        .details-grid {
            display: grid;

            grid-template-columns: repeat(2, 1fr);

            gap: 15px;
        }

        .detail-box {
            border: 1px solid #ddd;

            padding: 15px;

            border-radius: 8px;
        }

        .detail-box span {
            display: block;

            color: #777;

            font-size: 13px;

            margin-bottom: 5px;
        }

        .detail-box strong {
            font-size: 16px;
        }


        /* Price */

        .price-section {
            margin-top: 25px;

            padding: 20px;

            background: #f7f7f7;

            border-radius: 10px;

            display: flex;

            justify-content: space-between;

            align-items: center;
        }

        .price {
            font-size: 25px;

            font-weight: bold;
        }


        /* Status */

        .status {
            margin-top: 20px;

            text-align: center;
        }

        .confirmed {
            display: inline-block;

            background: #d4edda;

            color: #155724;

            padding: 10px 20px;

            border-radius: 20px;

            font-weight: bold;
        }


        /* Buttons */

        .buttons {
            display: flex;

            gap: 15px;

            margin-top: 25px;
        }

        .btn {
            flex: 1;

            padding: 14px;

            text-align: center;

            text-decoration: none;

            border-radius: 8px;

            font-weight: bold;

            cursor: pointer;

            border: none;

            font-size: 15px;
        }

        .print-btn {
            background: #007bff;

            color: white;
        }

        .home-btn {
            background: #eee;

            color: #333;
        }

        .print-btn:hover {
            background: #0056b3;
        }

        .home-btn:hover {
            background: #ddd;
        }


        /* Print */

        @media print {

            body {
                background: white;
            }

            .navbar,
            .buttons {
                display: none;
            }

            .container {
                width: 100%;
                margin: 0;
            }

            .ticket {
                box-shadow: none;
                border: 1px solid #ccc;
            }

        }


        /* Mobile */

        @media (max-width: 600px) {

            .navbar {
                padding: 15px;
            }

            .nav-links a {
                margin-left: 8px;
                font-size: 13px;
            }

            .ticket-body {
                padding: 20px;
            }

            .route {
                flex-direction: column;

                gap: 15px;
            }

            .location {
                width: 100%;
            }

            .details-grid {
                grid-template-columns: 1fr;
            }

            .buttons {
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


    <!-- Success Message -->

    <div class="success">

        <div class="success-icon">
            ✅
        </div>

        <h1>
            Booking Confirmed!
        </h1>

        <p>
            Your bus ticket has been booked successfully.
        </p>

    </div>


    <!-- Ticket -->

    <div class="ticket">


        <!-- Header -->

        <div class="ticket-header">

            <h2>
                BUS TICKET
            </h2>

            <p>
                Online Bus Ticket Reservation System
            </p>

        </div>


        <div class="ticket-body">


            <!-- Booking ID -->

            <div class="booking-id">

                Booking ID:

                <strong>
                    #<?php echo $ticket["booking_id"]; ?>
                </strong>

            </div>


            <!-- Route -->

            <div class="route">


                <div class="location">

                    <h3>
                        <?php
                        echo htmlspecialchars(
                            $ticket["source"]
                        );
                        ?>
                    </h3>

                    <p>
                        <?php echo $departure; ?>
                    </p>

                </div>


                <div class="arrow">
                    →
                </div>


                <div class="location">

                    <h3>
                        <?php
                        echo htmlspecialchars(
                            $ticket["destination"]
                        );
                        ?>
                    </h3>

                    <p>
                        <?php echo $arrival; ?>
                    </p>

                </div>


            </div>


            <!-- Journey Details -->

            <h3 class="details-title">
                Journey Details
            </h3>


            <div class="details-grid">


                <div class="detail-box">

                    <span>
                        Passenger Name
                    </span>

                    <strong>
                        <?php
                        echo htmlspecialchars(
                            $ticket["passenger_name"]
                        );
                        ?>
                    </strong>

                </div>


                <div class="detail-box">

                    <span>
                        Age
                    </span>

                    <strong>
                        <?php echo $ticket["age"]; ?>
                    </strong>

                </div>


                <div class="detail-box">

                    <span>
                        Gender
                    </span>

                    <strong>
                        <?php
                        echo htmlspecialchars(
                            $ticket["gender"]
                        );
                        ?>
                    </strong>

                </div>


                <div class="detail-box">

                    <span>
                        Bus Name
                    </span>

                    <strong>
                        <?php
                        echo htmlspecialchars(
                            $ticket["bus_name"]
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
                            $ticket["bus_number"]
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
                            $ticket["bus_type"]
                        );
                        ?>
                    </strong>

                </div>


                <div class="detail-box">

                    <span>
                        Journey Date
                    </span>

                    <strong>
                        <?php echo $journey_date; ?>
                    </strong>

                </div>


                <div class="detail-box">

                    <span>
                        Seat Number
                    </span>

                    <strong>
                        Seat <?php
                        echo htmlspecialchars(
                            $ticket["seat_number"]
                        );
                        ?>
                    </strong>

                </div>


                <div class="detail-box">

                    <span>
                        Booking Date
                    </span>

                    <strong>
                        <?php echo $booking_date; ?>
                    </strong>

                </div>


            </div>


            <!-- Price -->

            <div class="price-section">

                <strong>
                    Total Amount
                </strong>

                <div class="price">

                    ₹<?php
                    echo number_format(
                        $ticket["price"],
                        2
                    );
                    ?>

                </div>

            </div>


            <!-- Status -->

            <div class="status">

                <span class="confirmed">

                    ✓
                    <?php
                    echo htmlspecialchars(
                        $ticket["status"]
                    );
                    ?>

                </span>

            </div>


            <!-- Buttons -->

            <div class="buttons">


                <button
                    class="btn print-btn"
                    onclick="window.print()"
                >
                    🖨 Print Ticket
                </button>


                <a
                    href="index.php"
                    class="btn home-btn"
                >
                    ← Back to Home
                </a>


            </div>


        </div>

    </div>


</div>


</body>

</html>