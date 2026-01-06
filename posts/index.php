<?php
session_start();
require "../db.php";

// Get tag filter if exists
$tag = $_GET["tag"] ?? null;

// Fetch posts
$query = "
SELECT posts.*, users.username, users.avatar
FROM posts
JOIN users ON users.id = posts.user_id
";

if ($tag) {
  $query .= "
  JOIN post_tags pt ON pt.post_id = posts.id
  JOIN tags t ON t.id = pt.tag_id
  WHERE t.name = '$tag'
  ";
}

$posts = $conn->query($query);

// Fetch all tags
$tags = $conn->query("SELECT * FROM tags");
?>
<!DOCTYPE html>
<html>
<head>
  <title>DevRum</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<!-- NAVBAR -->

<nav class="navbar navbar-expand-lg fixed-top bg-white border-bottom shadow-sm">
  <div class="container-fluid px-4">

    <!-- Brand -->
    <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="index.php">
      <span class="text-secondary">DEV<b class="text-primary">Rum</b></span>
    </a>
    <button 
  class="btn btn-primary btn-sm px-3 rounded-pill fw-semibold"
  data-bs-toggle="modal"
  data-bs-target="#askModal">
  + Ask
</button>

    <!-- Right actions -->
    <div class="ms-auto d-flex align-items-center gap-3">
      <!-- Profile -->
      <a href="../auth/profile.php"
         class="d-flex align-items-center gap-2 text-decoration-none text-dark">
        <img
          src="<?= $_SESSION['image'] ?? '../uploads/avatar/defult_profile.jpeg' ?>"
          class="rounded-circle"
          style="width:32px;height:32px;object-fit:cover;"
        >
        <span class="fw-semibold small"><?= $_SESSION['user'] ?></span>
      </a>
    
      <!-- Logout -->
      <a href="../auth/logout.php"
         class="btn btn-outline-dark btn-sm px-3 rounded-pill">
        Logout
      </a>

    </div>
  </div>
</nav>
<div style="height:80px"></div>
<div class="container-fluid">
  <div class="row">
    <!-- TAG SIDEBAR -->
    <div class="col-md-4 mb-3">
      <div class="card p-3 sidebar">
        <h6 class="fw-bold mb-3">Tags</h6>
        <div class="d-flex flex-wrap gap-2">
          
          <?php while ($t = $tags->fetch_assoc()): ?>
            <a href="?tag=<?= $t['name'] ?>" class="badge-tag">#<?= $t['name'] ?></a>
          <?php endwhile; ?>
        </div>
      </div>
    </div>
  
    <!-- POSTS -->
    <div class="col-md-8 mx-auto">
      <?php while ($post = $posts->fetch_assoc()): ?>
        <div class="card mb-4">
          <div class="d-flex align-items-center gap-2 mb-2">

  <!-- Avatar -->
  <a href="../auth/user_profile.php?user_id=<?= $post['user_id'] ?>">
    <img 
      src="<?= $post['avatar'] ? htmlspecialchars($post['avatar']) : '../uploads/avatar/defult_profile.jpeg' ?>"
      alt="avatar"
      class="rounded-circle border-lg m-3"
      style="width:40px; height:40px; object-fit:cover;"
    >
  </a>

  <!-- Username + Read more -->
  <div>
    <a 
      href="../auth/user_profile.php?user_id=<?= $post['user_id'] ?>"
      class="text-decoration-none fw-bold text-dark p-2"
      style="font-size: 15px;"
    >
      <?= htmlspecialchars($post['username']) ?>
    </a>

    <div>
      <a 
        href="view_post.php?post_id=<?= $post['id'] ?>"
        class="text-primary small text-decoration-none"
      >
        Read more →
      </a>
    </div>
  </div>

</div>
  


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

            </div>
      </div>

      <?php endwhile; ?>
      
    </div>

  </div>
</div>


<!--modal-->
<div class="modal fade" id="askModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content rounded-4 border-0 shadow">

      <!-- Modal Header -->
      <div class="modal-header border-0">
        <h5 class="modal-title fw-bold">Ask the DevRum Community</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal Body -->
      <form action="store.php" method="POST" enctype="multipart/form-data">
        <div class="modal-body">

          <div class="mb-3">
            <label class="form-label fw-semibold">Title</label>
            <input name="title" class="form-control form-control-lg" placeholder="What's your question?" required>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Details</label>
            <textarea 
              name="content"
              class="form-control"
              rows="4"
              placeholder="Explain your problem, share code, errors, etc..."
              required></textarea>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Tags</label>
            <input 
              name="tags"
              class="form-control"
              placeholder="php, laravel, javascript">
            <small class="text-muted">Separate tags with commas</small>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Code / Screenshot</label>
            <input type="file" name="image" class="form-control">
          </div>

        </div>

        <!-- Modal Footer -->
        <div class="modal-footer border-0">
          <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">
            Cancel
          </button>
          <button type="submit" class="btn btn-primary rounded-pill px-4">
            Post
          </button>
        </div>
      </form>

    </div>
  </div>
</div>


<!-- Toast -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
  <div id="toast" class="toast">
    <div class="toast-body"></div>
  </div>
</div>

<?php if (isset($_SESSION["toast"])): ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/toast.js"></script>
<script>
showToast("<?= $_SESSION['toast'][1] ?>", "<?= $_SESSION['toast'][0] ?>");
</script>
<?php unset($_SESSION["toast"]); endif; ?>

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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
