<?php

session_start();

require_once "config/db_connection.php";

// Check route ID
$route_id = intval($_GET["route_id"] ?? 0);

if ($route_id <= 0) {
    die("Invalid route selected.");
}

// Get route and bus details
$sql = "
    SELECT
        r.id AS route_id,
        r.source,
        r.destination,
        r.departure_time,
        r.arrival_time,
        r.journey_date,
        r.price,
        b.id AS bus_id,
        b.bus_number,
        b.bus_name,
        b.bus_type,
        b.total_seats
    FROM routes r
    INNER JOIN buses b ON r.bus_id = b.id
    WHERE r.id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $route_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Bus route not found.");
}

$bus = $result->fetch_assoc();

$stmt->close();

// Get already booked seats
$booked_seats = [];

$sql_booked = "
    SELECT seat_number
    FROM bookings
    WHERE route_id = ?
    AND status = 'Confirmed'
";

$stmt = $conn->prepare($sql_booked);
$stmt->bind_param("i", $route_id);
$stmt->execute();

$result_booked = $stmt->get_result();

while ($row = $result_booked->fetch_assoc()) {
    $booked_seats[] = $row["seat_number"];
}

$stmt->close();


// Convert time to 12-hour format
$departure = date("h:i A", strtotime($bus["departure_time"]));
$arrival = date("h:i A", strtotime($bus["arrival_time"]));


// Seat layout
$total_seats = intval($bus["total_seats"]);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Select Seat - Bus Reservation</title>

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

        /* Main */

        .container {
            width: 90%;
            max-width: 1100px;
            margin: 30px auto;
        }

        .page-title {
            text-align: center;
            margin-bottom: 25px;
        }

        .page-title h1 {
            font-size: 30px;
            margin-bottom: 8px;
        }

        .page-title p {
            color: #666;
        }

        /* Bus information */

        .bus-info {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.08);
            margin-bottom: 25px;
        }

        .bus-info h2 {
            margin-bottom: 15px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
        }

        .info-box {
            background: #f7f7f7;
            padding: 15px;
            border-radius: 8px;
        }

        .info-box strong {
            display: block;
            margin-bottom: 5px;
        }

        /* Seat area */

        .seat-section {
            display: flex;
            gap: 40px;
            justify-content: center;
            align-items: flex-start;
        }

        /* Bus */

        .bus {
            width: 330px;
            background: white;
            border: 3px solid #333;
            border-radius: 25px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.12);
        }

        .driver {
            text-align: center;
            font-weight: bold;
            margin-bottom: 25px;
            padding: 12px;
            background: #eee;
            border-radius: 10px;
        }

        .seat-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .seat-group {
            display: flex;
            gap: 10px;
        }

        .aisle {
            width: 25px;
        }

        .seat {
            width: 50px;
            height: 45px;
            border: 2px solid #555;
            border-radius: 8px;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            font-weight: bold;
            background: #fff;
            transition: 0.2s;
        }

        .seat:hover {
            transform: scale(1.05);
        }

        .seat.selected {
            background: #28a745;
            color: white;
            border-color: #28a745;
        }

        .seat.booked {
            background: #dc3545;
            color: white;
            border-color: #dc3545;
            cursor: not-allowed;
        }

        /* Booking panel */

        .booking-panel {
            width: 320px;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.08);
        }

        .booking-panel h2 {
            margin-bottom: 20px;
        }

        .selected-seat {
            background: #f1f1f1;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .selected-seat span {
            font-weight: bold;
        }

        .price {
            font-size: 24px;
            font-weight: bold;
            margin: 20px 0;
        }

        .continue-btn {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 8px;
            background: #007bff;
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        .continue-btn:hover {
            background: #0056b3;
        }

        .continue-btn:disabled {
            background: #aaa;
            cursor: not-allowed;
        }

        /* Legend */

        .legend {
            margin-top: 20px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .legend-box {
            width: 25px;
            height: 25px;
            border-radius: 5px;
            border: 1px solid #555;
        }

        .available-box {
            background: white;
        }

        .selected-box {
            background: #28a745;
        }

        .booked-box {
            background: #dc3545;
        }

        /* Mobile */

        @media (max-width: 800px) {

            .seat-section {
                flex-direction: column;
                align-items: center;
            }

            .info-grid {
                grid-template-columns: repeat(2, 1fr);
            }

        }

        @media (max-width: 500px) {

            .navbar {
                padding: 15px;
            }

            .nav-links a {
                margin-left: 8px;
                font-size: 14px;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .bus {
                width: 100%;
                max-width: 330px;
            }

            .booking-panel {
                width: 100%;
                max-width: 330px;
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

        <a href="index.php">Home</a>

        <?php if (isset($_SESSION["user_id"])): ?>

            <a href="my_bookings.php">My Bookings</a>

            <a href="logout.php">Logout</a>

        <?php else: ?>

            <a href="login.php">Login</a>

            <a href="register.php">Register</a>

        <?php endif; ?>

    </div>

</div>


<!-- Main -->

<div class="container">


    <div class="page-title">

        <h1>Select Your Seat</h1>

        <p>
            Choose an available seat for your journey
        </p>

    </div>


    <!-- Bus Information -->

    <div class="bus-info">

        <h2>
            <?php echo htmlspecialchars($bus["bus_name"]); ?>
        </h2>

        <div class="info-grid">

            <div class="info-box">

                <strong>Bus Number</strong>

                <?php echo htmlspecialchars($bus["bus_number"]); ?>

            </div>


            <div class="info-box">

                <strong>Bus Type</strong>

                <?php echo htmlspecialchars($bus["bus_type"]); ?>

            </div>


            <div class="info-box">

                <strong>Journey</strong>

                <?php echo htmlspecialchars($bus["source"]); ?>
                →
                <?php echo htmlspecialchars($bus["destination"]); ?>

            </div>


            <div class="info-box">

                <strong>Date</strong>

                <?php echo date("d M Y", strtotime($bus["journey_date"])); ?>

            </div>


            <div class="info-box">

                <strong>Departure</strong>

                <?php echo $departure; ?>

            </div>


            <div class="info-box">

                <strong>Arrival</strong>

                <?php echo $arrival; ?>

            </div>


            <div class="info-box">

                <strong>Total Seats</strong>

                <?php echo $total_seats; ?>

            </div>


            <div class="info-box">

                <strong>Price / Seat</strong>

                ₹<?php echo number_format($bus["price"], 2); ?>

            </div>

        </div>

    </div>


    <!-- Seat Section -->

    <div class="seat-section">


        <!-- Bus Seat Layout -->

        <div class="bus">

            <div class="driver">
                🚍 DRIVER
            </div>


            <?php

            $seat_number = 1;

            while ($seat_number <= $total_seats):

            ?>

                <div class="seat-row">


                    <!-- Left side -->

                    <div class="seat-group">

                        <?php

                        for ($i = 0; $i < 2 && $seat_number <= $total_seats; $i++):

                            $seat = $seat_number;

                            $is_booked = in_array((string)$seat, $booked_seats);

                        ?>

                            <div
                                class="seat <?php echo $is_booked ? 'booked' : ''; ?>"
                                data-seat="<?php echo $seat; ?>"
                                onclick="<?php echo $is_booked ? '' : 'selectSeat(' . $seat . ')'; ?>"
                            >

                                <?php echo $seat; ?>

                            </div>

                        <?php

                            $seat_number++;

                        endfor;

                        ?>

                    </div>


                    <div class="aisle"></div>


                    <!-- Right side -->

                    <div class="seat-group">

                        <?php

                        for ($i = 0; $i < 2 && $seat_number <= $total_seats; $i++):

                            $seat = $seat_number;

                            $is_booked = in_array((string)$seat, $booked_seats);

                        ?>

                            <div
                                class="seat <?php echo $is_booked ? 'booked' : ''; ?>"
                                data-seat="<?php echo $seat; ?>"
                                onclick="<?php echo $is_booked ? '' : 'selectSeat(' . $seat . ')'; ?>"
                            >

                                <?php echo $seat; ?>

                            </div>

                        <?php

                            $seat_number++;

                        endfor;

                        ?>

                    </div>


                </div>


            <?php endwhile; ?>


        </div>


        <!-- Booking Panel -->

        <div class="booking-panel">

            <h2>Booking Details</h2>


            <div class="selected-seat">

                Selected Seat:

                <span id="selectedSeat">
                    None
                </span>

            </div>


            <div class="price">

                ₹<span id="price">
                    <?php echo number_format($bus["price"], 2); ?>
                </span>

            </div>


            <form action="booking.php" method="GET">

                <input
                    type="hidden"
                    name="route_id"
                    value="<?php echo $route_id; ?>"
                >

                <input
                    type="hidden"
                    name="seat_number"
                    id="seatNumber"
                    value=""
                >


                <button
                    type="submit"
                    class="continue-btn"
                    id="continueBtn"
                    disabled
                >
                    Continue Booking
                </button>

            </form>


            <!-- Legend -->

            <div class="legend">

                <div class="legend-item">

                    <div class="legend-box available-box"></div>

                    Available

                </div>


                <div class="legend-item">

                    <div class="legend-box selected-box"></div>

                    Selected

                </div>


                <div class="legend-item">

                    <div class="legend-box booked-box"></div>

                    Booked

                </div>

            </div>

        </div>


    </div>


</div>


<script>

let selectedSeat = null;


function selectSeat(seatNumber) {

    // Remove previous selection

    document.querySelectorAll(".seat.selected").forEach(function(seat) {

        seat.classList.remove("selected");

    });


    // Find selected seat

    const seat = document.querySelector(
        '.seat[data-seat="' + seatNumber + '"]'
    );


    if (!seat || seat.classList.contains("booked")) {

        return;

    }


    // Select seat

    seat.classList.add("selected");

    selectedSeat = seatNumber;


    // Update information

    document.getElementById("selectedSeat").textContent =
        "Seat " + seatNumber;


    document.getElementById("seatNumber").value =
        seatNumber;


    // Enable button

    document.getElementById("continueBtn").disabled = false;

}

</script>


</body>

</html>