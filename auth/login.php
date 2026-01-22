
<?php 
    include "../config/auth/login.php";
    include "../components/auth/header.php"; 
?>

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
