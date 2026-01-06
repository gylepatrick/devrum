<?php
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
        $_SESSION["image"] = $user["image"];
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
              sendVerificationMail($user["email"], $code);
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
          }
        exit;
    } else {
        $_SESSION["toast"] = ["error", "Invalid login"];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login - DevRum</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../css/style.css">
</head>
<body class="bg-light d-flex justify-content-center align-items-center min-vh-100">

<div class="d-flex flex-column align-items-center justify-content-center w-100">

  <!-- Site Name -->
<h1 class="text-center fw-bold mb-4">
    <span class="text-primary">DEV</span>Rum
  </h1>

  <!-- Login Card -->
  <div class="card p-4 shadow-sm rounded-4" style="width: 360px; background-color: #fff;">
    <h3 class="mb-4 text-center fw-bold">Sign In</h3>

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

<!-- Toast Container -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
  <div id="toast" class="toast">
    <div class="toast-body"></div>
  </div>
</div>

<?php if (isset($_SESSION["toast"])): ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/toast.js"></script>
<script>
  showToast("<?= $_SESSION['toast'][1] ?>", "<?= $_SESSION['toast'][0] ?>");
</script>
<?php unset($_SESSION["toast"]); endif; ?>

</body>
</html>
