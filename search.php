<?php

session_start();

require_once "config/db_connection.php";

$source = trim($_GET["source"] ?? "");
$destination = trim($_GET["destination"] ?? "");
$journey_date = trim($_GET["journey_date"] ?? "");

$buses = [];

// Convert user input to lowercase
$source_search = strtolower($source);
$destination_search = strtolower($destination);

// Handle common spelling
if ($destination_search == "banguluru" || $destination_search == "bangalore") {
    $destination_search = "bengaluru";
}

if ($source_search == "banguluru" || $source_search == "bangalore") {
    $source_search = "bengaluru";
}

if ($source_search !== "" && $destination_search !== "" && $journey_date !== "") {

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
        WHERE LOWER(TRIM(r.source)) = ?
        AND LOWER(TRIM(r.destination)) = ?
        AND r.journey_date = ?
        ORDER BY r.departure_time ASC
    ";

    $stmt = $conn->prepare($sql);

    if ($stmt) {

        $stmt->bind_param(
            "sss",
            $source_search,
            $destination_search,
            $journey_date
        );

        $stmt->execute();

        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $buses[] = $row;
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

    <title>Available Buses</title>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        body {
            background: #f5f7fb;
            color: #333;
        }

        header {
            background: white;
            padding: 20px 8%;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        header a {
            text-decoration: none;
            color: #667eea;
            font-size: 24px;
            font-weight: bold;
        }

        .container {
            width: 85%;
            max-width: 1100px;
            margin: 40px auto;
        }

        .search-info {
            background: white;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }

        .search-info h1 {
            margin-bottom: 10px;
            font-size: 28px;
        }

        .search-info p {
            color: #666;
            font-size: 16px;
        }

        .bus-card {
            background: white;
            padding: 25px;
            margin-bottom: 20px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }

        .bus-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .bus-name {
            font-size: 22px;
            font-weight: bold;
            color: #667eea;
        }

        .bus-type {
            background: #eee;
            padding: 7px 12px;
            border-radius: 20px;
            font-size: 13px;
        }

        .bus-details {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }

        .detail {
            text-align: center;
        }

        .detail strong {
            display: block;
            margin-bottom: 5px;
        }

        .detail span {
            color: #666;
        }

        .price {
            font-size: 22px;
            font-weight: bold;
        }

        .seat-btn {
            display: inline-block;
            text-decoration: none;
            background: #667eea;
            color: white;
            padding: 12px 22px;
            border-radius: 7px;
            font-weight: bold;
        }

        .seat-btn:hover {
            background: #5568d9;
        }

        .no-buses {
            background: white;
            padding: 50px;
            text-align: center;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }

        .no-buses h2 {
            margin-bottom: 10px;
        }

        .no-buses p {
            color: #666;
            margin-bottom: 20px;
        }

        .back-btn {
            display: inline-block;
            text-decoration: none;
            background: #667eea;
            color: white;
            padding: 12px 20px;
            border-radius: 7px;
        }

        @media (max-width: 800px) {

            .bus-details {
                grid-template-columns: 1fr 1fr;
            }

        }

        @media (max-width: 500px) {

            .bus-details {
                grid-template-columns: 1fr;
            }

            .container {
                width: 92%;
            }

        }

    </style>

</head>

<body>

<header>

    <a href="index.php">🚌 BusBook</a>

</header>

<div class="container">

    <div class="search-info">

        <h1>Available Buses</h1>

        <p>
            <?php echo htmlspecialchars($source); ?>
            →
            <?php echo htmlspecialchars($destination); ?>
            |
            <?php echo date("d M Y", strtotime($journey_date)); ?>
        </p>

    </div>


    <?php if (!empty($buses)): ?>

        <?php foreach ($buses as $bus): ?>

            <div class="bus-card">

                <div class="bus-header">

                    <div class="bus-name">
                        <?php echo htmlspecialchars($bus["bus_name"]); ?>
                    </div>

                    <div class="bus-type">
                        <?php echo htmlspecialchars($bus["bus_type"]); ?>
                    </div>

                </div>


                <div class="bus-details">

                    <div class="detail">

                        <strong>Bus Number</strong>

                        <span>
                            <?php echo htmlspecialchars($bus["bus_number"]); ?>
                        </span>

                    </div>


                    <div class="detail">

                        <strong>Departure</strong>

                        <span>
                            <?php
                            echo date(
                                "h:i A",
                                strtotime($bus["departure_time"])
                            );
                            ?>
                        </span>

                    </div>


                    <div class="detail">

                        <strong>Arrival</strong>

                        <span>
                            <?php
                            echo date(
                                "h:i A",
                                strtotime($bus["arrival_time"])
                            );
                            ?>
                        </span>

                    </div>


                    <div class="detail">

                        <strong>Seats</strong>

                        <span>
                            <?php echo $bus["total_seats"]; ?>
                        </span>

                    </div>

                </div>


                <div style="display:flex; justify-content:space-between; align-items:center;">

                    <div class="price">
                        ₹<?php echo number_format($bus["price"], 2); ?>
                    </div>

                    <a
                        href="seat_selection.php?route_id=<?php echo $bus["route_id"]; ?>"
                        class="seat-btn"
                    >
                        Select Seat
                    </a>

                </div>

            </div>

        <?php endforeach; ?>


    <?php else: ?>

        <div class="no-buses">

            <h2>No buses found</h2>

            <p>
                No buses are available for the selected route and date.
            </p>

            <a href="index.php" class="back-btn">
                ← Search Again
            </a>

        </div>

    <?php endif; ?>

</div>

</body>

</html>