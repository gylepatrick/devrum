<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require "../vendor/autoload.php";

function sendVerificationMail($email, $code) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = "sandbox.smtp.mailtrap.io";
        $mail->SMTPAuth = true;
        $mail->Username = "f0d4b95cce15b5";
        $mail->Password = "eb32d0b84c1c4f"; // Gmail App Password
        // $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 2525;

        $mail->setFrom("lukinhaygylepatrick@gmail.com", "DevRum");
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = "Verify your DevRum account";
        $mail->Body = "
            <h2>DevRum Email Verification</h2>
            <p>Your verification code is:</p>
            <h1 style='letter-spacing:3px;'>$code</h1>
            <p>This code expires in 10 minutes.</p>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
