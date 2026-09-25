<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit();
}

require_once "../config/db_connection.php";

$sql = "SELECT id, name, email, phone, created_at
        FROM users
        ORDER BY created_at DESC";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manage Users - Bus Reservation</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            color: #333;
        }

        .navbar {
            background: #1f2937;
            color: white;
            padding: 18px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar h2 {
            margin: 0;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
        }

        .navbar a:hover {
            text-decoration: underline;
        }

        .container {
            width: 95%;
            max-width: 1200px;
            margin: 40px auto;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .page-header h1 {
            color: #1f2937;
        }

        .back-btn {
            background: #374151;
            color: white;
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 6px;
        }

        .back-btn:hover {
            background: #111827;
        }

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
            min-width: 700px;
        }

        th,
        td {
            padding: 14px 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        th {
            background: #f3f4f6;
            color: #374151;
        }

        tr:hover {
            background: #f9fafb;
        }

        .user-count {
            margin-bottom: 15px;
            color: #555;
        }

        .empty {
            text-align: center;
            padding: 40px;
            color: #777;
        }

        .admin-name {
            font-size: 14px;
            color: #d1d5db;
            margin-right: 10px;
        }
    </style>
</head>

<body>

    <div class="navbar">

        <h2>Bus Reservation - Admin</h2>

        <div>
            <span class="admin-name">
                Welcome, <?php echo htmlspecialchars($_SESSION["admin_name"]); ?>
            </span>

            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        </div>

    </div>


    <div class="container">

        <div class="page-header">

            <h1>Registered Users</h1>

            <a href="dashboard.php" class="back-btn">
                Back to Dashboard
            </a>

        </div>


        <?php if ($result && $result->num_rows > 0): ?>

            <p class="user-count">
                Total Registered Users:
                <strong><?php echo $result->num_rows; ?></strong>
            </p>

            <div class="table-container">

                <table>

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Registered Date</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php while ($user = $result->fetch_assoc()): ?>

                            <tr>

                                <td>
                                    <?php echo htmlspecialchars($user["id"]); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($user["name"]); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($user["email"]); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($user["phone"]); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($user["created_at"]); ?>
                                </td>

                            </tr>

                        <?php endwhile; ?>

                    </tbody>

                </table>

            </div>

        <?php else: ?>

            <div class="table-container">

                <div class="empty">
                    <h3>No Registered Users</h3>
                    <p>No users have registered yet.</p>
                </div>

            </div>

        <?php endif; ?>

    </div>

</body>

</html>