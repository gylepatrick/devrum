<?php
session_start();
if (!isset($_SESSION["user"])) {
  header("Location: auth/login.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - DevRum</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- NAVBAR -->
<?php include 'components/navbar.php'; ?>
<div style="height: 80px;"></div>

<!-- MAIN CONTENT -->
<main class="container py-5">
  <div class="row">
    <!-- SIDEBAR -->
    <div class="col-lg-3">
      <div class="card">
        <div class="card-body text-center">
          <img 
            src="<?= $_SESSION['image'] ?? 'uploads/avatar/defult_profile.jpeg' ?>" 
            alt="Profile" 
            class="rounded-circle mb-3"
            style="width: 100px; height: 100px; object-fit: cover; border: 3px solid #e5e7eb;"
          >
          <h4 class="fw-bold"><?= htmlspecialchars($_SESSION["user"]) ?></h4>
          <p class="text-muted small mb-3"><?= htmlspecialchars($_SESSION['fullname'] ?? 'Developer') ?></p>
          
          <div class="d-grid gap-2">
            <a href="auth/profile.php" class="btn btn-primary">View Profile</a>
            <a href="posts/create.php" class="btn btn-outline-primary">Create Post</a>
          </div>
        </div>
      </div>

      <!-- STATS CARD -->
      <div class="card mt-3">
        <div class="card-body">
          <h6 class="fw-bold mb-3">Stats</h6>
          <div class="row g-2">
            <div class="col-6">
              <div class="text-center p-2 bg-light rounded">
                <div class="fw-bold text-primary">0</div>
                <small class="text-muted">Posts</small>
              </div>
            </div>
            <div class="col-6">
              <div class="text-center p-2 bg-light rounded">
                <div class="fw-bold text-primary">0</div>
                <small class="text-muted">Likes</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- MAIN PANEL -->
    <div class="col-lg-9">
      <!-- WELCOME BANNER -->
      <div class="card bg-gradient mb-4" style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: white; border: none;">
        <div class="card-body">
          <h2 class="fw-bold mb-2">Welcome Back, <?= htmlspecialchars($_SESSION["user"]) ?> 👋</h2>
          <p class="mb-0 opacity-90">Here's what's happening in the community today</p>
        </div>
      </div>

      <!-- QUICK ACTIONS -->
      <div class="row g-3 mb-4">
        <div class="col-md-6">
          <a href="posts/index.php" class="btn btn-outline-primary w-100 py-3 text-start">
            <div class="fw-bold">Browse Posts</div>
            <small class="text-muted">See latest discussions</small>
          </a>
        </div>
        <div class="col-md-6">
          <a href="posts/create.php" class="btn btn-primary w-100 py-3 text-start">
            <div class="fw-bold">Ask Question</div>
            <small class="opacity-75">Share your problem</small>
          </a>
        </div>
      </div>

      <!-- RECENT ACTIVITY -->
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">Recent Activity</h5>
        </div>
        <div class="card-body text-center py-5">
          <p class="text-muted">No activity yet. Start by asking a question or browsing posts!</p>
        </div>
      </div>
    </div>
  </div>
</main>

<!-- FOOTER -->
<?php include 'components/footer.php'; ?>

<!-- TOAST UI -->
<?php include 'components/toast_ui.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/toast.js"></script>
<?php 
  if(isset($_SESSION["toast"])): 
?>
<script>
  showToast("<?= $_SESSION['toast'][1] ?>", "<?= $_SESSION['toast'][0] ?>");
</script>
<?php unset($_SESSION["toast"]); endif; ?>

</body>
</html>
