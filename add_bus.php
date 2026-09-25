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

    $bus_number = trim($_POST["bus_number"]);
    $bus_name = trim($_POST["bus_name"]);
    $bus_type = trim($_POST["bus_type"]);
    $total_seats = intval($_POST["total_seats"]);

    // Validate fields
    if (
        empty($bus_number) ||
        empty($bus_name) ||
        empty($bus_type) ||
        $total_seats <= 0
    ) {

        $message = "Please fill all fields correctly.";
        $message_type = "error";

    } else {

        // Check duplicate bus number
        $check_stmt = $conn->prepare(
            "SELECT id FROM buses WHERE bus_number = ?"
        );

        $check_stmt->bind_param("s", $bus_number);
        $check_stmt->execute();

        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {

            $message = "This bus number already exists.";
            $message_type = "error";

        } else {

            // Insert new bus
            $stmt = $conn->prepare(
                "INSERT INTO buses 
                (bus_number, bus_name, bus_type, total_seats)
                VALUES (?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "sssi",
                $bus_number,
                $bus_name,
                $bus_type,
                $total_seats
            );

            if ($stmt->execute()) {

                $message = "Bus added successfully.";
                $message_type = "success";

                // Clear form values
                $bus_number = "";
                $bus_name = "";
                $bus_type = "";
                $total_seats = "";

            } else {

                $message = "Failed to add bus.";
                $message_type = "error";
            }

            $stmt->close();
        }

        $check_stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Add Bus - Admin</title>

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
            width: 90%;
            max-width: 650px;
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
            margin-bottom: 10px;
            color: #222;
        }

        .form-card .subtitle {
            text-align: center;
            color: #777;
            margin-bottom: 25px;
        }

        /* Message */

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
            margin-top: 5px;
        }

        .submit-btn:hover {
            background: #0056b3;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #007bff;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
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

            <a href="logout.php" class="logout-btn">
                Logout
            </a>

        </div>

    </div>


    <!-- Main Content -->

    <div class="container">

        <div class="form-card">

            <h1>Add New Bus</h1>

            <p class="subtitle">
                Enter the bus details below
            </p>


            <?php if (!empty($message)): ?>

                <div class="message <?php echo $message_type; ?>">

                    <?php echo htmlspecialchars($message); ?>

                </div>

            <?php endif; ?>


            <form method="POST" action="">


                <!-- Bus Number -->

                <div class="form-group">

                    <label for="bus_number">
                        Bus Number
                    </label>

                    <input
                        type="text"
                        id="bus_number"
                        name="bus_number"
                        placeholder="Example: KA03GH4567"
                        value="<?php echo htmlspecialchars($bus_number ?? ''); ?>"
                        required
                    >

                </div>


                <!-- Bus Name -->

                <div class="form-group">

                    <label for="bus_name">
                        Bus Name
                    </label>

                    <input
                        type="text"
                        id="bus_name"
                        name="bus_name"
                        placeholder="Example: Karnataka Express"
                        value="<?php echo htmlspecialchars($bus_name ?? ''); ?>"
                        required
                    >

                </div>


                <!-- Bus Type -->

                <div class="form-group">

                    <label for="bus_type">
                        Bus Type
                    </label>

                    <select
                        id="bus_type"
                        name="bus_type"
                        required
                    >

                        <option value="">
                            Select Bus Type
                        </option>

                        <option value="AC Sleeper">
                            AC Sleeper
                        </option>

                        <option value="AC Seater">
                            AC Seater
                        </option>

                        <option value="Non-AC Sleeper">
                            Non-AC Sleeper
                        </option>

                        <option value="Non-AC Seater">
                            Non-AC Seater
                        </option>

                    </select>

                </div>


                <!-- Total Seats -->

                <div class="form-group">

                    <label for="total_seats">
                        Total Seats
                    </label>

                    <input
                        type="number"
                        id="total_seats"
                        name="total_seats"
                        min="1"
                        max="100"
                        placeholder="Example: 40"
                        value="<?php echo htmlspecialchars($total_seats ?? ''); ?>"
                        required
                    >

                </div>


                <!-- Submit -->

                <button type="submit" class="submit-btn">
                    Add Bus
                </button>

            </form>


            <a href="manage_bus.php" class="back-link">
                View All Buses →
            </a>

        </div>

    </div>

</body>

</html>