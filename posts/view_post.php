<?php
session_start();
require "../db.php";

$postId = $_GET['post_id'] ?? null;

if (!$postId) {
  die("No post found!");
}

/* FETCH POST */
$stmt = $conn->prepare("
  SELECT posts.*, users.username, users.avatar
  FROM posts
  JOIN users ON users.id = posts.user_id
  WHERE posts.id = ?
");
$stmt->bind_param("i", $postId);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();

if (!$post) {
  die("Post not found!");
}

/* FETCH COMMENTS (FIRST 3) */
$comments = $conn->query("
  SELECT comments.*, users.username
  FROM comments
  JOIN users ON users.id = comments.user_id
  WHERE comments.post_id = $postId
  ORDER BY comments.created_at desc
  LIMIT 3
");

/* COMMENT COUNT */
$commentCount = $conn->query("
  SELECT COUNT(*) AS total FROM comments WHERE post_id = $postId
")->fetch_assoc()['total'];

/* LIKES */
$likeCount = $conn->query("
  SELECT COUNT(*) AS total FROM likes WHERE post_id = $postId
")->fetch_assoc()['total'];

$userLiked = false;
if (isset($_SESSION['user_id'])) {
  $userLiked = $conn->query("
    SELECT id FROM likes 
    WHERE post_id = $postId AND user_id = {$_SESSION['user_id']}
  ")->num_rows > 0;
}
?>
<?php include "../components/posts_ui/header.php"; ?>
<!-- BREADCRUMB -->
  <nav class="p-3 rounded-4 bg-secondary opacity-20 shadow-m text-white m-3 shadow-sm">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a class="text-warning text-decoration-none" href="../posts/index.php">Home</a></li>
      <li class="breadcrumb-item text-white active"><?= htmlspecialchars($post['title']) ?></li>
    </ol>
  </nav>

  <div class="container-fluid">
  <div class="row px-4">

    <!-- LEFT: POST -->
    <div class="col-lg-8 col-md-7">
      <div class="card shadow-sm bg-secondary opacity-10 rounded-4 mb-4">

        <?php if ($post["image"]): ?>
          <img src="<?= $post["image"] ?>" class="card-img-top rounded-top-4">
        <?php endif; ?>

        <div class="card-body">

          <!-- USER -->
          <div class="d-flex align-items-center gap-2 mb-3">
            <img src="<?= $post["avatar"] ?: "../uploads/avatar/defult_profile.jpeg" ?>"
                 class="rounded-circle" width="40" height="40">
            <strong class="text-white"><?= htmlspecialchars($post["username"]) ?></strong>
          </div>

          <h4 class="fw-bold text-white medium"><?= htmlspecialchars($post["title"]) ?></h4>
          <p class="text-white"><?= nl2br(htmlspecialchars($post["content"])) ?></p>

          <!-- LIKE -->
          <button id="likeBtn"
                  data-post-id="<?= $postId ?>"
                  class="btn btn-sm <?= $userLiked ? 'btn-outline-danger' : 'btn-outline-danger' ?>">
            ❤️ <span id="likeCount"><?= $likeCount ?></span>
          </button>
        </div>
      </div>

      <!-- COMMENT FORM -->
      <?php if (isset($_SESSION["user_id"])): ?>
        <div class="card shadow-sm bg-secondary opacity-10 rounded-4 mb-4">
          <div class="card-body">
            <h6 class="fw-bold mb-3">Add a Comment</h6>
            <form action="comment_single.php" method="POST" class="d-flex gap-2">
              <input type="hidden" name="post_id" value="<?= $postId ?>">
              <input name="comment" class="form-control bg-secondary opacity-10 text-white placeholder-text-white"
                     placeholder="Write your comment here..." required>
              <button class="btn btn-primary">Post</button>
            </form>
          </div>
        </div>
      <?php endif; ?>
    </div>

    <!-- RIGHT: COMMENTS -->
    <div class="col-lg-4 col-md-5">
      <div class="card shadow-sm bg-secondary opacity-10 rounded-4 mb-4" >
        <div class="card-body">

          <h6 class="fw-bold mb-3">
            💬 Comments (<?= $commentCount ?>)
          </h6>

          <!-- COMMENTS LIST -->
          <div id="comment-section">
            <?php while ($c = $comments->fetch_assoc()): ?>
              <div class="mb-3 border-bottom pb-2">
                <strong class="small text-white"><?= htmlspecialchars($c["username"]) ?></strong>
                <p class="small text-white mb-1">
                  <?= htmlspecialchars($c["comment"]) ?>
                </p>
                <small class="text-white" data-time="<?= $c["created_at"] ?>"></small>
              </div>
            <?php endwhile; ?>
          </div>

          <?php if ($commentCount > 3): ?>
            <button id="loadMore"
                    class="btn btn-link btn-sm w-100"
                    data-offset="3"
                    data-total="<?= $commentCount ?>">
              Show more comments
            </button>
          <?php endif; ?>

        </div>
      </div>
    </div>

  </div>
</div>


<script>
// time ago function
function timeAgo(dateString) {
  const now = new Date();
  const past = new Date(dateString);
  const seconds = Math.floor((now - past) / 1000);
  let interval = Math.floor(seconds / 31536000);
  if (interval >= 1) return interval + " year" + (interval > 1 ? "s" : "") + " ago";
  interval = Math.floor(seconds / 2592000);
  if (interval >= 1) return interval + " month" + (interval > 1 ? "s" : "") + " ago";
  interval = Math.floor(seconds / 86400);
  if (interval >= 1) return interval + " day" + (interval > 1 ? "s" : "") + " ago";
  interval = Math.floor(seconds / 3600);
  if (interval >= 1) return interval + " hour" + (interval > 1 ? "s" : "") + " ago";
  interval = Math.floor(seconds / 60);
  if (interval >= 1) return interval + " minute" + (interval > 1 ? "s" : "") + " ago";
  return Math.floor(seconds) + " second" + (seconds > 1 ? "s" : "") + " ago";
}

// Update all time elements
document.querySelectorAll("[data-time]").forEach(el => {
  el.innerText = timeAgo(el.dataset.time);
});

/* LIKE */
document.getElementById("likeBtn")?.addEventListener("click", function () {
  const postId = this.dataset.postId;

  fetch(`like.php?post_id=${postId}`)
    .then(() => fetch(`like_count.php?post_id=${postId}`))
    .then(res => res.text())
    .then(count => {
      document.getElementById("likeCount").innerText = count;
      this.classList.toggle("btn-primary");
      this.classList.toggle("btn-outline-primary");
    });
});



/* LOAD MORE COMMENTS */
document.getElementById("loadMore")?.addEventListener("click", function () {
  let offset = parseInt(this.dataset.offset);
  const total = parseInt(this.dataset.total);

  fetch(`load_comments.php?post_id=<?= $postId ?>&offset=${offset}`)
    .then(res => res.text())
    .then(html => {
      document.getElementById("comment-section")
        .insertAdjacentHTML("beforeend", html);

      // Update time elements for newly loaded comments
      document.querySelectorAll("[data-time]").forEach(el => {
        el.innerText = timeAgo(el.dataset.time);
      });

      offset += 3;
      this.dataset.offset = offset;
      if (offset >= total) this.remove();
    });
});
</script>

</body>
</html>
