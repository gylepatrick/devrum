<?php
session_start();
require "../db.php";

$viewUserId = $_GET['user_id'] ?? null;
$currentUserId = $_SESSION['user_id'] ?? null;

if (!$viewUserId) {
    echo "User not found.";
    exit;
}

// Redirect to own profile if viewing yourself
if ($viewUserId == $currentUserId) {
    header("Location: profile.php");
    exit;
}

// Fetch user info
$user = $conn->query("SELECT * FROM users WHERE id=$viewUserId")->fetch_assoc();
if (!$user) {
    echo "User not found.";
    exit;
}

// Default values
$avatar = $user['avatar'] ?: '../uploads/avatar/defult_profile.jpeg';
$bio = $user['bio'] ?: 'This user hasn’t written a bio yet.';
$username = htmlspecialchars($user['username']);
$name = htmlspecialchars($user['fullname'] ?? 'Unknown');

// Fetch user posts
$posts = $conn->query("SELECT * FROM posts WHERE user_id=$viewUserId ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?= $username ?> - DevRum</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../css/style.css">
<style>
/* Instagram-like Profile Read-Only */
.profile-header { text-align: center; margin-bottom: 2rem; }
.profile-avatar { width: 120px; height: 120px; object-fit: cover; border-radius: 50%; border: 3px solid #ddd; }
.profile-username { font-size: 1.5rem; font-weight: 600; margin-top: 0.5rem; }
.profile-bio { font-size: 0.95rem; color: #555; margin-bottom: 1rem; }
.posts-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 8px; }
.posts-grid img { width: 100%; height: 120px; object-fit: cover; border-radius: 8px; }
</style>
</head>
<body class="bg-light">
  
<section class="ui-section">

  <nav class="bg-white p-3 rounded-4 m-3">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="../posts/index.php">Home</a></li>
      <li class="breadcrumb-item active">Profile</li>
    </ol>
  </nav>
</section>


<div class="container py-5">

  <div class="card shadow-sm p-4 rounded-4 mx-auto" style="max-width:600px;">
    
    <!-- Profile Header -->
    <div class="profile-header">
      <img src="<?= $avatar ?>" alt="Avatar" class="profile-avatar mb-2">
      <div class="profile-username"><?= $name ?></div>
      <div class=""><small><i><?= $username ?></i></small></div>
      <div class="profile-bio"><?= htmlspecialchars($bio) ?></div>
    </div>

    <!-- User's Posts Grid -->
    <h5 class="mt-4 mb-2"><?= $username ?>'s Posts</h5>
    <div class="posts-grid">
      <?php if ($posts->num_rows > 0): ?>
        <?php while($post = $posts->fetch_assoc()): ?>
          <?php if($post['image']): ?>
            <img src="<?= $post['image'] ?>" alt="Post">
          <?php endif; ?>
        <?php endwhile; ?>
      <?php else: ?>
        <p class="text-muted text-center">No posts yet.</p>
      <?php endif; ?>
    </div>

  </div>

</div>

</body>
</html>
