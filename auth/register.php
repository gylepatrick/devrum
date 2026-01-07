<?php
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

                // 📩 SEND EMAIL
                try {
                    $mail = new PHPMailer(true);
                    $mail->isSMTP();
                    $mail->Host = "smtp.gmail.com";
                    $mail->SMTPAuth = true;
                    $mail->Username = "your_email@gmail.com"; // 🔴 CHANGE
                    $mail->Password = "your_app_password";    // 🔴 CHANGE
                    $mail->SMTPSecure = "tls";
                    $mail->Port = 587;

                    $mail->setFrom("your_email@gmail.com", "DevRum");
                    $mail->addAddress($email, $fullname);

                    $mail->isHTML(true);
                    $mail->Subject = "Verify your DevRum account";
                    $mail->Body = "
                        <h2>Welcome to DevRum 👋</h2>
                        <p>Your verification code is:</p>
                        <h1 style='letter-spacing:4px;'>$verificationCode</h1>
                        <p>This code expires in 15 minutes.</p>
                    ";

                    $mail->send();

                } catch (Exception $e) {
                    $_SESSION["toast"] = ["error", "Email could not be sent"];
                    exit;
                }

                // 🚀 Redirect to verification
                $_SESSION["pending_verification"] = $conn->insert_id;
                $_SESSION["toast"] = ["success", "Verification code sent to your email"];
                header("Location: verify_account.php");
                exit;

            } else {
                $_SESSION["toast"] = ["error", "Something went wrong"];
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register · DevRum</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center min-vh-100">

<div class="w-100" style="max-width: 380px;">

  <!-- Brand -->
  <h1 class="text-center fw-bold mb-4">
    <span class="text-primary">DEV</span>Rum
  </h1>

  <!-- Card -->
  <div class="card shadow-sm rounded-4 border-0 p-4">

    <h4 class="fw-bold text-center mb-3">Create your account</h4>

    <form method="POST" class="d-flex flex-column gap-3">

      <input name="username" class="form-control form-control-lg" placeholder="Username" required>
      <input name="fullname" class="form-control form-control-lg" placeholder="Full name" required>
      <input type="email" name="email" class="form-control form-control-lg" placeholder="Email address" required>
      <input type="password" name="password" class="form-control form-control-lg" placeholder="Password" required>
      <input type="password" name="confirm_password" class="form-control form-control-lg" placeholder="Confirm password" required>

      <!-- Terms -->
      <div class="form-check small">
        <input class="form-check-input" type="checkbox" name="accept_terms" id="terms">
        <label class="form-check-label" for="terms">
          I agree to the
          <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Terms & Conditions</a>
        </label>
      </div>

      <button class="btn btn-primary btn-lg rounded-pill mt-2">
        Create Account
      </button>
    </form>

    <p class="text-center small text-muted mt-3">
      Already have an account?
      <a href="login.php" class="text-decoration-none fw-semibold">Login</a>
    </p>
  </div>
</div>

<!-- TERMS MODAL -->
<div class="modal fade" id="termsModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
    <div class="modal-content rounded-4">
      <div class="modal-header">
        <h5 class="modal-title fw-bold">DevRum Terms & Conditions</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body small text-muted">

        <p><strong>1. Purpose</strong><br>
        DevRum is a developer-focused discussion platform for sharing code, asking questions, and collaborating respectfully.</p>

        <p><strong>2. User Content</strong><br>
        You are responsible for the content you post. Do not upload malicious code, copyrighted material without permission, or harmful content.</p>

        <p><strong>3. Community Conduct</strong><br>
        Be respectful. Harassment, hate speech, and spam will result in account suspension or removal.</p>

        <p><strong>4. Data & Privacy</strong><br>
        We store only essential information required to operate your account. Passwords are securely hashed.</p>

        <p><strong>5. Account Termination</strong><br>
        DevRum reserves the right to suspend or terminate accounts that violate these terms.</p>

        <p><strong>6. Changes</strong><br>
        Terms may be updated. Continued use means acceptance of the latest version.</p>

        <p class="mt-3">By using DevRum, you agree to build cool things and help other devs grow 🚀</p>

      </div>
      <div class="modal-footer">
        <button class="btn btn-primary rounded-pill px-4" data-bs-dismiss="modal">
          I Understand
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Toast -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
  <div id="toast" class="toast"><div class="toast-body"></div></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/toast.js"></script>

<?php if (isset($_SESSION["toast"])): ?>
<script>
  showToast("<?= $_SESSION['toast'][1] ?>", "<?= $_SESSION['toast'][0] ?>");
</script>
<?php unset($_SESSION["toast"]); endif; ?>

</body>
</html>
