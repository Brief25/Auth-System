<?php
session_start();
require_once 'src/config/connection.php';
require_once 'src/mailer/MailService.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        $error = "Email not found.";
    } else {
        $otp = rand(100000, 999999);
        $expiry = date("Y-m-d H:i:s", strtotime('+10 minutes'));

        $stmt = $pdo->prepare("UPDATE users SET otp_code = ?, otp_expiry = ? WHERE email = ?");
        $stmt->execute([$otp, $expiry, $email]);

        if (sendOTPEmail($email, $user['name'], $otp)) {
            $_SESSION['reset_email'] = $email;
            header("Location: verify_reset_otp.php");
            exit;
        } else {
            $error = "Failed to send OTP email.";
        }
    }
}
?>
<!--
<!DOCTYPE html>
<html>
<head><title>Forgot Password</title></head>
<body>
    <h2>Forgot Password</h2>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="POST">
        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>
        <button type="submit">Send OTP</button>
    </form>
</body>
</html>
-->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password - eBook Platform</title>
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
        input[type="email"] {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
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
    </style>
</head>
<body>

    <h2>Forgot Your Password?</h2>
    <p>Enter your registered email address and we'll send you a one-time password (OTP) to reset it.</p>

    <?php if (isset($error)) echo "<p class='error'>" . htmlspecialchars($error) . "</p>"; ?>

    <form method="POST" action="">
        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" required placeholder="you@example.com">
        <button type="submit">Send OTP</button>
    </form>

</body>
</html>
