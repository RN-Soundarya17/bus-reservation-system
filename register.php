<?php

require_once "config/db_connection.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    // Check empty fields
    if (empty($name) || empty($email) || empty($phone) || empty($password)) {
        $message = "Please fill all fields.";
    }

    // Check password
    elseif ($password !== $confirm_password) {
        $message = "Passwords do not match.";
    }

    // Check email
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
    }

    else {

        // Check whether email already exists
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {

            $message = "Email already registered.";

        } else {

            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert user
            $stmt = $conn->prepare(
                "INSERT INTO users (name, email, phone, password)
                 VALUES (?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "ssss",
                $name,
                $email,
                $phone,
                $hashed_password
            );

            if ($stmt->execute()) {

                $message = "Registration successful! You can now login.";

            } else {

                $message = "Registration failed. Please try again.";
            }

            $stmt->close();
        }

        $check->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Bus Reservation - Register</title>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #667eea, #764ba2);
        }

        .register-container {
            width: 400px;
            background: white;
            padding: 35px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        h2 {
            text-align: center;
            margin-bottom: 10px;
            color: #333;
        }

        .subtitle {
            text-align: center;
            color: #777;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            color: #333;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 12px;
            margin-bottom: 18px;
            border: 1px solid #ccc;
            border-radius: 7px;
            font-size: 15px;
        }

        input:focus {
            outline: none;
            border-color: #667eea;
        }

        button {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 7px;
            background: #667eea;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background: #5568d9;
        }

        .message {
            text-align: center;
            margin-bottom: 18px;
            color: #d9534f;
            font-weight: bold;
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
        }

        .login-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: bold;
        }

    </style>

</head>

<body>

    <div class="register-container">

        <h2>Create Account</h2>

        <p class="subtitle">
            Online Bus Ticket Reservation
        </p>

        <?php if (!empty($message)): ?>

            <div class="message">
                <?php echo htmlspecialchars($message); ?>
            </div>

        <?php endif; ?>

        <form method="POST">

            <label for="name">Full Name</label>

            <input
                type="text"
                id="name"
                name="name"
                placeholder="Enter your full name"
                required
            >

            <label for="email">Email</label>

            <input
                type="email"
                id="email"
                name="email"
                placeholder="Enter your email"
                required
            >

            <label for="phone">Phone Number</label>

            <input
                type="tel"
                id="phone"
                name="phone"
                placeholder="Enter your phone number"
                required
            >

            <label for="password">Password</label>

            <input
                type="password"
                id="password"
                name="password"
                placeholder="Enter password"
                required
            >

            <label for="confirm_password">Confirm Password</label>

            <input
                type="password"
                id="confirm_password"
                name="confirm_password"
                placeholder="Confirm password"
                required
            >

            <button type="submit">
                Register
            </button>

        </form>

        <div class="login-link">

            Already have an account?

            <a href="login.php">
                Login
            </a>

        </div>

    </div>

</body>

</html>