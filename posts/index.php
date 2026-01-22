<?php
session_start();
require "../db.php";

$tag = $_GET["tag"] ?? null;

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

$query .= " ORDER BY posts.created_at DESC";
$posts = $conn->query($query);
$tags  = $conn->query("SELECT * FROM tags");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>DevRum | Developer Forum</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
:root {
  --brand: #22d3ee;
  --bg-main: #020617;
  --bg-soft: #020617;
  --card-bg: rgba(255,255,255,0.06);
  --card-border: rgba(255,255,255,0.12);
  --text-main: #e5e7eb;
  --text-muted: #9ca3af;
}

/* PAGE BACKGROUND */
body {
  background:
    radial-gradient(60% 40% at 50% 0%, rgba(34,211,238,0.08), transparent 60%),
    #020617;
  color: var(--text-main);
}

/* NAVBAR */
.navbar {
  background: rgba(2,6,23,.85) !important;
  backdrop-filter: blur(14px);
  border-bottom: 1px solid var(--card-border);
}

/* MAIN POST CARD */
.card {
  background: var(--card-bg);
  border: 1px solid var(--card-border);
  border-radius: 20px;
  box-shadow:
    0 0 0 1px rgba(255,255,255,0.02),
    0 20px 40px rgba(0,0,0,0.45);
}

.card:hover {
  transform: translateY(-2px);
  transition: 0.2s ease;
}

/* POST CONTENT */
.card h5 {
  color: #f9fafb;
}

.card p {
  color: var(--text-muted);
  font-size: 14.5px;
}

/* POST IMAGE */
.card img {
  background: #020617;
  padding: 12px;
  border-radius: 14px;
  border: 1px solid var(--card-border);
}

/* TAGS */
.badge-tag {
  background: rgba(34,211,238,0.12);
  color: var(--brand);
  border: 1px solid rgba(34,211,238,0.35);
}

/* SIDEBAR */
.sidebar {
  background: rgba(255,255,255,0.04);
  border: 1px solid var(--card-border);
  border-radius: 18px;
}

/* LIKE BUTTON */
.btn-outline-primary {
  border-color: var(--brand);
  color: var(--brand);
}

.btn-primary {
  background: var(--brand);
  color: #020617;
}

/* FOOTER */
footer {
  background: linear-gradient(to top, rgba(0,0,0,.6), transparent);
}


.post-user {
  color: #f9fafb;
  font-weight: 600;
}



</style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar fixed-top">
  <div class="container-fluid px-4">
    <a class="navbar-brand fw-bold text-light" href="index.php">
      DEV<span style="color:var(--brand)">Rum</span>
    </a>

    <button class="btn btn-primary btn-sm rounded-pill px-3"
      data-bs-toggle="modal"
      data-bs-target="#askModal">
      + Ask
    </button>

    <div class="ms-auto d-flex align-items-center gap-3">
      <a href="../auth/profile.php" class="text-decoration-none d-flex align-items-center gap-2 text-light">
        <img src="<?= $_SESSION['image'] ?? '../uploads/avatars/defult_profile.jpeg' ?>"
          class="rounded-circle" style="width:34px;height:34px;object-fit:cover;">
        <span class="small fw-semibold text-white"><?= $_SESSION['user'] ?></span>
      </a>
      <a href="../auth/logout.php" class="btn btn-soft btn-sm rounded-pill btn-danger text-white">Logout</a>
    </div>
  </div>
</nav>

<div style="height:90px"></div>

<div class="container-fluid">
<div class="row">

<!-- TAG SIDEBAR -->
<div class="col-md-2">
  <div class="sidebar p-3">
    <h6 class="fw-bold mb-3">Trending Tags</h6>
    <div class="d-flex flex-wrap gap-2">
      <?php while($t=$tags->fetch_assoc()): ?>
        <a href="?tag=<?= $t['name'] ?>" class="badge-tag">#<?= $t['name'] ?></a>
      <?php endwhile; ?>
    </div>
  </div>
</div>

<!-- POSTS -->
<div class="col-md-7 mx-auto">

<?php while($post=$posts->fetch_assoc()): ?>

<?php
$likeCount = $conn->query(
  "SELECT COUNT(*) total FROM likes WHERE post_id={$post['id']}"
)->fetch_assoc()['total'];

$userLiked = $conn->query(
  "SELECT id FROM likes WHERE post_id={$post['id']} AND user_id={$_SESSION['user_id']}"
)->num_rows > 0;

$tg = $conn->query("
  SELECT t.name FROM tags t
  JOIN post_tags pt ON pt.tag_id=t.id
  WHERE pt.post_id={$post['id']}
");
?>

<div class="card mb-4">
  <div class="card-body">

    <div class="d-flex align-items-center gap-2 mb-3">
      <img src="<?= $post['avatar'] ?>" class="rounded-circle"
        style="width:36px;height:36px;object-fit:cover; border:2px solid var(--brand);">
      <strong style="color:var(--brand);"><?= $post['username'] ?></strong>
      <span class=" small ms-auto" style="font-size:13px; color:var(--brand);">
        <?= date("M d, Y", strtotime($post['created_at'])) ?>
      </span>
    </div>

    <h5 class="fw-semibold"><?= htmlspecialchars($post['title']) ?></h5>
    <p class="text-muted"><?= nl2br(htmlspecialchars($post['content'])) ?></p>

    <?php if($post['image']): ?>
      <img src="<?= $post['image'] ?>" class="w-100 mb-3"
        style="max-height:420px;object-fit:contain;border-radius:12px;">
    <?php endif; ?>

    <div class="mb-3 d-flex flex-wrap gap-2">
      <?php while($tr=$tg->fetch_assoc()): ?>
        <span class="badge-tag">#<?= $tr['name'] ?></span>
      <?php endwhile; ?>
    </div>

    <div class="d-flex justify-content-between align-items-center">
      <a href="like.php?post_id=<?= $post['id'] ?>"
         class="btn btn-sm <?= $userLiked?'btn-primary':'btn-outline-primary' ?>"
         data-post-id="<?= $post['id'] ?>">
        👍 <span class="like-count"><?= $likeCount ?></span>
      </a>

      <?php if($_SESSION['user_id']==$post['user_id']): ?>
        <div class="d-flex gap-2">
          <!-- 3 dots -->
          <button data-bs-toggle="dropdown" class="btn btn-soft btn-sm text-white">⋮</button>
          <div class="dropdown-menu border-0 shadow-sm text-small">
            <a href="edit.php?id=<?= $post['id'] ?>" class="dropdown-item">Edit</a>
            <form method="POST" action="delete.php"  class="d-inline">
              <input type="hidden" name="id" value="<?= $post['id'] ?>">
              <button class="dropdown-item">Delete</button>
            </form>
          </div>
        </div>
      <?php endif; ?>
    </div>

  </div>
</div>

<?php endwhile; ?>

</div>
</div>
</div>

<!-- ASK MODAL -->
<div class="modal fade" id="askModal">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <form class="modal-content p-4" action="store.php" method="POST" enctype="multipart/form-data"
      style="background:var(--card);border-radius:20px;">
      <h5 class="fw-bold mb-3">Ask DevRum</h5>

      <input name="title" class="form-control mb-3" placeholder="Question title" required>
      <textarea name="content" class="form-control mb-3" rows="4"
        placeholder="Explain your issue..." required></textarea>

      <input name="tags" class="form-control mb-3" placeholder="php, js, mysql">

      <div class="drop-zone" id="dropZone">Drag & drop image or click</div>
      <input type="file" name="image" id="imageInput" class="d-none" accept="image/*">

      <div class="text-end mt-3">
        <button class="btn btn-primary rounded-pill px-4">Post</button>
      </div>
    </form>
  </div>
</div>

<footer class="text-center py-4 mt-5">
  © <?= date('Y') ?> DevRum · Built for devs 🧑‍💻
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelectorAll('[data-post-id]').forEach(btn=>{
  btn.onclick=e=>{
    e.preventDefault();
    fetch(btn.href).then(()=> {
      btn.classList.toggle('btn-primary');
      btn.classList.toggle('btn-outline-primary');
      fetch(`like_count.php?post_id=${btn.dataset.postId}`)
        .then(r=>r.text())
        .then(c=>btn.querySelector('.like-count').innerText=c);
    });
  }
});

const dz=document.getElementById('dropZone');
const input=document.getElementById('imageInput');
dz.onclick=()=>input.click();
dz.ondrop=e=>{
  e.preventDefault();
  input.files=e.dataTransfer.files;
};
dz.ondragover=e=>dz.classList.add('dragover');
dz.ondragleave=e=>dz.classList.remove('dragover');
</script>

</body>
</html>
