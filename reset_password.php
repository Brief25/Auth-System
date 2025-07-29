<?php
session_start();
require_once 'src/config/connection.php';

if (!isset($_SESSION['reset_email']) || !isset($_SESSION['otp_verified'])) {
    header("Location: forgot_password.php");
    exit;
}

$email = $_SESSION['reset_email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = $_POST['password'];
    $confirm     = $_POST['confirm'];

    if (strlen($newPassword) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif ($newPassword !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        $hashed = password_hash($newPassword, PASSWORD_BCRYPT);

        $stmt = $pdo->prepare("UPDATE users SET password_hash = ?, otp_code = NULL, otp_expiry = NULL WHERE email = ?");
        $stmt->execute([$hashed, $email]);

        // Clear session
        unset($_SESSION['reset_email']);
        unset($_SESSION['otp_verified']);

        $success = "Password changed successfully. <a href='index.php'>Login</a>";
    }
}
?>
<!--
<!DOCTYPE html>
<html>
<head><title>Reset Password</title></head>
<body>
    <h2>Reset Your Password</h2>
    <?php
   /*if (isset($error)) echo "<p style='color:red;'>$error</p>";
    if (isset($success)) echo "<p style='color:green;'>$success</p>";*/
    ?>
    <form method="POST">
        <input type="password" name="password" required placeholder="New Password"><br><br>
        <input type="password" name="confirm" required placeholder="Confirm Password"><br><br>
        <button type="submit">Change Password</button>
    </form>
</body>
</html>
-->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password - Secure Auth System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 40px;
            max-width: 400px;
            margin: auto;
            background-color: #f8f8f8;
        }
        h2 {
            color: #333;
            margin-bottom: 20px;
        }
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #0077cc;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
        }
        button:hover {
            background-color: #005fa3;
        }
        .error {
            color: red;
            margin-bottom: 15px;
        }
        .success {
            color: green;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

    <h2>Reset Your Password</h2>

    <?php
    if (isset($error)) {
        echo "<p class='error'>" . htmlspecialchars($error) . "</p>";
    }
    if (isset($success)) {
        echo "<p class='success'>" . htmlspecialchars($success) . "</p>";
        echo "<a href='index.php'>Login</a>";
    }
    ?>

    <form method="POST" action="">
        <input type="password" name="password" required placeholder="New Password" minlength="8">
        <input type="password" name="confirm" required placeholder="Confirm Password" minlength="8">
        <button type="submit">Change Password</button>
    </form>

</body>
</html>
