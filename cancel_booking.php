<?php

session_start();

require_once "config/db_connection.php";

// User must be logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

$booking_id = intval($_GET["booking_id"] ?? 0);

if ($booking_id <= 0) {
    die("Invalid booking ID.");
}


// Check whether the booking belongs to the logged-in user
$sql = "
    SELECT id, status
    FROM bookings
    WHERE id = ?
    AND user_id = ?
";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "ii",
    $booking_id,
    $user_id
);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $stmt->close();

    die("Booking not found or you do not have permission to cancel it.");
}

$booking = $result->fetch_assoc();

$stmt->close();


// Check current status
if ($booking["status"] === "Cancelled") {

    header("Location: my_bookings.php");

    exit();
}


// Cancel booking
$sql_cancel = "
    UPDATE bookings
    SET status = 'Cancelled'
    WHERE id = ?
    AND user_id = ?
    AND status = 'Confirmed'
";

$stmt = $conn->prepare($sql_cancel);

$stmt->bind_param(
    "ii",
    $booking_id,
    $user_id
);

$stmt->execute();

$stmt->close();


// Go back to My Bookings
header("Location: my_bookings.php");

exit();

?>