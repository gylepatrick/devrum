<?php include '../components/auth/header.php' ?>
<?php include 'auth_config/login_config.php' ?>

<div class="auth-wrapper">
  <div class="auth-card">
    <!-- Brand -->
    <div class="text-center mb-5">
      <h1 class="fw-bold mb-2">
        <span class="text-gradient">DEV</span>Rum
      </h1>
      <p class="text-muted small">
        A modern space where developers ask, learn, and grow together
      </p>
    </div>

    <!-- Login Card -->
    <div class="card border-0 shadow-lg rounded-4 p-4 p-md-5">
      <h4 class="fw-bold text-center mb-4">Welcome Back</h4>

      <form method="POST" class="d-grid gap-3">
        <!-- Username Field -->
        <div class="form-floating">
          <input 
            type="text"
            name="username" 
            class="form-control form-control-lg rounded-3" 
            placeholder="Username or Email" 
            required
            autofocus
          >
          <label>Username or Email</label>
        </div>

        <!-- Password Field -->
        <div class="form-floating">
          <input 
            type="password"
            name="password" 
            class="form-control form-control-lg rounded-3" 
            placeholder="Password" 
            required
            id="passwordInput"
          >
          <label>Password</label>
        </div>

        <!-- Show Password Checkbox -->
        <div class="form-check">
          <input 
            type="checkbox" 
            class="form-check-input" 
            name="show_pass" 
            id="showPassword"
            onchange="togglePassword()"
          >
          <label class="form-check-label" for="showPassword">
            Show Password
          </label>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary btn-lg w-100 mt-3 fw-bold rounded-3">
          Sign In
        </button>
      </form>

      <!-- Sign Up Link -->
      <div class="text-center mt-4 small">
        <p class="text-muted mb-0">
          Don't have an account? 
          <a href="register.php" class="fw-bold text-primary text-decoration-none">
            Sign Up
          </a>
        </p>
      </div>
    </div>
  </div>
</div>

<script>
function togglePassword() {
  const passwordField = document.getElementById('passwordInput');
  if (passwordField.type === "password") {
    passwordField.type = "text";
  } else {
    passwordField.type = "password";
  }
}
</script>

<?php include '../components/auth/footer.php' ?>
