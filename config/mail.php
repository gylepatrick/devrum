<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require "../vendor/autoload.php";

function sendVerificationMail($email, $code) {
    $mail = new PHPMailer(true);

    try {
       // Looking to send emails in production? Check out our Email API/SMTP product!
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = 'a3e0878c1a711b';
        $mail->Password = '3bb6b4a9d0189f';

        $mail->setFrom("lukinhaygylepatrick@gmail.com", "DevRum");
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = "Verify your DevRum account";
      $mail->Body = "
            <div style='
                max-width: 480px;
                margin: auto;
                padding: 30px;
                background: #0f2027;
                background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
                border-radius: 12px;
                font-family: Arial, sans-serif;
                color: #ffffff;
                text-align: center;
            '>
                <h2 style='margin-bottom: 10px; color: #00e5ff;'>
                    DevRum Email Verification
                </h2>

                <p style='font-size: 15px; opacity: 0.9;'>
                    Use the verification code below to confirm your email address.
                </p>

                <div style='
                    margin: 25px 0;
                    padding: 15px;
                    background: rgba(255,255,255,0.1);
                    border-radius: 8px;
                '>
                    <h1 style='
                        margin: 0;
                        letter-spacing: 6px;
                        font-size: 36px;
                        color: #00e5ff;
                    '>
                        $code
                    </h1>
                </div>

                <p style='font-size: 14px; opacity: 0.85;'>
                    This code expires in <strong>10 minutes</strong>.
                </p>

                <hr style='border: none; border-top: 1px solid rgba(255,255,255,0.2); margin: 25px 0;'>

                <p style='font-size: 12px; opacity: 0.7;'>
                    If you didn’t request this, you can safely ignore this email.
                </p>
            </div>
            ";


        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
