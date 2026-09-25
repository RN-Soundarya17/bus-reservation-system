<?php

session_start();

require_once "config/db_connection.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if (empty($email) || empty($password)) {

        $message = "Please enter email and password.";

    } else {

        $stmt = $conn->prepare(
            "SELECT id, name, email, password FROM users WHERE email = ?"
        );

        $stmt->bind_param("s", $email);

        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows == 1) {

            $user = $result->fetch_assoc();

            if (password_verify($password, $user["password"])) {

                $_SESSION["user_id"] = $user["id"];
                $_SESSION["user_name"] = $user["name"];
                $_SESSION["user_email"] = $user["email"];

                header("Location: index.php");
                exit();

            } else {

                $message = "Invalid email or password.";
            }

        } else {

            $message = "Invalid email or password.";
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

    <title>Bus Reservation - Login</title>

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

        .login-container {
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

        .register-link {
            text-align: center;
            margin-top: 20px;
        }

        .register-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: bold;
        }

    </style>

</head>

<body>

    <div class="login-container">

        <h2>Welcome Back</h2>

        <p class="subtitle">
            Login to your Bus Reservation Account
        </p>

        <?php if (!empty($message)): ?>

            <div class="message">
                <?php echo htmlspecialchars($message); ?>
            </div>

        <?php endif; ?>

        <form method="POST">

            <label for="email">Email</label>

            <input
                type="email"
                id="email"
                name="email"
                placeholder="Enter your email"
                required
            >

            <label for="password">Password</label>

            <input
                type="password"
                id="password"
                name="password"
                placeholder="Enter your password"
                required
            >

            <button type="submit">
                Login
            </button>

        </form>

        <div class="register-link">

            Don't have an account?

            <a href="register.php">
                Create Account
            </a>

        </div>

    </div>

</body>

</html>