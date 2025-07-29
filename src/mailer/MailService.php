<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


// Corrected paths to PHPMailer files
require_once __DIR__ . '/../config/PHPMailer-master/src/PHPMailer.php';
require_once __DIR__ . '/../config/PHPMailer-master/src/SMTP.php';
require_once __DIR__ . '/../config/PHPMailer-master/src/Exception.php';

// Email credentials for PHPMailer
define('MAIL_FROM_EMAIL', '');
//define('MAIL_FROM_NAME', 'Auth-System');
define('MAIL_APP_PASSWORD', ''); // Use App Password, not your Gmail password

function sendOTPEmail($toEmail, $toName, $otp) {
    
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = MAIL_FROM_EMAIL;           // App Email
        $mail->Password   = MAIL_APP_PASSWORD;        // App password from Gmail (not your main password)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('your@gmail.com', 'eBook Platform');
        $mail->addAddress($toEmail, $toName);

        $mail->isHTML(true);
        $mail->Subject = 'Auth-System- Confirm Your Email (OTP Inside)';
        $mail->Body = "
            <h2>Welcome to eBook Platform, $toName!</h2>
            <p>Your registration is almost complete.</p>
            <p><strong>Here is your OTP:</strong> <span style='font-size: 18px;'>$otp</span></p>
            <p>This code is valid for <strong>10 minutes</strong>.</p>
            <p>If you didn't request this, you can safely ignore this email.</p>
            <br>
            <p>Regards,<br>Auth-SystemTeam</p>
        ";

        $mail->AltBody = "Your OTP is: $otp (valid for 10 minutes).";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mail error: " . $mail->ErrorInfo);
        return false;
    }
}

/*Sucessfull message mail sevice */
function sendWelcomeEmail($toEmail, $toName) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = MAIL_FROM_EMAIL;
        $mail->Password   = MAIL_APP_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('your@gmail.com', 'eBook Platform');
        $mail->addAddress($toEmail, $toName);

        $mail->isHTML(true);
        $mail->Subject = 'Welcome to Auth-System📚';
        $mail->Body    = "
            <p>Hi <strong>$toName</strong>,</p>
            <p>🎉 Your registration was successful! You can now log in and explore eBooks.</p>
            <p>Happy reading!<br>– Auth-SystemTeam</p>
        ";

        $mail->send();
    } catch (Exception $e) {
        error_log("Welcome Email Error: " . $mail->ErrorInfo);
    }
}


/*Login Alert*/
function sendLoginAlertEmail($toEmail, $toName) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = MAIL_FROM_EMAIL;
        $mail->Password   = MAIL_APP_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('your@gmail.com', 'eBook Platform');
        $mail->addAddress($toEmail, $toName);

        $mail->isHTML(true);
        $mail->Subject = 'Security Alert: Multiple Failed Login Attempts';
        $mail->Body    = "
            <p>Hi <strong>$toName</strong>,</p>
            <p>We noticed multiple failed login attempts to your account.</p>
            <p>If this wasn't you, please reset your password immediately.</p>
        ";

        $mail->send();
    } catch (Exception $e) {
        error_log("Login alert failed: " . $mail->ErrorInfo);
    }
}

/* Mail Sending Service */
class MailService {
    private $mail;

    public function __construct() {
        $this->mail = new PHPMailer(true);
        $this->mail->isSMTP();
        $this->mail->Host       = 'smtp.gmail.com';
        $this->mail->SMTPAuth   = true;
        $this->mail->Username   = MAIL_FROM_EMAIL;
        $this->mail->Password   = MAIL_APP_PASSWORD;
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port       = 587;
        $this->mail->setFrom('your-email@gmail.com', 'eBook Platform');
        $this->mail->isHTML(true);
    }

    public function sendMail($to, $name, $subject, $body): bool {
        try {
            $this->mail->clearAllRecipients();
            $this->mail->addAddress($to, $name);
            $this->mail->Subject = $subject;
            $this->mail->Body    = $body;
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
