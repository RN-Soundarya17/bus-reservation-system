<?php

session_start();

require_once "config/db_connection.php";

// Check whether user is logged in
$is_logged_in = isset($_SESSION["user_id"]);

?>

<!DOCTYPE html>

<html lang="en">

<head>


<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>BusBook | Online Bus Reservation</title>

<style>

    /* =========================================
       GLOBAL
    ========================================= */

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: Arial, Helvetica, sans-serif;
    }

    html {
        scroll-behavior: smooth;
    }

    body {
        background: #f6f8fc;
        color: #222;
        line-height: 1.6;
    }

    a {
        text-decoration: none;
    }


    /* =========================================
       NAVIGATION
    ========================================= */

    nav {
        height: 74px;
        background: rgba(255, 255, 255, 0.97);
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 7%;
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
        position: sticky;
        top: 0;
        z-index: 1000;
    }

    .logo {
        font-size: 26px;
        font-weight: 800;
        color: #5b5fe9;
        letter-spacing: -0.5px;
    }

    .logo span {
        color: #222;
    }

    .nav-links {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .nav-links a {
        color: #444;
        font-size: 15px;
        font-weight: 600;
        padding: 10px 15px;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .nav-links a:hover {
        color: #5b5fe9;
        background: #f1f2ff;
    }

    .login-btn {
        background: #5b5fe9 !important;
        color: white !important;
        padding: 10px 20px !important;
        box-shadow: 0 5px 15px rgba(91, 95, 233, 0.25);
    }

    .login-btn:hover {
        background: #4b4fd4 !important;
        transform: translateY(-2px);
    }

    .register-btn {
        border: 1px solid #5b5fe9;
        color: #5b5fe9 !important;
    }

    .register-btn:hover {
        background: #5b5fe9 !important;
        color: white !important;
    }

    .admin-btn {
        background: #222 !important;
        color: white !important;
        padding: 10px 18px !important;
    }

    .admin-btn:hover {
        background: #444 !important;
        transform: translateY(-2px);
    }


    /* =========================================
       HERO
    ========================================= */

    .hero {
        min-height: 590px;
        position: relative;
        overflow: hidden;
        background:
            radial-gradient(
                circle at 15% 20%,
                rgba(255,255,255,0.18),
                transparent 30%
            ),
            radial-gradient(
                circle at 85% 80%,
                rgba(255,255,255,0.12),
                transparent 30%
            ),
            linear-gradient(
                135deg,
                #5b5fe9 0%,
                #6951d8 50%,
                #764ba2 100%
            );

        display: flex;
        justify-content: center;
        align-items: center;

        padding: 70px 7%;
    }


    /* Decorative circles */

    .hero::before {
        content: "";
        position: absolute;
        width: 350px;
        height: 350px;
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 50%;
        top: -120px;
        right: -80px;
    }

    .hero::after {
        content: "";
        position: absolute;
        width: 250px;
        height: 250px;
        border: 1px solid rgba(255,255,255,0.10);
        border-radius: 50%;
        bottom: -100px;
        left: -70px;
    }

    .hero-content {
        width: 100%;
        max-width: 1100px;
        position: relative;
        z-index: 2;
    }

    .hero-text {
        text-align: center;
        color: white;
        margin-bottom: 35px;
    }

    .hero-badge {
        display: inline-block;
        background: rgba(255,255,255,0.16);
        border: 1px solid rgba(255,255,255,0.25);
        padding: 8px 18px;
        border-radius: 30px;
        font-size: 14px;
        margin-bottom: 18px;
        backdrop-filter: blur(5px);
    }

    .hero h1 {
        font-size: 52px;
        line-height: 1.15;
        margin-bottom: 18px;
        font-weight: 800;
        letter-spacing: -1px;
    }

    .hero h1 span {
        color: #ffe082;
    }

    .hero-text p {
        max-width: 650px;
        margin: auto;
        font-size: 18px;
        color: rgba(255,255,255,0.9);
    }


    /* =========================================
       SEARCH BOX
    ========================================= */

    .search-box {
        background: white;
        border-radius: 18px;
        padding: 30px;
        box-shadow:
            0 20px 50px rgba(0,0,0,0.20);

        max-width: 1050px;
        margin: auto;
    }

    .search-title {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 25px;
    }

    .search-title h2 {
        font-size: 23px;
        color: #222;
    }

    .search-title p {
        color: #888;
        font-size: 14px;
    }

    .search-form {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr auto;
        gap: 15px;
        align-items: end;
    }

    .input-group {
        text-align: left;
    }

    .input-group label {
        display: block;
        font-size: 14px;
        font-weight: 700;
        color: #444;
        margin-bottom: 8px;
    }

    .input-group input {
        width: 100%;
        height: 48px;
        padding: 0 14px;
        border: 1px solid #d9dce5;
        border-radius: 9px;
        font-size: 15px;
        color: #333;
        outline: none;
        transition: 0.3s;
        background: #fafbfe;
    }

    .input-group input:focus {
        border-color: #5b5fe9;
        background: white;
        box-shadow: 0 0 0 3px rgba(91,95,233,0.10);
    }

    .search-btn {
        height: 48px;
        padding: 0 28px;
        border: none;
        border-radius: 9px;
        background: #5b5fe9;
        color: white;
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
        transition: 0.3s;
        white-space: nowrap;
    }

    .search-btn:hover {
        background: #4b4fd4;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(91,95,233,0.25);
    }


    /* =========================================
       QUICK STATS
    ========================================= */

    .stats {
        background: white;
        padding: 25px 7%;
        border-bottom: 1px solid #eee;
    }

    .stats-container {
        max-width: 1100px;
        margin: auto;

        display: grid;
        grid-template-columns: repeat(3, 1fr);

        text-align: center;
    }

    .stat {
        padding: 10px;
        border-right: 1px solid #eee;
    }

    .stat:last-child {
        border-right: none;
    }

    .stat h3 {
        color: #5b5fe9;
        font-size: 25px;
        margin-bottom: 3px;
    }

    .stat p {
        color: #777;
        font-size: 14px;
    }


    /* =========================================
       SECTION COMMON
    ========================================= */

    .section {
        padding: 75px 7%;
    }

    .section-heading {
        text-align: center;
        max-width: 650px;
        margin: 0 auto 45px;
    }

    .section-heading .small-title {
        color: #5b5fe9;
        font-size: 13px;
        font-weight: 800;
        letter-spacing: 1.5px;
        text-transform: uppercase;
    }

    .section-heading h2 {
        font-size: 34px;
        margin: 10px 0;
        color: #222;
    }

    .section-heading p {
        color: #777;
        font-size: 16px;
    }


    /* =========================================
       FEATURES
    ========================================= */

    .feature-container {
        max-width: 1100px;
        margin: auto;

        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 24px;
    }

    .feature-card {
        background: white;
        padding: 32px 28px;
        border-radius: 15px;
        border: 1px solid #eee;
        box-shadow: 0 8px 25px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        text-align: left;
    }

    .feature-card:hover {
        transform: translateY(-7px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.10);
        border-color: #dcdcff;
    }

    .icon {
        width: 58px;
        height: 58px;
        display: flex;
        justify-content: center;
        align-items: center;
        background: #f0f1ff;
        border-radius: 13px;
        font-size: 28px;
        margin-bottom: 20px;
    }

    .feature-card h3 {
        font-size: 19px;
        margin-bottom: 10px;
        color: #222;
    }

    .feature-card p {
        color: #777;
        font-size: 14px;
        line-height: 1.7;
    }


    /* =========================================
       HOW IT WORKS
    ========================================= */

    .how-section {
        background: white;
    }

    .steps {
        max-width: 1000px;
        margin: auto;

        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 25px;
    }

    .step {
        text-align: center;
        padding: 25px;
        position: relative;
    }

    .step-number {
        width: 65px;
        height: 65px;
        margin: 0 auto 20px;

        display: flex;
        justify-content: center;
        align-items: center;

        border-radius: 50%;

        background: linear-gradient(
            135deg,
            #5b5fe9,
            #764ba2
        );

        color: white;
        font-size: 20px;
        font-weight: 800;

        box-shadow: 0 8px 20px rgba(91,95,233,0.25);
    }

    .step h3 {
        font-size: 19px;
        margin-bottom: 8px;
    }

    .step p {
        color: #777;
        font-size: 14px;
    }


    /* =========================================
       CTA
    ========================================= */

    .cta-section {
        padding: 65px 7%;
    }

    .cta {
        max-width: 1100px;
        margin: auto;

        background:
            linear-gradient(
                135deg,
                #5b5fe9,
                #764ba2
            );

        border-radius: 20px;
        padding: 45px;

        color: white;

        display: flex;
        align-items: center;
        justify-content: space-between;

        box-shadow: 0 15px 35px rgba(91,95,233,0.20);
    }

    .cta h2 {
        font-size: 30px;
        margin-bottom: 8px;
    }

    .cta p {
        color: rgba(255,255,255,0.85);
    }

    .cta-btn {
        display: inline-block;
        background: white;
        color: #5b5fe9;
        padding: 13px 25px;
        border-radius: 9px;
        font-weight: 700;
        transition: 0.3s;
    }

    .cta-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }


    /* =========================================
       FOOTER
    ========================================= */

    footer {
        background: #18181b;
        color: white;
        padding: 45px 7% 20px;
    }

    .footer-content {
        max-width: 1100px;
        margin: auto;

        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 50px;

        padding-bottom: 35px;
    }

    .footer-brand h2 {
        color: #8b8ef5;
        margin-bottom: 10px;
    }

    .footer-brand p {
        color: #aaa;
        max-width: 400px;
        font-size: 14px;
    }

    .footer-links {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .footer-links h3 {
        margin-bottom: 8px;
    }

    .footer-links a {
        color: #aaa;
        font-size: 14px;
        transition: 0.3s;
    }

    .footer-links a:hover {
        color: white;
    }

    .footer-bottom {
        max-width: 1100px;
        margin: auto;
        padding-top: 20px;
        border-top: 1px solid #333;
        text-align: center;
        color: #888;
        font-size: 13px;
    }


    /* =========================================
       RESPONSIVE
    ========================================= */

    @media (max-width: 900px) {

        nav {
            padding: 0 4%;
        }

        .hero {
            padding: 60px 4%;
        }

        .hero h1 {
            font-size: 42px;
        }

        .search-form {
            grid-template-columns: 1fr 1fr;
        }

        .search-btn {
            width: 100%;
        }

        .feature-container {
            grid-template-columns: 1fr 1fr;
        }

        .steps {
            grid-template-columns: 1fr 1fr;
        }

        .section {
            padding: 60px 5%;
        }

    }


    @media (max-width: 650px) {

        nav {
            height: auto;
            padding: 15px 20px;
            flex-direction: column;
            gap: 12px;
        }

        .nav-links {
            flex-wrap: wrap;
            justify-content: center;
            gap: 5px;
        }

        .nav-links a {
            font-size: 13px;
            padding: 8px 10px;
        }

        .hero {
            padding: 55px 18px;
        }

        .hero h1 {
            font-size: 34px;
        }

        .hero-text p {
            font-size: 15px;
        }

        .search-box {
            padding: 22px;
        }

        .search-title {
            display: block;
        }

        .search-title h2 {
            margin-bottom: 5px;
        }

        .search-form {
            grid-template-columns: 1fr;
        }

        .stats-container {
            grid-template-columns: 1fr;
        }

        .stat {
            border-right: none;
            border-bottom: 1px solid #eee;
            padding: 15px;
        }

        .stat:last-child {
            border-bottom: none;
        }

        .feature-container {
            grid-template-columns: 1fr;
        }

        .steps {
            grid-template-columns: 1fr;
        }

        .section-heading h2 {
            font-size: 28px;
        }

        .cta {
            padding: 30px 25px;
            flex-direction: column;
            align-items: flex-start;
            gap: 25px;
        }

        .cta h2 {
            font-size: 25px;
        }

        .footer-content {
            grid-template-columns: 1fr;
        }

    }

</style>


</head>

<body>

<!-- =========================================
     NAVIGATION
========================================= -->

<nav>


<div class="logo">
    🚌 BusBook
</div>


<div class="nav-links">

    <a href="index.php">
        Home
    </a>


    <?php if ($is_logged_in): ?>

        <a href="my_bookings.php">
            My Bookings
        </a>

        <a href="logout.php">
            Logout
        </a>

    <?php else: ?>

        <a href="login.php" class="login-btn">
            Login
        </a>

        <a href="register.php" class="register-btn">
            Register
        </a>

    <?php endif; ?>


    <!-- ADMIN -->

    <a href="admin/login.php" class="admin-btn">
        Admin
    </a>

</div>


</nav>

<!-- =========================================
     HERO SECTION
========================================= -->

<section class="hero">


<div class="hero-content">


    <div class="hero-text">

        <div class="hero-badge">
            ✨ Simple • Fast • Convenient
        </div>

        <h1>
            Your Journey Starts <span>Here</span>
        </h1>

        <p>
            Search for buses, choose your seat and
            book your journey easily with BusBook.
        </p>

    </div>


    <!-- SEARCH -->

    <div class="search-box">

        <div class="search-title">

            <div>

                <h2>
                    🔎 Search Buses
                </h2>

                <p>
                    Find buses for your next journey
                </p>

            </div>

        </div>


        <form
            class="search-form"
            action="search.php"
            method="GET"
        >


            <!-- FROM -->

            <div class="input-group">

                <label for="source">
                    From
                </label>

                <input
                    type="text"
                    id="source"
                    name="source"
                    placeholder="Enter source city"
                    required
                >

            </div>


            <!-- TO -->

            <div class="input-group">

                <label for="destination">
                    To
                </label>

                <input
                    type="text"
                    id="destination"
                    name="destination"
                    placeholder="Enter destination city"
                    required
                >

            </div>


            <!-- DATE -->

            <div class="input-group">

                <label for="journey_date">
                    Journey Date
                </label>

                <input
                    type="date"
                    id="journey_date"
                    name="journey_date"
                    min="<?php echo date('Y-m-d'); ?>"
                    required
                >

            </div>


            <!-- BUTTON -->

            <button
                type="submit"
                class="search-btn"
            >
                Search Buses →
            </button>

        </form>

    </div>

</div>


</section>

<!-- =========================================
     STATS
========================================= -->

<section class="stats">


<div class="stats-container">


    <div class="stat">

        <h3>
            🚌
        </h3>

        <p>
            Comfortable Bus Travel
        </p>

    </div>


    <div class="stat">

        <h3>
            💺
        </h3>

        <p>
            Easy Seat Selection
        </p>

    </div>


    <div class="stat">

        <h3>
            🎫
        </h3>

        <p>
            Instant Booking Ticket
        </p>

    </div>

</div>


</section>

<!-- =========================================
     FEATURES
========================================= -->

<section class="section">


<div class="section-heading">

    <div class="small-title">
        Why Choose Us
    </div>

    <h2>
        Everything You Need for Your Journey
    </h2>

    <p>
        BusBook makes your bus reservation simple,
        convenient and easy to manage.
    </p>

</div>


<div class="feature-container">


    <!-- FEATURE 1 -->

    <div class="feature-card">

        <div class="icon">
            🔍
        </div>

        <h3>
            Easy Bus Search
        </h3>

        <p>
            Search available buses using your
            source, destination and journey date.
        </p>

    </div>


    <!-- FEATURE 2 -->

    <div class="feature-card">

        <div class="icon">
            💺
        </div>

        <h3>
            Choose Your Seat
        </h3>

        <p>
            View available and booked seats
            and select your preferred seat.
        </p>

    </div>


    <!-- FEATURE 3 -->

    <div class="feature-card">

        <div class="icon">
            🎫
        </div>

        <h3>
            Instant Ticket
        </h3>

        <p>
            After booking, view your ticket
            details and print your reservation.
        </p>

    </div>

</div>


</section>

<!-- =========================================
     HOW IT WORKS
========================================= -->

<section class="section how-section">


<div class="section-heading">

    <div class="small-title">
        Simple Process
    </div>

    <h2>
        How BusBook Works
    </h2>

    <p>
        Book your bus ticket in just three simple steps.
    </p>

</div>


<div class="steps">


    <!-- STEP 1 -->

    <div class="step">

        <div class="step-number">
            01
        </div>

        <h3>
            Search
        </h3>

        <p>
            Enter your source, destination and
            journey date.
        </p>

    </div>


    <!-- STEP 2 -->

    <div class="step">

        <div class="step-number">
            02
        </div>

        <h3>
            Select
        </h3>

        <p>
            Select your preferred bus and
            available seat.
        </p>

    </div>


    <!-- STEP 3 -->

    <div class="step">

        <div class="step-number">
            03
        </div>

        <h3>
            Book
        </h3>

        <p>
            Enter passenger details and
            confirm your booking.
        </p>

    </div>

</div>


</section>

<!-- =========================================
     CALL TO ACTION
========================================= -->

<section class="cta-section">


<div class="cta">

    <div>

        <h2>
            Ready to Start Your Journey?
        </h2>

        <p>
            Search for your bus and reserve your seat today.
        </p>

    </div>


    <a
        href="#"
        onclick="document.getElementById('source').focus(); return false;"
        class="cta-btn"
    >
        Book Your Bus →
    </a>

</div>


</section>

<!-- =========================================
     FOOTER
========================================= -->

<footer>


<div class="footer-content">


    <div class="footer-brand">

        <h2>
            🚌 BusBook
        </h2>

        <p>
            Online bus ticket reservation system
            designed to make your journey simple
            and convenient.
        </p>

    </div>


    <div class="footer-links">

        <h3>
            Quick Links
        </h3>

        <a href="index.php">
            Home
        </a>

        <a href="login.php">
            Login
        </a>

        <a href="register.php">
            Register
        </a>

        <a href="admin/login.php">
            Admin Login
        </a>

    </div>

</div>


<div class="footer-bottom">

    <p>
        © <?php echo date("Y"); ?>
        BusBook. All Rights Reserved.
    </p>

</div>


</footer>

</body>

</html>
