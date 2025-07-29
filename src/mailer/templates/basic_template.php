<?php
function renderEmailTemplate($title, $body) {
    return "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; }
            .container {
                max-width: 600px;
                margin: auto;
                background: white;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 0 10px rgba(0,0,0,0.05);
            }
            .footer {
                margin-top: 30px;
                font-size: 12px;
                color: #777;
                text-align: center;
            }
        </style>
    </head>
    <body>
        <div class='container'>
            <h2>$title</h2>
            <p>$body</p>
            <div class='footer'>
                This email was sent from the eBook Platform Admin Panel.
            </div>
        </div>
    </body>
    </html>
    ";
}
