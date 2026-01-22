<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require "../db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
    $stmt->bind_param("s", $_POST["username"]);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($_POST["password"], $user["password"])) {
        $_SESSION["user"] = $user["username"];
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["image"] = $user["avatar"];
        
        $_SESSION["toast"] = ["success", "Login success!"];

        $is_verified = $user["is_verified"];

          if ($is_verified == 0) {

              // Generate code
              $code = rand(100000, 999999);
              $expires = date("Y-m-d H:i:s", strtotime("+10 minutes"));

              $update = $conn->prepare(
                  "UPDATE users SET verification_code=?, code_expires=? WHERE id=?"
              );
              $update->bind_param("ssi", $code, $expires, $user["id"]);
              $update->execute();

              require "../config/mail.php";
              if (!sendVerificationMail($user["email"], $code)) {
                  $_SESSION["toast"] = [
                      "error",
                      "Failed to send verification email. Please try again.",
                  ];
                  header("Location: login.php");
                  exit;
              }

              $_SESSION["pending_verification"] = $user["id"];
              $_SESSION["toast"] = ["warning", "Verify your email to continue"];

              header("Location: verify_account.php");
              exit;
          } else {
              // User is verified, redirect to dashboard
              header("Location: ../posts/index.php");
              exit;
          }
    } else {
        $_SESSION["toast"] = ["error", "Invalid login"];
    }
}
?>