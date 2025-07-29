<?php
session_start();
require_once 'src/config/connection.php';
require_once 'src/mailer/MailService.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($name) || empty($email) || empty($password)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email address.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->rowCount() > 0) {
            $error = "Email already registered.";
        } else {
            $otp = rand(100000, 999999);
            $otp_expiry = date("Y-m-d H:i:s", strtotime('+10 minutes'));
            $hashed = password_hash($password, PASSWORD_BCRYPT);

            $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, otp_code, otp_expiry) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $email, $hashed, $otp, $otp_expiry]);

            if (sendOTPEmail($email, $name, $otp)) {
                $_SESSION['pending_email'] = $email;
                header("Location: verify_otp.php");
                exit;
            } else {
                $error = "Failed to send OTP email.";
            }
        }
    }
}
?>
<!--
<!DOCTYPE html>
<html>
<head>
    <title>Register </title>
</head>
<body>
    <h2>Register</h2>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="POST" action="">
        <label>Name</label><br>
        <input type="text" name="name" required><br><br>

        <label>Email</label><br>
        <input type="email" name="email" required><br><br>

        <label>Password</label><br>
        <input type="password" name="password" required><br><br>

        <button type="submit">Register</button>
    </form>
    <p>I already have an account? <a href="index.php">Login</a></p>

</body>
</html>
-->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - eBook Platform</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 40px;
            max-width: 400px;
            margin: auto;
            background-color: #f5f5f5;
        }
        h2 {
            color: #333;
        }
        label {
            font-weight: bold;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        #strengthMessage {
            font-size: 14px;
            margin-bottom: 20px;
            font-weight: bold;
        }
        button {
            padding: 10px 20px;
            background-color: #0077cc;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #005fa3;
        }
        .error {
            color: red;
            margin-bottom: 20px;
        }
        .login-link {
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <h2>Create Your Account</h2>

    <?php if (isset($error)) echo "<p class='error'>" . htmlspecialchars($error) . "</p>"; ?>

    <form method="POST" action="">
        <label for="name">Full Name</label>
        <input type="text" id="name" name="name" required placeholder="Your full name">

        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" required placeholder="you@example.com">

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required placeholder="Choose a secure password" onkeyup="checkPasswordStrength()">
        <div id="strengthMessage"></div>

        <button type="submit">Register</button>
    </form>

    <div class="login-link">
        <p>Already have an account? <a href="index.php">Login here</a></p>
    </div>

    <script>
        function checkPasswordStrength() {
            const password = document.getElementById('password').value;
            const strengthMessage = document.getElementById('strengthMessage');

            let strength = 0;
            if (password.length >= 8) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/\d/.test(password)) strength++;
            if (/[\W]/.test(password)) strength++;

            switch (strength) {
                case 0:
                case 1:
                    strengthMessage.textContent = "Very Weak 🔴";
                    strengthMessage.style.color = "red";
                    break;
                case 2:
                    strengthMessage.textContent = "Weak 🟠";
                    strengthMessage.style.color = "orange";
                    break;
                case 3:
                    strengthMessage.textContent = "Moderate 🟡";
                    strengthMessage.style.color = "#e6b800";
                    break;
                case 4:
                    strengthMessage.textContent = "Strong 🟢";
                    strengthMessage.style.color = "green";
                    break;
                case 5:
                    strengthMessage.textContent = "Very Strong ✅";
                    strengthMessage.style.color = "darkgreen";
                    break;
            }
        }
    </script>

</body>
</html>
