<?php
session_start();
require_once 'src/config/connection.php';
require_once 'src/mailer/MailService.php'; // If not already included

if (!isset($_SESSION['pending_email'])) {
    header('Location: register.php');
    exit;
}

$email = $_SESSION['pending_email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entered_otp = trim($_POST['otp']);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        $error = "User not found.";
    } elseif ($user['is_verified']) {
        $error = "Account already verified. You can log in.";
    } elseif ($user['otp_code'] !== $entered_otp) {
        $error = "Invalid OTP. Please try again.";
    } elseif (strtotime($user['otp_expiry']) < time()) {
        $error = "OTP expired. Please re-register or contact support.";
    } else {
    // OTP is valid
    $stmt = $pdo->prepare("UPDATE users SET is_verified = 1, otp_code = NULL, otp_expiry = NULL WHERE email = ?");
    $stmt->execute([$email]);

    // Auto-login after verification
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['role'] = $user['role'];

    // ✅ Send welcome email
    sendWelcomeEmail($user['email'], $user['name']);

    unset($_SESSION['pending_email']);

    header("Location: index.php");
    exit;
    }
}
?>
<?php
if (isset($_SESSION['success'])) {
    echo "<p style='color:green;'>" . $_SESSION['success'] . "</p>";
    unset($_SESSION['success']);
}

if (isset($_SESSION['error'])) {
    echo "<p style='color:red;'>" . $_SESSION['error'] . "</p>";
    unset($_SESSION['error']);
}
?>
<!--
<!DOCTYPE html>
<html>
<head>
    <title>Verify OTP - eBook Platform</title>
</head>
<body>
    <h2>Verify OTP</h2>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="POST" action="">
        <label>Enter OTP sent to <strong><?php echo htmlspecialchars($email); ?></strong></label><br><br>
        <input type="text" name="otp" required><br><br>
        <button type="submit">Verify</button>
    </form>
    <p>Didn't receive the OTP? <a href="resend_otp.php">Resend OTP</a></p>

</body>
</html>-->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify OTP - eBook Platform</title>
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
        input[type="text"] {
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
        .resend {
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <h2>OTP Verification</h2>

    <?php if (isset($error)) echo "<p class='error'>" . htmlspecialchars($error) . "</p>"; ?>

    <form method="POST" action="">
        <label for="otp">Enter the OTP sent to <strong><?php echo htmlspecialchars($email); ?></strong></label>
        <input type="text" id="otp" name="otp" maxlength="6" required placeholder="Enter 6-digit OTP">
        <button type="submit">Verify</button>
    </form>

    <div class="resend">
        <p>Didn't receive the OTP? <a href="resend_otp.php">Resend OTP</a></p>
    </div>

</body>
</html>
