<?php
session_start();
require_once 'src/config/connection.php';
require_once 'src/mailer/MailService.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        $error = "Email is not registered.";
    } elseif ($user['status'] === 'blocked') {
        $error = "Your account is blocked. Please contact support.";
    } elseif (!$user['is_verified']) {
        $error = "Please verify your email before logging in.";
    } else {
        $now = new DateTime();
        $lockedUntil = $user['locked_until'] ? new DateTime($user['locked_until']) : null;

        if ($lockedUntil && $now < $lockedUntil) {
            $remaining = $lockedUntil->diff($now)->format('%i minutes %s seconds');
            $error = "Account is locked. Try again in $remaining.";
        } elseif (!password_verify($password, $user['password_hash'])) {
            
            // Track failed login attempt
            $attempts = $user['failed_attempts'] + 1;
            $stmt = $pdo->prepare("UPDATE users SET failed_attempts = ?, last_failed_at = NOW() WHERE email = ?");
            $stmt->execute([$attempts, $email]);

            if ($attempts >= 5) {
                $lockUntil = $now->modify('+15 minutes')->format('Y-m-d H:i:s');
                $stmt = $pdo->prepare("UPDATE users SET locked_until = ? WHERE email = ?");
                $stmt->execute([$lockUntil, $email]);

                // Optional: Send email alert
                sendLoginAlertEmail($email, $user['name']);

                $error = "Too many failed attempts. Account locked for 15 minutes.";
            } else {
                $left = 5 - $attempts;
                $error = "Incorrect password. $left attempt(s) remaining.";
            }
        } else {
            // Login success
            $stmt = $pdo->prepare("UPDATE users SET failed_attempts = 0, locked_until = NULL WHERE email = ?");
            $stmt->execute([$email]);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on role
            if ($_SESSION['role'] === 'admin') {
                header("Location: admin.html");
            } else {
                header("Location: home.html");
            }
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | Auth System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 40px;
        }
        input, button {
            padding: 8px;
            margin-top: 8px;
            width: 100%;
        }
        .container {
            max-width: 400px;
            margin: auto;
        }
        .error {
            color: red;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Login</h2>
    <?php if (isset($error)): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    
    <form method="POST" action="">
        <label>Email</label><br>
        <input type="email" name="email" required><br><br>

        <label>Password</label><br>
        <input type="password" name="password" required><br><br>

        <button type="submit">Login</button>
    </form>

    <p><a href="forgot_password.php">Forgot Password?</a></p>
    <p>No account? <a href="register.php">Register</a></p>
</div>
</body>
</html>
