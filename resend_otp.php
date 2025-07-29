<?php
session_start();
require_once 'src/config/connection.php';
require_once 'src/mailer/MailService.php';

if (!isset($_SESSION['pending_email'])) {
    header("Location: register.php");
    exit;
}

$email = $_SESSION['pending_email'];

// Fetch user details
$stmt = $pdo->prepare("SELECT name FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user) {
    $_SESSION['error'] = "User not found.";
    header("Location: verify_otp.php");
    exit;
}

// Generate new OTP
$new_otp = rand(100000, 999999);
$new_expiry = date("Y-m-d H:i:s", strtotime('+10 minutes'));

// Update in database
$stmt = $pdo->prepare("UPDATE users SET otp_code = ?, otp_expiry = ? WHERE email = ?");
$stmt->execute([$new_otp, $new_expiry, $email]);

// Send OTP
if (sendOTPEmail($email, $user['name'], $new_otp)) {
    $_SESSION['success'] = "A new OTP has been sent to your email.";
} else {
    $_SESSION['error'] = "Failed to send OTP. Please try again.";
}

header("Location: verify_otp.php");
exit;
