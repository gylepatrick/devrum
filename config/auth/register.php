<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require "../db.php";
require "../vendor/autoload.php"; // PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    $fullname = trim($_POST["fullname"]);
    $email = trim($_POST["email"]);
    $accepted = isset($_POST["accept_terms"]);

    if (!$username || !$password || !$fullname || !$email) {
        $_SESSION["toast"] = ["error", "All fields are required"];

    } elseif (!$accepted) {
        $_SESSION["toast"] = ["error", "You must accept the Terms & Conditions"];

    } elseif ($password !== $confirm_password) {
        $_SESSION["toast"] = ["error", "Passwords do not match!"];

    } else {
        // Check duplicate
        $check = $conn->prepare("SELECT id FROM users WHERE username=? OR email=?");
        $check->bind_param("ss", $username, $email);
        $check->execute();

        if ($check->get_result()->num_rows > 0) {
            $_SESSION["toast"] = ["error", "Username or email already taken"];

        } else {

            // 🔐 Hash password
            $hash = password_hash($password, PASSWORD_DEFAULT);

            // 🔢 Generate verification code
            $verificationCode = strval(random_int(100000, 999999));
            $expiresAt = date("Y-m-d H:i:s", time() + 900); // 15 mins

            // Insert user
            $stmt = $conn->prepare("
                INSERT INTO users 
                (username, password, fullname, email, verification_code, code_expires, is_verified)
                VALUES (?, ?, ?, ?, ?, ?, 0)
            ");
            $stmt->bind_param(
                "ssssss",
                $username,
                $hash,
                $fullname,
                $email,
                $verificationCode,
                $expiresAt
            );

            if ($stmt->execute()) {

                require "../config/mail.php";
                if (!sendVerificationMail($email, $verificationCode)) {
                    $_SESSION["toast"] = ["error", "Email could not be sent"];
                    // Maybe redirect or something, but for now, continue
                } else {
                    // Redirect to verification
                    $_SESSION["pending_verification"] = $conn->insert_id;
                    $_SESSION["toast"] = ["success", "Verification code sent to your email"];
                    header("Location: verify_account.php");
                    exit;
                }

            } else {
                $_SESSION["toast"] = ["error", "Something went wrong"];
            }
        }
    }
}
?>