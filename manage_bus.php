<?php

session_start();

require_once "../config/db_connection.php";

// Check admin login
if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit();
}

$message = "";

// Delete bus
if (isset($_GET["delete"])) {

    $bus_id = intval($_GET["delete"]);

    if ($bus_id > 0) {

        $stmt = $conn->prepare("DELETE FROM buses WHERE id = ?");
        $stmt->bind_param("i", $bus_id);

        if ($stmt->execute()) {
            $message = "Bus deleted successfully.";
        } else {
            $message = "Unable to delete bus.";
        }

        $stmt->close();
    }
}

// Get all buses
$result = $conn->query(
    "SELECT id, bus_number, bus_name, bus_type, total_seats, created_at
     FROM buses
     ORDER BY id DESC"
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manage Buses - Admin</title>

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
            max-width: 1200px;
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
            min-width: 750px;
        }

        th,
        td {
            padding: 14px 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #f1f3f5;
            color: #333;
        }

        tr:hover {
            background: #f8f9fa;
        }

        /* Delete button */

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
            padding: 35px;
            color: #777;
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

            <a href="dashboard.php" class="dashboard-btn">
                Dashboard
            </a>

            <a href="add_bus.php" class="add-btn">
                Add Bus
            </a>

            <a href="logout.php" class="logout-btn">
                Logout
            </a>

        </div>

    </div>


    <!-- Main Content -->

    <div class="container">

        <div class="page-header">

            <h1>Manage Buses</h1>

            <p>
                View and manage all buses in the system.
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

                            <th>Bus Number</th>

                            <th>Bus Name</th>

                            <th>Bus Type</th>

                            <th>Total Seats</th>

                            <th>Created Date</th>

                            <th>Action</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php while ($bus = $result->fetch_assoc()): ?>

                            <tr>

                                <td>
                                    <?php echo $bus["id"]; ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($bus["bus_number"]); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($bus["bus_name"]); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($bus["bus_type"]); ?>
                                </td>

                                <td>
                                    <?php echo $bus["total_seats"]; ?>
                                </td>

                                <td>
                                    <?php echo date(
                                        "d M Y",
                                        strtotime($bus["created_at"])
                                    ); ?>
                                </td>

                                <td>

                                    <a
                                        href="manage_bus.php?delete=<?php echo $bus["id"]; ?>"
                                        class="delete-btn"
                                        onclick="return confirm('Are you sure you want to delete this bus?');"
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

                    <h3>No buses found.</h3>

                    <p>Add a bus to start managing your buses.</p>

                    <a href="add_bus.php">
                        Add Bus
                    </a>

                </div>

            <?php endif; ?>

        </div>

    </div>

</body>

</html>