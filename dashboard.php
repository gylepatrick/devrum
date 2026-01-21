<?php
session_start();
if (!isset($_SESSION["user"])) {
  header("Location: login.php");
  exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex justify-content-center align-items-center min-vh-100">
  <h2>Welcome <?= $_SESSION["user"] ?> 👋</h2>
  <a href="logout.php">Logout</a>
  
  
  <?php 
    include "components/toast_ui.php";
    if(isset($_SESSION["toast"])):
  ?>
  
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="js/toast.js"></script>
  <script>
    showToast("<?= $_SESSION['toast'][1] ?>", "<?= $_SESSION['toast'][0] ?>");
  </script>
  
  <?php unset($_SESSION["toast"]); endif; ?>
</body>
</html>
