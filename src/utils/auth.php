<?php
//session_start();

if (!isset($_SESSION['user_id'])) {
    // Show countdown and redirect
    echo "
    <!DOCTYPE html>
    <html>
    <head>
        <title>Redirecting...</title>
        <style>
            body { font-family: Arial, sans-serif; text-align: center; margin-top: 100px; }
            .countdown { font-size: 24px; font-weight: bold; color: #d00; }
        </style>
        <script>
            let seconds = 5;
            function countdown() {
                const counter = document.getElementById('countdown');
                counter.innerText = seconds;
                if (seconds === 0) {
                    window.location.href = 'login.php';
                } else {
                    seconds--;
                    setTimeout(countdown, 1000);
                }
            }
            window.onload = countdown;
        </script>
    </head>
    <body>
        <h2>You are not logged in.</h2>
        <p>You will be redirected to the login page in <span class='countdown' id='countdown'>5</span> seconds...</p>
    </body>
    </html>
    ";
    exit;
}
