<?php

session_start();

require_once "../config/db_connection.php";

// Check admin login
if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit();
}

$message = "";

// Delete route
if (isset($_GET["delete"])) {

    $route_id = intval($_GET["delete"]);

    if ($route_id > 0) {

        $stmt = $conn->prepare("DELETE FROM routes WHERE id = ?");
        $stmt->bind_param("i", $route_id);

        if ($stmt->execute()) {
            $message = "Route deleted successfully.";
        } else {
            $message = "Unable to delete route.";
        }

        $stmt->close();
    }
}

// Get all routes
$result = $conn->query(
    "SELECT
        r.id,
        r.source,
        r.destination,
        r.departure_time,
        r.arrival_time,
        r.journey_date,
        r.price,
        b.bus_number,
        b.bus_name,
        b.bus_type,
        b.total_seats
     FROM routes r
     INNER JOIN buses b ON r.bus_id = b.id
     ORDER BY r.journey_date ASC, r.departure_time ASC"
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manage Routes - Admin</title>

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

        .add-btn {
            background: #198754;
        }

        .logout-btn {
            background: #dc3545;
        }

        .dashboard-btn:hover {
            background: #0056b3;
        }

        .add-btn:hover {
            background: #146c43;
        }

        .logout-btn:hover {
            background: #b02a37;
        }

        /* Container */

        .container {
            width: 95%;
            max-width: 1400px;
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

        /* Message */

        .message {
            background: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 7px;
            margin-bottom: 20px;
        }

        /* Table */

        .table-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.08);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1100px;
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

        /* Delete */

        .delete-btn {
            display: inline-block;
            text-decoration: none;
            background: #dc3545;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            font-size: 14px;
        }

        .delete-btn:hover {
            background: #b02a37;
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

        .empty a {
            display: inline-block;
            margin-top: 15px;
            text-decoration: none;
            background: #007bff;
            color: white;
            padding: 10px 16px;
            border-radius: 6px;
        }

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

            <a href="dashboard.php" class="dashboard-btn">
                Dashboard
            </a>

            <a href="add_route.php" class="add-btn">
                Add Route
            </a>

            <a href="logout.php" class="logout-btn">
                Logout
            </a>

        </div>

    </div>


    <!-- Main Content -->

    <div class="container">

        <div class="page-header">

            <h1>Manage Routes</h1>

            <p>
                View and manage all bus routes.
            </p>

        </div>


        <?php if (!empty($message)): ?>

            <div class="message">
                <?php echo htmlspecialchars($message); ?>
            </div>

        <?php endif; ?>


        <div class="table-container">

            <?php if ($result->num_rows > 0): ?>

                <table>

                    <thead>

                        <tr>

                            <th>ID</th>

                            <th>Bus</th>

                            <th>Bus Number</th>

                            <th>Type</th>

                            <th>Source</th>

                            <th>Destination</th>

                            <th>Departure</th>

                            <th>Arrival</th>

                            <th>Journey Date</th>

                            <th>Price</th>

                            <th>Action</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php while ($route = $result->fetch_assoc()): ?>

                            <tr>

                                <td>
                                    <?php echo $route["id"]; ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($route["bus_name"]); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($route["bus_number"]); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($route["bus_type"]); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($route["source"]); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($route["destination"]); ?>
                                </td>

                                <td>
                                    <?php echo date(
                                        "h:i A",
                                        strtotime($route["departure_time"])
                                    ); ?>
                                </td>

                                <td>
                                    <?php echo date(
                                        "h:i A",
                                        strtotime($route["arrival_time"])
                                    ); ?>
                                </td>

                                <td>
                                    <?php echo date(
                                        "d M Y",
                                        strtotime($route["journey_date"])
                                    ); ?>
                                </td>

                                <td>
                                    ₹<?php echo number_format(
                                        $route["price"],
                                        2
                                    ); ?>
                                </td>

                                <td>

                                    <a
                                        href="manage_routes.php?delete=<?php echo $route["id"]; ?>"
                                        class="delete-btn"
                                        onclick="return confirm('Are you sure you want to delete this route?');"
                                    >
                                        Delete
                                    </a>

                                </td>

                            </tr>

                        <?php endwhile; ?>

                    </tbody>

                </table>

            <?php else: ?>

                <div class="empty">

                    <h3>No routes found.</h3>

                    <p>
                        Add a route to start managing your bus journeys.
                    </p>

                    <a href="add_route.php">
                        Add Route
                    </a>

                </div>

            <?php endif; ?>

        </div>

    </div>

</body>

</html>