<?php
session_start();
require "../db.php";

/**
 * If user is not pending verification, kick them out
 */
if (!isset($_SESSION["pending_verification"])) {
    header("Location: login.php");
    exit;
}

$userId = (int) $_SESSION["pending_verification"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $code = trim($_POST["code"]);

    if (!$code) {
        $_SESSION["toast"] = ["error", "Verification code is required"];
    } else {

        $stmt = $conn->prepare("
            SELECT verification_code, code_expires 
            FROM users 
            WHERE id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if (!$user) {
            $_SESSION["toast"] = ["error", "Invalid verification request"];

        // 🔥 FIX: cast + trim BOTH sides
        } elseif (trim((string)$user["verification_code"]) !== trim($code)) {
            $_SESSION["toast"] = ["error", "Invalid verification code"];

        } elseif (strtotime($user["code_expires"]) < time()) {
            $_SESSION["toast"] = ["error", "Verification code expired"];

        } else {
            // ✅ Mark account as verified
            $update = $conn->prepare("
                UPDATE users 
                SET is_verified = 1,
                    verification_code = NULL,
                    code_expires = NULL
                WHERE id = ?
            ");
            $update->bind_param("i", $userId);
            $update->execute();

            unset($_SESSION["pending_verification"]);

            $_SESSION["toast"] = ["success", "Email verified successfully 🎉"];
            header("Location: ../posts/index.php");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Verify Account · DevRum</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../css/style.css">
</head>

<body class="bg-light d-flex justify-content-center align-items-center min-vh-100">

<div class="card p-4 rounded-4 shadow-sm" style="width:360px">
  
  <h3 class="fw-bold text-center mb-2">
    <span class="text-primary">DEV</span>Rum
  </h3>

  <p class="text-muted text-center small mb-3">
    Enter the 6-digit code sent to your email
  </p>

  <!-- verification expiration timer countdown -->

  <div class="text-center small text-muted mb-3">
    The code expires in 10 minutes.

  </div>


  <form method="POST" class="d-flex flex-column gap-3">
    <input 
      name="code"
      class="form-control form-control-lg text-center rounded-3 letter-spacing-3"
      placeholder="******"
      maxlength="6"
      required
    >
    <button class="btn btn-primary btn-lg rounded-pill">
      Verify Account
    </button>
  </form>

  <p class="text-center small mt-3 text-muted">
    Didn’t receive a code?  
    <a href="resend_code.php" class="text-decoration-none">Resend</a>
  </p>

</div>

<!-- Toast -->
<?php include "../components/toast_ui.php"; ?>

<?php if (isset($_SESSION["toast"])): ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/toast.js"></script>
<script>
  showToast(
    "<?= $_SESSION['toast'][1] ?>",
    "<?= $_SESSION['toast'][0] ?>"
  );
</script>
<?php unset($_SESSION["toast"]); endif; ?>

</body>
</html>
