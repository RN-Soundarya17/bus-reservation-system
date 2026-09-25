<?php

session_start();

require_once "../config/db_connection.php";

if (isset($_SESSION["admin_id"])) {
    header("Location: dashboard.php");
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    if (empty($email) || empty($password)) {

        $error = "Please enter email and password.";

    } else {

        $stmt = $conn->prepare(
            "SELECT id, name, email, password 
             FROM admins 
             WHERE email = ?"
        );

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows === 1) {

            $admin = $result->fetch_assoc();

            /*
             * The current admin password in the database
             * is stored as plain text for this demo project.
             */
            if ($password === $admin["password"]) {

                $_SESSION["admin_id"] = $admin["id"];
                $_SESSION["admin_name"] = $admin["name"];
                $_SESSION["admin_email"] = $admin["email"];

                header("Location: dashboard.php");
                exit();

            } else {

                $error = "Invalid email or password.";
            }

        } else {

            $error = "Invalid email or password.";
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

    <title>Admin Login - Bus Reservation</title>

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
            background: #f4f7fb;
        }

        .login-container {
            width: 400px;
            background: white;
            padding: 35px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.12);
        }

        .login-container h1 {
            text-align: center;
            margin-bottom: 10px;
            color: #222;
        }

        .login-container p {
            text-align: center;
            color: #777;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            margin-bottom: 7px;
            font-weight: bold;
            color: #333;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 7px;
            font-size: 15px;
        }

        .form-group input:focus {
            outline: none;
            border-color: #007bff;
        }

        .login-btn {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 7px;
            background: #007bff;
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        .login-btn:hover {
            background: #0056b3;
        }

        .error {
            background: #ffe5e5;
            color: #d00000;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 18px;
            text-align: center;
        }

        .back-home {
            display: block;
            text-align: center;
            margin-top: 20px;
            text-decoration: none;
            color: #007bff;
        }

        .back-home:hover {
            text-decoration: underline;
        }

    </style>

</head>

<body>

    <div class="login-container">

        <h1>Admin Login</h1>

        <p>Bus Reservation System</p>

        <?php if (!empty($error)): ?>

            <div class="error">
                <?php echo htmlspecialchars($error); ?>
            </div>

        <?php endif; ?>

        <form method="POST" action="">

            <div class="form-group">

                <label for="email">Admin Email</label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Enter admin email"
                    required
                >

            </div>

            <div class="form-group">

                <label for="password">Password</label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter admin password"
                    required
                >

            </div>

            <button type="submit" class="login-btn">
                Login
            </button>

        </form>

        <a href="../index.php" class="back-home">
            ← Back to Home
        </a>

    </div>

</body>

</html>