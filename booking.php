<?php

session_start();

require_once "config/db_connection.php";

// User must be logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Get route and seat
$route_id = intval($_GET["route_id"] ?? 0);
$seat_number = trim($_GET["seat_number"] ?? "");

if ($route_id <= 0 || $seat_number === "") {
    die("Invalid booking details.");
}


// Get route and bus information
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
        b.bus_type
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


// Check whether selected seat is already booked
$sql_check = "
    SELECT id
    FROM bookings
    WHERE route_id = ?
    AND seat_number = ?
    AND status = 'Confirmed'
";

$stmt = $conn->prepare($sql_check);
$stmt->bind_param("is", $route_id, $seat_number);
$stmt->execute();

$result_check = $stmt->get_result();

if ($result_check->num_rows > 0) {
    die("Sorry, this seat has already been booked. Please select another seat.");
}

$stmt->close();


// Convert time
$departure = date("h:i A", strtotime($bus["departure_time"]));
$arrival = date("h:i A", strtotime($bus["arrival_time"]));


// Passenger form submission
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $passenger_name = trim($_POST["passenger_name"] ?? "");
    $age = intval($_POST["age"] ?? 0);
    $gender = trim($_POST["gender"] ?? "");

    // Validation
    if ($passenger_name === "" || $age <= 0 || $gender === "") {

        $error = "Please fill all passenger details.";

    } elseif ($age < 1 || $age > 120) {

        $error = "Please enter a valid age.";

    } else {

        // Check seat again before booking
        $sql_check = "
            SELECT id
            FROM bookings
            WHERE route_id = ?
            AND seat_number = ?
            AND status = 'Confirmed'
        ";

        $stmt = $conn->prepare($sql_check);
        $stmt->bind_param("is", $route_id, $seat_number);
        $stmt->execute();

        $result_check = $stmt->get_result();

        if ($result_check->num_rows > 0) {

            $error = "Sorry, this seat was just booked by another user.";

        } else {

            // Start transaction
            $conn->begin_transaction();

            try {

                // Insert booking
                $sql_booking = "
                    INSERT INTO bookings
                    (user_id, bus_id, route_id, seat_number, status)
                    VALUES (?, ?, ?, ?, 'Confirmed')
                ";

                $stmt_booking = $conn->prepare($sql_booking);

                $user_id = $_SESSION["user_id"];
                $bus_id = $bus["bus_id"];

                $stmt_booking->bind_param(
                    "iiis",
                    $user_id,
                    $bus_id,
                    $route_id,
                    $seat_number
                );

                $stmt_booking->execute();

                // Get booking ID
                $booking_id = $conn->insert_id;

                $stmt_booking->close();


                // Insert passenger details
                $sql_passenger = "
                    INSERT INTO passengers
                    (booking_id, passenger_name, age, gender)
                    VALUES (?, ?, ?, ?)
                ";

                $stmt_passenger = $conn->prepare($sql_passenger);

                $stmt_passenger->bind_param(
                    "isis",
                    $booking_id,
                    $passenger_name,
                    $age,
                    $gender
                );

                $stmt_passenger->execute();

                $stmt_passenger->close();


                // Complete transaction
                $conn->commit();


                // Go to ticket page
                header(
                    "Location: ticket.php?booking_id=" . $booking_id
                );

                exit();

            } catch (Exception $e) {

                $conn->rollback();

                $error = "Booking failed. Please try again.";

            }

        }

        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Passenger Details - Bus Reservation</title>

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
            max-width: 1000px;
            margin: 35px auto;
        }


        .title {
            text-align: center;
            margin-bottom: 25px;
        }

        .title h1 {
            margin-bottom: 8px;
        }

        .title p {
            color: #666;
        }


        /* Layout */

        .booking-layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
        }


        /* Journey card */

        .journey-card,
        .passenger-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.08);
        }


        .journey-card h2,
        .passenger-card h2 {
            margin-bottom: 20px;
        }


        .journey-item {
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }

        .journey-item:last-child {
            border-bottom: none;
        }

        .journey-item strong {
            display: block;
            margin-bottom: 5px;
        }


        .price {
            font-size: 25px;
            font-weight: bold;
            margin-top: 20px;
        }


        /* Form */

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 7px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 7px;
            font-size: 15px;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #007bff;
        }


        .error {
            background: #ffe5e5;
            color: #b00020;
            padding: 12px;
            border-radius: 7px;
            margin-bottom: 18px;
        }


        .confirm-btn {
            width: 100%;
            padding: 14px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 7px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        .confirm-btn:hover {
            background: #0056b3;
        }


        .back-btn {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #007bff;
            text-decoration: none;
        }


        /* Mobile */

        @media (max-width: 750px) {

            .booking-layout {
                grid-template-columns: 1fr;
            }

            .navbar {
                padding: 15px;
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

        <a href="my_bookings.php">My Bookings</a>

        <a href="logout.php">Logout</a>

    </div>

</div>


<!-- Main -->

<div class="container">


    <div class="title">

        <h1>Passenger Details</h1>

        <p>Enter passenger information to confirm your booking</p>

    </div>


    <div class="booking-layout">


        <!-- Journey Details -->

        <div class="journey-card">

            <h2>Journey Details</h2>


            <div class="journey-item">

                <strong>Bus</strong>

                <?php echo htmlspecialchars($bus["bus_name"]); ?>

            </div>


            <div class="journey-item">

                <strong>Bus Number</strong>

                <?php echo htmlspecialchars($bus["bus_number"]); ?>

            </div>


            <div class="journey-item">

                <strong>Bus Type</strong>

                <?php echo htmlspecialchars($bus["bus_type"]); ?>

            </div>


            <div class="journey-item">

                <strong>Route</strong>

                <?php echo htmlspecialchars($bus["source"]); ?>

                →

                <?php echo htmlspecialchars($bus["destination"]); ?>

            </div>


            <div class="journey-item">

                <strong>Journey Date</strong>

                <?php echo date(
                    "d M Y",
                    strtotime($bus["journey_date"])
                ); ?>

            </div>


            <div class="journey-item">

                <strong>Departure</strong>

                <?php echo $departure; ?>

            </div>


            <div class="journey-item">

                <strong>Arrival</strong>

                <?php echo $arrival; ?>

            </div>


            <div class="journey-item">

                <strong>Selected Seat</strong>

                Seat <?php echo htmlspecialchars($seat_number); ?>

            </div>


            <div class="price">

                ₹<?php echo number_format($bus["price"], 2); ?>

            </div>

        </div>


        <!-- Passenger Form -->

        <div class="passenger-card">

            <h2>Passenger Information</h2>


            <?php if ($error !== ""): ?>

                <div class="error">

                    <?php echo htmlspecialchars($error); ?>

                </div>

            <?php endif; ?>


            <form method="POST">


                <div class="form-group">

                    <label for="passenger_name">
                        Passenger Name
                    </label>

                    <input
                        type="text"
                        id="passenger_name"
                        name="passenger_name"
                        placeholder="Enter passenger name"
                        value="<?php echo htmlspecialchars(
                            $_POST["passenger_name"] ?? ""
                        ); ?>"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="age">
                        Age
                    </label>

                    <input
                        type="number"
                        id="age"
                        name="age"
                        min="1"
                        max="120"
                        placeholder="Enter age"
                        value="<?php echo htmlspecialchars(
                            $_POST["age"] ?? ""
                        ); ?>"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="gender">
                        Gender
                    </label>

                    <select
                        id="gender"
                        name="gender"
                        required
                    >

                        <option value="">
                            Select Gender
                        </option>

                        <option value="Male">
                            Male
                        </option>

                        <option value="Female">
                            Female
                        </option>

                        <option value="Other">
                            Other
                        </option>

                    </select>

                </div>


                <button
                    type="submit"
                    class="confirm-btn"
                >
                    Confirm Booking
                </button>


            </form>


            <a
                href="seat_selection.php?route_id=<?php echo $route_id; ?>"
                class="back-btn"
            >
                ← Change Seat
            </a>

        </div>


    </div>


</div>


</body>

</html>