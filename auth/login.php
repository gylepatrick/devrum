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

<?php include "../components/auth/header.php"; ?>

<div class="d-flex flex-column align-items-center justify-content-center w-100">



  <!-- Login Card -->
  <div class="card p-4 shadow-sm rounded-4" style="width: 560px; background-color: #fff;">
    <!-- Site Name -->
    <h1 class="text-center fw-bold mb-4">
        <span class="text-primary">DEV</span>RUM
    </h1>
    <h3 class="mb-4 text-center fw-bold text-uppercase">Sign In</h3>

    <form method="POST" class="d-flex flex-column gap-3">
      <input name="username" class="form-control form-control-lg rounded-3" placeholder="Username" required>
      <input name="password" type="password" class="form-control form-control-lg rounded-3" placeholder="Password" required>
      <button class="btn btn-primary btn-lg w-100 mt-2 shadow-sm">Sign In</button>
    </form>

    <p class="mt-3 text-center text-muted small">
      Don't have an account? <a href="register.php" class="text-decoration-none">Sign Up</a>
    </p>
  </div>

</div>

<?php include "../components/auth/footer.php"; ?>
