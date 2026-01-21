<?php
session_start();
require "../db.php";

// Get tag filter if exists
$tag = $_GET["tag"] ?? null;


$query = "
SELECT posts.*, users.username, users.avatar
FROM posts
JOIN users ON users.id = posts.user_id
ORDER BY posts.created_at DESC
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
  <style>
    .drop-zone {
      border: 2px dashed #dee2e6;
      border-radius: 8px;
      padding: 40px 20px;
      text-align: center;
      transition: all 0.3s ease;
      background-color: #f8f9fa;
      cursor: pointer;
    }
    .drop-zone.dragover {
      border-color: #0d6efd;
      background-color: #e7f3ff;
    }
    .drop-zone-content {
      pointer-events: none;
    }
  </style>
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
    <div class="col-md-2 mb-3">
      <div class="card p-3 sidebar">
        <h6 class="fw-bold mb-3">Recent Tags</h6>
        <div class="d-flex flex-wrap gap-2">
          
          <?php while ($t = $tags->fetch_assoc()): ?>
            <a href="?tag=<?= $t['name'] ?>" class="badge-tag">#<?= $t['name'] ?></a>
          <?php endwhile; ?>

          <?php if ($tags->num_rows === 0): ?>
            <div class="text-muted small">No tags found.</div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  
    <!-- POSTS -->
    <div class="col-md-6 mx-auto">
      <?php while ($post = $posts->fetch_assoc()): ?>
        <div class="card mb-4">
          <div class="d-flex align-items-center gap-2 mb-2">

  <?php include "../components/posts/avatar_component.php"; ?>

  <?php include "../components/posts/readmore_component.php"; ?>a

</div>
  


            <?php if ($post["image"]): ?>
            <div class="zoom-container">
              <a href="<?= $post['image'] ?>" target="_blank"><img src="<?= $post['image'] ?>" class="card-img-top w-100 zoomable" alt="Post Image" style="height:400px; object-fit:contain; background-color:#f8f9fa;"></a> 
            </div>
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

              <?php if( $tg->num_rows === 0 ): ?>
                <span class="text-muted small">No tags</span>
              <?php endif; ?>
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

      <?php if ($posts->num_rows === 0): ?>
        <p class="text-center text-muted">No posts found. Please reload the page.</p>
      <?php endif; ?>
      
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
        <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">

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

          <img
              id="preview-img"
              src="#"
              alt="Image Preview"
              class="mb-3 border-lg"
              style="width:100%; max-height:400px; object-fit:contain; display:none;"
            />

          <div class="mb-3">
            <label class="form-label fw-semibold">Code / Screenshot</label>
            <div class="drop-zone" id="dropZone">
              <div class="drop-zone-content">
                <i class="bi bi-cloud-upload fs-1 text-muted mb-2"></i>
                <p class="mb-1">Drag & drop your image here</p>
                <p class="text-muted small">or <span class="text-primary fw-semibold" style="cursor: pointer;" onclick="document.getElementById('imageInput').click()">browse files</span></p>
                <small class="text-muted">Supported: JPG, PNG, GIF (Max 5MB)</small>
              </div>
              <input 
                type="file" 
                name="image" 
                class="form-control d-none" 
                accept="image/*"
                id="imageInput">
            </div>
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

<footer class="bg-light mt-5 py-3 border-top"> 
  <div class="text-center p-3 text-muted small">
    &copy; <?= date("Y") ?> DevRum. All rights reserved.
    <br> Made with ❤️ for developers by <a href="https://github.com/gylepatrick" class="text-decoration-none">dev_gpl</a>.
  </div>

</footer>

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


<script>
      const dropZone = document.getElementById('dropZone');
      const imageInput = document.getElementById('imageInput');
      const previewImg = document.getElementById('preview-img');

      // Prevent default drag behaviors
      ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
        document.body.addEventListener(eventName, preventDefaults, false);
      });

      // Highlight drop zone when dragging over it
      ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, highlight, false);
      });

      ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, unhighlight, false);
      });

      // Handle drop
      dropZone.addEventListener('drop', handleDrop, false);

      // Click to open file dialog
      dropZone.addEventListener('click', () => {
        imageInput.click();
      });

      function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
      }

      function highlight(e) {
        dropZone.classList.add('dragover');
      }

      function unhighlight(e) {
        dropZone.classList.remove('dragover');
      }

      function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;

        if (files.length > 0) {
          imageInput.files = files;
          // Trigger change event to show preview
          imageInput.dispatchEvent(new Event('change'));
        }
      }

      // Existing image preview script
      imageInput.addEventListener("change", function (event) {
        const file = event.target.files[0];
        if (file) {
          const reader = new FileReader();
          reader.onload = function (e) {
            previewImg.src = e.target.result;
            previewImg.style.display = "block";
          };
          reader.readAsDataURL(file);
        } else {
          previewImg.src = "#";
          previewImg.style.display = "none";
        }
      });
    </script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>



</body>
</html>
