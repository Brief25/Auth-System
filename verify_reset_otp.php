<?php
session_start();
require_once 'src/config/connection.php';

if (!isset($_SESSION['reset_email'])) {
    header("Location: forgot_password.php");
    exit;
}

$email = $_SESSION['reset_email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entered_otp = trim($_POST['otp']);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        $error = "User not found.";
    } elseif ($user['otp_code'] !== $entered_otp) {
        $error = "Invalid OTP.";
    } elseif (strtotime($user['otp_expiry']) < time()) {
        $error = "OTP expired.";
    } else {
        $_SESSION['otp_verified'] = true;
        header("Location: reset_password.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>Verify OTP</title></head>
<body>
    <h2>Enter the OTP sent to your email</h2>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="POST">
        <input type="text" name="otp" required placeholder="Enter OTP"><br><br>
        <button type="submit">Verify OTP</button>
    </form>
</body>
</html>
