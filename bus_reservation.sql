-- =========================================================
-- Online Bus Ticket Reservation System
-- Database: bus_reservation
-- =========================================================

CREATE DATABASE IF NOT EXISTS bus_reservation;

USE bus_reservation;


-- =========================================================
-- 1. USERS TABLE
-- =========================================================

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(15) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- =========================================================
-- 2. ADMINS TABLE
-- =========================================================

CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- =========================================================
-- 3. BUSES TABLE
-- =========================================================

CREATE TABLE IF NOT EXISTS buses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bus_number VARCHAR(50) NOT NULL UNIQUE,
    bus_name VARCHAR(100) NOT NULL,
    bus_type VARCHAR(50) NOT NULL,
    total_seats INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- =========================================================
-- 4. ROUTES TABLE
-- =========================================================

CREATE TABLE IF NOT EXISTS routes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bus_id INT NOT NULL,
    source VARCHAR(100) NOT NULL,
    destination VARCHAR(100) NOT NULL,
    departure_time TIME NOT NULL,
    arrival_time TIME NOT NULL,
    journey_date DATE NOT NULL,
    price DECIMAL(10,2) NOT NULL,

    FOREIGN KEY (bus_id)
        REFERENCES buses(id)
        ON DELETE CASCADE
);


-- =========================================================
-- 5. BOOKINGS TABLE
-- =========================================================

CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    bus_id INT NOT NULL,
    route_id INT NOT NULL,
    seat_number VARCHAR(10) NOT NULL,
    booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Confirmed', 'Cancelled') DEFAULT 'Confirmed',

    FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE,

    FOREIGN KEY (bus_id)
        REFERENCES buses(id)
        ON DELETE CASCADE,

    FOREIGN KEY (route_id)
        REFERENCES routes(id)
        ON DELETE CASCADE
);


-- =========================================================
-- 6. PASSENGERS TABLE
-- =========================================================

CREATE TABLE IF NOT EXISTS passengers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    passenger_name VARCHAR(100) NOT NULL,
    age INT NOT NULL,
    gender VARCHAR(20) NOT NULL,

    FOREIGN KEY (booking_id)
        REFERENCES bookings(id)
        ON DELETE CASCADE
);


-- =========================================================
-- ADMIN ACCOUNT
-- =========================================================

INSERT INTO admins (name, email, password)
SELECT 'Admin', 'admin@gmail.com', 'admin123'
WHERE NOT EXISTS (
    SELECT 1
    FROM admins
    WHERE email = 'admin@gmail.com'
);


-- =========================================================
-- SAMPLE BUSES
-- =========================================================

INSERT INTO buses
    (bus_number, bus_name, bus_type, total_seats)
SELECT
    'KA01AB1234',
    'Express Travels',
    'AC Sleeper',
    40
WHERE NOT EXISTS (
    SELECT 1
    FROM buses
    WHERE bus_number = 'KA01AB1234'
);


INSERT INTO buses
    (bus_number, bus_name, bus_type, total_seats)
SELECT
    'KA02CD5678',
    'City Travels',
    'AC Seater',
    40
WHERE NOT EXISTS (
    SELECT 1
    FROM buses
    WHERE bus_number = 'KA02CD5678'
);


INSERT INTO buses
    (bus_number, bus_name, bus_type, total_seats)
SELECT
    'TN01EF9012',
    'South India Travels',
    'Non-AC Seater',
    50
WHERE NOT EXISTS (
    SELECT 1
    FROM buses
    WHERE bus_number = 'TN01EF9012'
);


-- =========================================================
-- SAMPLE ROUTES
-- =========================================================

INSERT INTO routes
    (bus_id, source, destination, departure_time, arrival_time, journey_date, price)
SELECT
    b.id,
    'Bangalore',
    'Chennai',
    '08:00:00',
    '14:00:00',
    '2026-09-25',
    650.00
FROM buses b
WHERE b.bus_number = 'KA01AB1234'
AND NOT EXISTS (
    SELECT 1
    FROM routes r
    WHERE r.bus_id = b.id
      AND r.source = 'Bangalore'
      AND r.destination = 'Chennai'
      AND r.journey_date = '2026-09-25'
      AND r.departure_time = '08:00:00'
);


INSERT INTO routes
    (bus_id, source, destination, departure_time, arrival_time, journey_date, price)
SELECT
    b.id,
    'Bangalore',
    'Chennai',
    '21:00:00',
    '05:00:00',
    '2026-09-25',
    800.00
FROM buses b
WHERE b.bus_number = 'KA02CD5678'
AND NOT EXISTS (
    SELECT 1
    FROM routes r
    WHERE r.bus_id = b.id
      AND r.source = 'Bangalore'
      AND r.destination = 'Chennai'
      AND r.journey_date = '2026-09-25'
      AND r.departure_time = '21:00:00'
);


INSERT INTO routes
    (bus_id, source, destination, departure_time, arrival_time, journey_date, price)
SELECT
    b.id,
    'Bangalore',
    'Mysore',
    '09:00:00',
    '12:00:00',
    '2026-09-25',
    300.00
FROM buses b
WHERE b.bus_number = 'TN01EF9012'
AND NOT EXISTS (
    SELECT 1
    FROM routes r
    WHERE r.bus_id = b.id
      AND r.source = 'Bangalore'
      AND r.destination = 'Mysore'
      AND r.journey_date = '2026-09-25'
      AND r.departure_time = '09:00:00'
);


INSERT INTO routes
    (bus_id, source, destination, departure_time, arrival_time, journey_date, price)
SELECT
    b.id,
    'Davangere',
    'Bengaluru',
    '08:00:00',
    '13:00:00',
    '2026-10-10',
    450.00
FROM buses b
WHERE b.bus_number = 'KA01AB1234'
AND NOT EXISTS (
    SELECT 1
    FROM routes r
    WHERE r.bus_id = b.id
      AND r.source = 'Davangere'
      AND r.destination = 'Bengaluru'
      AND r.journey_date = '2026-10-10'
      AND r.departure_time = '08:00:00'
);


INSERT INTO routes
    (bus_id, source, destination, departure_time, arrival_time, journey_date, price)
SELECT
    b.id,
    'Davangere',
    'Bengaluru',
    '08:00:00',
    '13:00:00',
    '2026-10-07',
    450.00
FROM buses b
WHERE b.bus_number = 'KA01AB1234'
AND NOT EXISTS (
    SELECT 1
    FROM routes r
    WHERE r.bus_id = b.id
      AND r.source = 'Davangere'
      AND r.destination = 'Bengaluru'
      AND r.journey_date = '2026-10-07'
      AND r.departure_time = '08:00:00'
);


INSERT INTO routes
    (bus_id, source, destination, departure_time, arrival_time, journey_date, price)
SELECT
    b.id,
    'Davangere',
    'Bengaluru',
    '10:00:00',
    '15:00:00',
    '2026-10-07',
    500.00
FROM buses b
WHERE b.bus_number = 'KA02CD5678'
AND NOT EXISTS (
    SELECT 1
    FROM routes r
    WHERE r.bus_id = b.id
      AND r.source = 'Davangere'
      AND r.destination = 'Bengaluru'
      AND r.journey_date = '2026-10-07'
      AND r.departure_time = '10:00:00'
);


INSERT INTO routes
    (bus_id, source, destination, departure_time, arrival_time, journey_date, price)
SELECT
    b.id,
    'Davangere',
    'Bengaluru',
    '21:00:00',
    '02:00:00',
    '2026-10-07',
    550.00
FROM buses b
WHERE b.bus_number = 'TN01EF9012'
AND NOT EXISTS (
    SELECT 1
    FROM routes r
    WHERE r.bus_id = b.id
      AND r.source = 'Davangere'
      AND r.destination = 'Bengaluru'
      AND r.journey_date = '2026-10-07'
      AND r.departure_time = '21:00:00'
);


-- =========================================================
-- END OF DATABASE SCRIPT
-- =========================================================
Email:    admin@gmail.com
Password: admin123