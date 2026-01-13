<?php include 'auth_config/register_config.php' ?>
<?php include '../components/auth/header.php' ?>

<div class="auth-wrapper">
  <div class="auth-card">
    <!-- Brand -->
    <div class="text-center mb-5">
      <h1 class="fw-bold mb-2">
        <span class="text-gradient">DEV</span>Rum
      </h1>
      <p class="text-muted small">
        Join a growing developer community
      </p>
    </div>

    <!-- Register Card -->
    <div class="card border-0 shadow-lg rounded-4 p-4 p-md-5">
      <h4 class="fw-bold text-center mb-4">Create your account</h4>

      <form method="POST" class="d-grid gap-3">
        <!-- Username Field -->
        <div class="form-floating">
          <input 
            type="text"
            name="username" 
            class="form-control form-control-lg rounded-3" 
            placeholder="Username" 
            required
            autofocus
          >
          <label>Username</label>
        </div>

        <!-- Full Name Field -->
        <div class="form-floating">
          <input 
            type="text"
            name="fullname" 
            class="form-control form-control-lg rounded-3" 
            placeholder="Full name" 
            required
          >
          <label>Full name</label>
        </div>

        <!-- Email Field -->
        <div class="form-floating">
          <input 
            type="email" 
            name="email" 
            class="form-control form-control-lg rounded-3" 
            placeholder="Email address" 
            required
          >
          <label>Email address</label>
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

        <!-- Confirm Password Field -->
        <div class="form-floating">
          <input 
            type="password" 
            name="confirm_password" 
            class="form-control form-control-lg rounded-3" 
            placeholder="Confirm password" 
            required
          >
          <label>Confirm password</label>
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

        <!-- Terms Checkbox -->
        <div class="form-check small mt-2">
          <input 
            class="form-check-input" 
            type="checkbox" 
            name="accept_terms" 
            id="terms" 
            required
          >
          <label class="form-check-label" for="terms">
            I agree to the
            <a href="#" class="text-primary text-decoration-none fw-500"
               data-bs-toggle="modal" data-bs-target="#termsModal">
              Terms & Conditions
            </a>
          </label>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary btn-lg w-100 mt-3 fw-bold rounded-3">
          Create Account
        </button>
      </form>

      <!-- Login Link -->
      <div class="text-center mt-4 small">
        <p class="text-muted mb-0">
          Already have an account? 
          <a href="login.php" class="fw-bold text-primary text-decoration-none">
            Login
          </a>
        </p>
      </div>
    </div>
  </div>
</div>

<!-- TERMS MODAL -->
<div class="modal fade" id="termsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
    <div class="modal-content rounded-4 border-0 shadow-lg">
      <div class="modal-header border-0 pb-2">
        <h5 class="modal-title fw-bold">DevRum Terms & Conditions</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body small text-muted lh-lg">
        <p><strong class="text-dark">1. Purpose</strong><br>
          DevRum is a developer-focused platform for sharing knowledge and collaborating.
        </p>

        <p><strong class="text-dark">2. User Content</strong><br>
          Users are responsible for the content they post. No harmful or illegal content is allowed.
        </p>

        <p><strong class="text-dark">3. Community Conduct</strong><br>
          Respect others. Harassment, spam, or abusive behavior leads to account suspension.
        </p>

        <p><strong class="text-dark">4. Data & Privacy</strong><br>
          Only essential data is stored. All passwords are securely hashed and encrypted.
        </p>

        <p><strong class="text-dark">5. Account Termination</strong><br>
          Accounts violating these rules may be suspended or permanently removed.
        </p>

        <p><strong class="text-dark">6. Changes to Terms</strong><br>
          Terms may be updated at any time. Continued use implies acceptance.
        </p>

        <p class="mt-4 fw-semibold text-primary">
          Build cool things. Help other devs grow 🚀
        </p>
      </div>

      <div class="modal-footer border-0 pt-2">
        <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-dismiss="modal">
          I Understand
        </button>
      </div>
    </div>
  </div>
</div>

<script>
function togglePassword() {
  const passwordInput = document.getElementById('passwordInput');
  const confirmInput = document.querySelector('input[name="confirm_password"]');
  
  if (passwordInput.type === "password") {
    passwordInput.type = "text";
    confirmInput.type = "text";
  } else {
    passwordInput.type = "password";
    confirmInput.type = "password";
  }
}
</script>

<?php include '../components/auth/footer.php' ?>

