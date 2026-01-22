<?php include "../config/auth/register.php"; ?>
<?php include "../components/auth/header.php"; ?>

<div class="w-100" style="max-width: 680px;">
  <!-- Card -->
  <div class="card shadow-sm rounded-4 bg-white border-0 p-4">

    <?php include "../components/brand.php"; ?>

    <h4 class="fw-bold text-center mb-3">Create your account</h4>

    <form method="POST" class="d-flex flex-column gap-3">

      <div class="row">
        <div class="col-6">
           <input name="username" class="form-control form-control-lg" placeholder="Username" required>
        </div>
        <div class="col-6">
            <input name="fullname" class="form-control form-control-lg" placeholder="Full name" required>
        </div>
      </div>

      <input type="email" name="email" class="form-control form-control-lg" placeholder="Email address" required>

      <div class="row">
        <div class="col-6">
          <input type="password" name="password" class="form-control form-control-lg" placeholder="Password" required>
        </div>
        <div class="col-6">
          <input type="password" name="confirm_password" class="form-control form-control-lg" placeholder="Confirm password" required>
        </div>
      </div>


      <!-- Terms -->
      <div class="form-check small">
        <input class="form-check-input" type="checkbox" name="accept_terms" id="terms">
        <label class="form-check-label" for="terms">
          I agree to the
          <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Terms & Conditions</a>
        </label>
      </div>

      <button 
        class="btn btn-primary btn-lg rounded-pill mt-2"
        id="registerBtn"
        disabled>
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

<script>
  let terms = document.getElementById('terms');
  let registerBtn = document.getElementById('registerBtn');

  terms.addEventListener('change', function() {
    registerBtn.disabled = !this.checked;
  });
</script>

<?php include "../components/auth/footer.php"; ?>