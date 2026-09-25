# 🚌 Online Bus Ticket Reservation System

A web-based **Online Bus Ticket Reservation System** developed using **PHP, MySQL, HTML, CSS, JavaScript, and XAMPP**.

The system allows users to search for buses, select available seats, enter passenger details, book tickets, view booking details, print tickets, and cancel reservations.

An **Admin Panel** is also provided for managing buses, routes, users, and bookings.

---

## ✨ Features

### 👤 User Features

* User registration and login
* Bus search by source, destination, and journey date
* View available buses
* Interactive seat selection
* Passenger details entry
* Bus ticket booking
* Ticket generation
* Print ticket
* View booking history
* Cancel bookings
* View booking status

### 🔐 Admin Features

* Admin login
* Admin dashboard
* Add buses
* Manage buses
* Add routes
* Manage routes
* View registered users
* View all bookings
* View passenger details
* View confirmed and cancelled bookings

---

## 🛠️ Technologies Used

| Technology             | Purpose                            |
| ---------------------- | ---------------------------------- |
| **HTML5**              | Web page structure                 |
| **CSS3**               | User interface and styling         |
| **JavaScript**         | Client-side functionality          |
| **PHP**                | Backend and server-side processing |
| **MySQL**              | Database management                |
| **XAMPP**              | Local development environment      |
| **Apache**             | Web server                         |
| **phpMyAdmin**         | Database management                |
| **Visual Studio Code** | Development environment            |

---

## 🏗️ Project Structure

```text
bus-reservation-system/
│
├── admin/
│   ├── login.php
│   ├── dashboard.php
│   ├── logout.php
│   ├── add_bus.php
│   ├── manage_bus.php
│   ├── add_route.php
│   ├── manage_routes.php
│   ├── bookings.php
│   └── users.php
│
├── config/
│   └── db_connection.php
│
├── css/
│   └── style.css
│
├── database/
│   └── bus_reservation.sql
│
├── index.php
├── login.php
├── register.php
├── logout.php
├── search.php
├── seat_selection.php
├── booking.php
├── ticket.php
├── my_bookings.php
├── cancel_booking.php
└── README.md
```

---

## 🗄️ Database

The application uses **MySQL** for storing user, bus, route, booking, and passenger information.

### Database Name

```text
bus_reservation
```

### Main Tables

* `users` — Stores registered user information
* `admins` — Stores administrator information
* `buses` — Stores bus details
* `routes` — Stores bus routes and schedules
* `bookings` — Stores ticket booking information
* `passengers` — Stores passenger details

### SQL File

The database structure and sample data are stored in:

```text
database/bus_reservation.sql
```

---

## ⚙️ Installation and Setup

### 1. Install XAMPP

Install **XAMPP** on your computer.

Start the following services from the XAMPP Control Panel:

* Apache
* MySQL

---

### 2. Copy the Project

Place the project inside the XAMPP `htdocs` directory:

```text
C:\xampp\htdocs\bus_reservation
```

---

### 3. Create the Database

Open phpMyAdmin in your browser:

```text
http://localhost/phpmyadmin/
```

Create a database named:

```text
bus_reservation
```

Then import:

```text
database/bus_reservation.sql
```

---

### 4. Configure the Database Connection

Open:

```text
config/db_connection.php
```

Configure the database connection according to your MySQL settings.

For the current project setup:

```php
$host = "localhost";
$port = "3307";
$username = "root";
$password = "";
$database = "bus_reservation";
```

If your MySQL server uses the default port, change:

```text
3307
```

to:

```text
3306
```

---

### 5. Run the Project

Open a web browser and visit:

```text
http://localhost/bus_reservation/
```

The home page of the Online Bus Ticket Reservation System will be displayed.

---

## 👤 User Booking Flow

The user can book a bus ticket through the following process:

```text
Home Page
     ↓
Register / Login
     ↓
Search Bus
     ↓
View Available Buses
     ↓
Select Seat
     ↓
Enter Passenger Details
     ↓
Confirm Booking
     ↓
View Ticket
```

---

## 🔐 Admin Flow

The administrator can manage the system through the Admin Panel.

```text
Home Page
     ↓
Admin
     ↓
Admin Login
     ↓
Admin Dashboard
     ↓
Manage Buses
     ↓
Manage Routes
     ↓
View Users
     ↓
View Bookings
```

---

## 💺 Seat Management

The system provides dynamic seat selection based on the total number of seats available in a bus.

### Seat Status

| Status       | Description                         |
| ------------ | ----------------------------------- |
| 🟢 Available | Seat can be selected                |
| 🔴 Booked    | Seat is already booked              |
| 🟡 Selected  | Seat currently selected by the user |

Only confirmed bookings make a seat unavailable.

When a booking is cancelled, the corresponding seat becomes available again.

---

## 🎫 Ticket Booking

After selecting a bus and seat, the user enters passenger information such as:

* Passenger name
* Age
* Gender

After successful booking, the system generates a ticket containing:

* Booking ID
* Passenger name
* Bus name
* Bus number
* Source
* Destination
* Journey date
* Departure time
* Arrival time
* Seat number
* Ticket price
* Booking status

The user can also print the generated ticket.

---

## 📋 Booking Management

Users can access their booking history through:

```text
My Bookings
```

The booking history displays:

* Booking ID
* Passenger name
* Bus details
* Source
* Destination
* Journey date
* Seat number
* Price
* Booking date
* Booking status

Users can cancel confirmed bookings.

Cancelled bookings are retained in the database with the status:

```text
Cancelled
```

---

## 🛡️ Admin Management

The Admin Panel provides the following management functions.

### Bus Management

Administrators can:

* Add new buses
* View buses
* Manage bus details
* Delete buses

### Route Management

Administrators can:

* Add routes
* View routes
* Manage routes
* Delete routes
* Set departure and arrival times
* Set journey dates
* Set ticket prices

### User Management

Administrators can view:

* User ID
* User name
* Email
* Phone number
* Registration date

### Booking Management

Administrators can view:

* Booking ID
* User details
* Passenger details
* Bus details
* Route details
* Seat number
* Journey date
* Ticket price
* Booking status

---

## 🔒 Security

The project implements basic security practices including:

* Password hashing for user accounts
* Password verification during login
* Prepared SQL statements
* PHP session-based authentication
* Input validation
* Booking ownership verification
* Admin authentication

> This project is intended for academic and educational use. Additional security measures would be required before production deployment.

---

## 🧪 Testing

The system can be tested using the following test scenarios:

### User Testing

* User registration
* Duplicate email registration
* User login
* Invalid login
* Logout
* Bus search
* Invalid bus search
* Seat selection
* Already booked seat
* Passenger details validation
* Booking confirmation
* Ticket generation
* Ticket printing
* Booking history
* Booking cancellation

### Admin Testing

* Admin login
* Invalid admin login
* Admin dashboard
* Add bus
* Manage buses
* Delete bus
* Add route
* Manage routes
* Delete route
* View users
* View bookings
* Admin logout

---

## 🎯 Project Objectives

The main objectives of this project are:

* To develop an easy-to-use online bus reservation system.
* To reduce manual ticket booking processes.
* To allow users to search for available buses.
* To provide convenient seat selection.
* To maintain passenger and booking information.
* To provide centralized administration.
* To reduce errors in manual reservation management.
* To provide users with easy access to their booking information.

---

## 🚀 Future Enhancements

The following features can be added in future versions:

* Online payment gateway
* Email booking confirmation
* SMS notifications
* PDF ticket generation
* Password reset functionality
* User profile management
* Multiple boarding points
* Multiple dropping points
* Dynamic fare calculation
* Advanced bus search and filtering
* Admin analytics and reports
* Bus operator management
* Mobile application
* Cloud deployment

---

## 📸 Screenshots

Screenshots of the application can be added to this section.

Suggested screenshots include:

1. Home Page
2. User Registration
3. User Login
4. Bus Search
5. Available Buses
6. Seat Selection
7. Passenger Details
8. Booking Ticket
9. My Bookings
10. Admin Login
11. Admin Dashboard
12. Bus Management
13. Route Management
14. Booking Management
15. User Management

---

## 📚 Learning Outcomes

This project provides practical experience in:

* PHP web development
* MySQL database design
* CRUD operations
* SQL queries
* SQL joins
* Prepared statements
* PHP sessions
* Authentication
* Form validation
* Database relationships
* Frontend and backend integration
* XAMPP configuration
* Apache web server
* Admin dashboard development

---

## 👩‍💻 Project Information

| Category                    | Details                              |
| --------------------------- | ------------------------------------ |
| **Project Name**            | Online Bus Ticket Reservation System |
| **Domain**                  | Web Application Development          |
| **Frontend**                | HTML5, CSS3, JavaScript              |
| **Backend**                 | PHP                                  |
| **Database**                | MySQL                                |
| **Web Server**              | Apache                               |
| **Development Environment** | XAMPP                                |
| **IDE**                     | Visual Studio Code                   |
| **Database Tool**           | phpMyAdmin                           |
| **Developer**               | RN Soundarya                         |

---

## 📂 Main Modules

The project is divided into the following modules:

### User Module

Handles:

* Registration
* Login
* Logout
* Bus search
* Seat selection
* Booking
* Ticket viewing
* Booking history
* Cancellation

### Admin Module

Handles:

* Admin authentication
* Dashboard
* Bus management
* Route management
* User management
* Booking management

### Database Module

Handles:

* User records
* Admin records
* Bus records
* Route records
* Booking records
* Passenger records

---

## 🌐 Application URL

When running locally using XAMPP:

```text
http://localhost/bus_reservation/
```

Admin login:

```text
http://localhost/bus_reservation/admin/login.php
```

---

## 📄 License

This project was developed for **educational and academic purposes**.
