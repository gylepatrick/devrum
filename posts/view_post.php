<?php
session_start();
require "../db.php";

$postId = $_GET['post_id'] ?? null;

if (!$postId) {
  echo "No post found!";
  exit;
}

$stmt = $conn->prepare("
  SELECT posts.*, users.username
  FROM posts
  JOIN users ON users.id = posts.user_id
  WHERE posts.id = ?
");

$stmt->bind_param("i", $postId);
$stmt->execute();

$result = $stmt->get_result();
$post = $result->fetch_assoc();

if (!$post) {
  echo "Post not found!";
  exit;
}
?>


<!DOCTYPE html>
<html>
<head>
  <title>DevRum</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="container-fluid">
   <section class="ui-section">

  <nav class="bg-white p-3 rounded-4 m-3">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="../posts/index.php">Home</a></li>
      <li class="breadcrumb-item active"><?= $post['title'] ?></li>
    </ol>
  </nav>
</section>
  <div class="row">
    <!-- POSTS -->
    <div class="col-md-6 mx-auto">

        <div class="card mb-4">
          
          <a href="../auth/user_profile.php?user_id=<?= $post['user_id'] ?>" 
   class="text-decoration-none fw-bold text-dark fs-5 p-2">
  <?= htmlspecialchars($post['username']) ?>
          <?php if ($post["image"]): ?>
            <img src="<?= $post['image'] ?>" class="card-img-top">
          <?php endif; ?>

          <div class="card-body">
            <h5 class="fw-semibold"><?= htmlspecialchars($post["title"]) ?></h5>
            <p class="text-muted"><?= nl2br(htmlspecialchars($post["content"])) ?></p>

            <!-- TAGS -->
            <div class="mb-2">
              <?php
              $tg = $conn->query("
                SELECT t.name FROM tags t
                JOIN post_tags pt ON pt.tag_id = t.id
                WHERE pt.post_id = {$post['id']}
              ");
              while ($tagRow = $tg->fetch_assoc()):
              ?>
                <span class="badge-tag">#<?= $tagRow["name"] ?></span>
              <?php endwhile; ?>
            </div>

            <!-- POST META & ACTIONS -->
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="post-meta">by <?= $post["username"] ?></span>

              <?php if ($_SESSION["user_id"] == $post["user_id"]): ?>
                <div class="d-flex gap-1">
                  <a href="edit.php?id=<?= $post['id'] ?>" class="btn btn-soft btn-sm">Edit</a>
                  <a href="delete.php?id=<?= $post['id'] ?>"
                     onclick="return confirm('Delete this post?')"
                     class="btn btn-soft btn-sm text-danger">Delete</a>
                </div>
              <?php endif; ?>
            </div>

            <!-- LIKES -->
            <?php
            $likeCount = $conn->query(
              "SELECT COUNT(*) AS total FROM likes WHERE post_id={$post['id']}"
            )->fetch_assoc()["total"];

            $userLiked = $conn->query(
              "SELECT id FROM likes WHERE post_id={$post['id']} AND user_id={$_SESSION['user_id']}"
            )->num_rows > 0;
            ?>
            <div class="d-flex align-items-center gap-3 mb-2">
              <a href="like.php?post_id=<?= $post['id'] ?>"
   class="btn btn-sm <?= $userLiked ? 'btn-primary' : 'btn-outline-primary' ?>"
   onclick="return false;"
   data-post-id="<?= $post['id'] ?>">
  ❤️ <span class="like-count"><?= $likeCount ?></span>
</a>

            </div>

            <!-- COMMENTS -->
<div class="mt-3">

  

  <?php
$comments = $conn->query("
  SELECT comments.*, users.username
  FROM comments
  JOIN users ON users.id = comments.user_id
  WHERE comments.post_id = {$post['id']}
  ORDER BY comments.created_at ASC
  LIMIT 3
");
$commentCount = $conn->query("
  SELECT COUNT(*) AS total FROM comments WHERE post_id={$post['id']}
")->fetch_assoc()["total"];
?>

<div class="mt-3 comment-section" id="comment-section-<?= $post['id'] ?>">

  <form action="comment_single.php" method="POST" class="mb-2 d-flex gap-2">
    <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
    <input name="comment" class="form-control form-control-sm" placeholder="Add a comment..." required>
    <button class="btn btn-primary btn-sm" type="submit">Post</button>
  </form>

  <div class="comments-list">
    <?php while ($c = $comments->fetch_assoc()): ?>
      <div class="border-start ps-2 mb-2 comment-box">
        <strong><?= htmlspecialchars($c['username']) ?></strong>
        <p class="mb-0"><?= htmlspecialchars($c['comment']) ?></p>
        <small class="text-muted"><?= $c['created_at'] ?></small>
      </div>
    <?php endwhile; ?>
  </div>

  <?php if ($commentCount > 3): ?>
    <button class="btn btn-link btn-sm text-primary show-more-comments"
            data-post-id="<?= $post['id'] ?>"
            data-offset="3">
      Show more comments (<?= $commentCount - 3 ?>)
    </button>
  <?php endif; ?>

</div>


</div>


          </div>
        </div>

    </div>

  </div>
</div>
<script>
document.querySelectorAll('.show-more-comments').forEach(btn => {
  btn.addEventListener('click', function() {
    const postId = this.dataset.postId;
    let offset = parseInt(this.dataset.offset);
    const total = parseInt(this.dataset.total);

    fetch(`load_comments.php?post_id=${postId}&offset=${offset}`)
      .then(res => res.text())
      .then(html => {
        const container = document.querySelector(`#comment-section-${postId} .comments-list`);
        container.insertAdjacentHTML('beforeend', html);

        offset += 3;
        this.dataset.offset = offset;

        // hide button if all comments loaded
        if (offset >= total) {
          this.style.display = 'none';
        }
      });
  });
});
</script>

<script>
document.querySelectorAll('[data-post-id]').forEach(btn => {
  btn.addEventListener('click', function(e) {
    e.preventDefault(); // prevent default navigation

    const postId = this.dataset.postId;

    // send like/unlike request
    fetch(`like.php?post_id=${postId}`)
      .then(res => res.text())
      .then(() => {
        // toggle button style
        if (this.classList.contains('btn-primary')) {
          this.classList.remove('btn-primary');
          this.classList.add('btn-outline-primary');
        } else {
          this.classList.remove('btn-outline-primary');
          this.classList.add('btn-primary');
        }

        // update like count dynamically
        fetch(`like_count.php?post_id=${postId}`)
          .then(res => res.text())
          .then(count => {
            this.querySelector('.like-count').innerText = count;
          });
      });
  });
});
</script>

</body>
</html>



