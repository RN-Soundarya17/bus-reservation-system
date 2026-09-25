<?php

session_start();

require_once "../config/db_connection.php";

// Check admin login
if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit();
}

$message = "";
$message_type = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $bus_id = intval($_POST["bus_id"]);
    $source = trim($_POST["source"]);
    $destination = trim($_POST["destination"]);
    $departure_time = $_POST["departure_time"];
    $arrival_time = $_POST["arrival_time"];
    $journey_date = $_POST["journey_date"];
    $price = floatval($_POST["price"]);

    // Validate fields
    if (
        $bus_id <= 0 ||
        empty($source) ||
        empty($destination) ||
        empty($departure_time) ||
        empty($arrival_time) ||
        empty($journey_date) ||
        $price <= 0
    ) {

        $message = "Please fill all fields correctly.";
        $message_type = "error";

    } elseif (strcasecmp($source, $destination) === 0) {

        $message = "Source and destination cannot be the same.";
        $message_type = "error";

    } elseif ($journey_date < date("Y-m-d")) {

        $message = "Journey date cannot be in the past.";
        $message_type = "error";

    } else {

        // Check whether selected bus exists
        $bus_check = $conn->prepare(
            "SELECT id FROM buses WHERE id = ?"
        );

        $bus_check->bind_param("i", $bus_id);
        $bus_check->execute();

        $bus_result = $bus_check->get_result();

        if ($bus_result->num_rows === 0) {

            $message = "Selected bus does not exist.";
            $message_type = "error";

        } else {

            // Insert route
            $stmt = $conn->prepare(
                "INSERT INTO routes
                (
                    bus_id,
                    source,
                    destination,
                    departure_time,
                    arrival_time,
                    journey_date,
                    price
                )
                VALUES (?, ?, ?, ?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "isssssd",
                $bus_id,
                $source,
                $destination,
                $departure_time,
                $arrival_time,
                $journey_date,
                $price
            );

            if ($stmt->execute()) {

                $message = "Route added successfully.";
                $message_type = "success";

                // Clear form
                $source = "";
                $destination = "";
                $departure_time = "";
                $arrival_time = "";
                $journey_date = "";
                $price = "";

            } else {

                $message = "Failed to add route.";
                $message_type = "error";
            }

            $stmt->close();
        }

        $bus_check->close();
    }
}

// Get all buses for dropdown
$buses = $conn->query(
    "SELECT id, bus_number, bus_name, bus_type, total_seats
     FROM buses
     ORDER BY bus_name ASC"
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Add Route - Admin</title>

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

        .routes-btn {
            background: #198754;
        }

        .logout-btn {
            background: #dc3545;
        }

        .dashboard-btn:hover {
            background: #0056b3;
        }

        .routes-btn:hover {
            background: #146c43;
        }

        .logout-btn:hover {
            background: #b02a37;
        }

        /* Container */

        .container {
            width: 90%;
            max-width: 700px;
            margin: 40px auto;
        }

        .form-card {
            background: white;
            padding: 35px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .form-card h1 {
            text-align: center;
            color: #222;
            margin-bottom: 10px;
        }

        .subtitle {
            text-align: center;
            color: #777;
            margin-bottom: 25px;
        }

        /* Messages */

        .message {
            padding: 12px;
            border-radius: 7px;
            margin-bottom: 20px;
            text-align: center;
        }

        .success {
            background: #d4edda;
            color: #155724;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
        }

        /* Form */

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            margin-bottom: 7px;
            font-weight: bold;
            color: #333;
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

        .submit-btn {
            width: 100%;
            padding: 13px;
            border: none;
            border-radius: 7px;
            background: #007bff;
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        .submit-btn:hover {
            background: #0056b3;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            text-decoration: none;
            color: #007bff;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .bus-info {
            margin-top: 6px;
            font-size: 13px;
            color: #777;
        }

    </style>

</head>

<body>

    <!-- Navbar -->

    <div class="navbar">

        <h2>Bus Reservation Admin</h2>

        <div class="nav-links">

            <a href="dashboard.php" class="dashboard-btn">
                Dashboard
            </a>

            <a href="manage_routes.php" class="routes-btn">
                Manage Routes
            </a>

            <a href="logout.php" class="logout-btn">
                Logout
            </a>

        </div>

    </div>


    <!-- Main -->

    <div class="container">

        <div class="form-card">

            <h1>Add New Route</h1>

            <p class="subtitle">
                Create a bus journey for customers
            </p>


            <?php if (!empty($message)): ?>

                <div class="message <?php echo $message_type; ?>">

                    <?php echo htmlspecialchars($message); ?>

                </div>

            <?php endif; ?>


            <?php if ($buses->num_rows === 0): ?>

                <div class="message error">

                    No buses are available.

                    <br><br>

                    Please add a bus before creating a route.

                </div>

                <a href="add_bus.php" class="back-link">
                    → Add Bus
                </a>

            <?php else: ?>


                <form method="POST" action="">


                    <!-- Select Bus -->

                    <div class="form-group">

                        <label for="bus_id">
                            Select Bus
                        </label>

                        <select
                            id="bus_id"
                            name="bus_id"
                            required
                        >

                            <option value="">
                                Select a bus
                            </option>

                            <?php while ($bus = $buses->fetch_assoc()): ?>

                                <option
                                    value="<?php echo $bus["id"]; ?>"
                                    <?php
                                    if (
                                        isset($_POST["bus_id"]) &&
                                        $_POST["bus_id"] == $bus["id"]
                                    ) {
                                        echo "selected";
                                    }
                                    ?>
                                >

                                    <?php
                                    echo htmlspecialchars(
                                        $bus["bus_name"]
                                    );
                                    ?>

                                    -
                                    <?php
                                    echo htmlspecialchars(
                                        $bus["bus_number"]
                                    );
                                    ?>

                                    -
                                    <?php
                                    echo htmlspecialchars(
                                        $bus["bus_type"]
                                    );
                                    ?>

                                </option>

                            <?php endwhile; ?>

                        </select>

                    </div>


                    <!-- Source -->

                    <div class="form-group">

                        <label for="source">
                            Source
                        </label>

                        <input
                            type="text"
                            id="source"
                            name="source"
                            placeholder="Example: Davangere"
                            value="<?php echo htmlspecialchars($source ?? ''); ?>"
                            required
                        >

                    </div>


                    <!-- Destination -->

                    <div class="form-group">

                        <label for="destination">
                            Destination
                        </label>

                        <input
                            type="text"
                            id="destination"
                            name="destination"
                            placeholder="Example: Bengaluru"
                            value="<?php echo htmlspecialchars($destination ?? ''); ?>"
                            required
                        >

                    </div>


                    <!-- Departure -->

                    <div class="form-group">

                        <label for="departure_time">
                            Departure Time
                        </label>

                        <input
                            type="time"
                            id="departure_time"
                            name="departure_time"
                            value="<?php echo htmlspecialchars($departure_time ?? ''); ?>"
                            required
                        >

                    </div>


                    <!-- Arrival -->

                    <div class="form-group">

                        <label for="arrival_time">
                            Arrival Time
                        </label>

                        <input
                            type="time"
                            id="arrival_time"
                            name="arrival_time"
                            value="<?php echo htmlspecialchars($arrival_time ?? ''); ?>"
                            required
                        >

                    </div>


                    <!-- Journey Date -->

                    <div class="form-group">

                        <label for="journey_date">
                            Journey Date
                        </label>

                        <input
                            type="date"
                            id="journey_date"
                            name="journey_date"
                            min="<?php echo date('Y-m-d'); ?>"
                            value="<?php echo htmlspecialchars($journey_date ?? ''); ?>"
                            required
                        >

                    </div>


                    <!-- Price -->

                    <div class="form-group">

                        <label for="price">
                            Ticket Price (₹)
                        </label>

                        <input
                            type="number"
                            id="price"
                            name="price"
                            min="1"
                            step="0.01"
                            placeholder="Example: 450"
                            value="<?php echo htmlspecialchars($price ?? ''); ?>"
                            required
                        >

                    </div>


                    <!-- Submit -->

                    <button
                        type="submit"
                        class="submit-btn"
                    >
                        Add Route
                    </button>

                </form>


                <a href="manage_routes.php" class="back-link">
                    View All Routes →
                </a>

            <?php endif; ?>

        </div>

    </div>

</body>

</html>