<?php
session_start();
require "../db.php";

$postId = $_GET["id"];
$userId = $_SESSION["user_id"];

// fetch post
$post = $conn->query("
  SELECT * FROM posts 
  WHERE id=$postId AND user_id=$userId
")->fetch_assoc();

if (!$post) {
  die("Unauthorized access");
}

// fetch tags
$tagsRes = $conn->query("
  SELECT t.name FROM tags t
  JOIN post_tags pt ON pt.tag_id = t.id
  WHERE pt.post_id = $postId
");

$tags = [];
while ($t = $tagsRes->fetch_assoc()) {
  $tags[] = $t["name"];
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Edit Post</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../css/style.css">
</head>
<body class="bg-light">

<div class="container mt-5">
  <div class="card p-4">
    <h4>Edit Post</h4>

    <form action="update.php" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="id" value="<?= $postId ?>">

      <input name="title" class="form-control mb-2"
        value="<?= htmlspecialchars($post['title']) ?>" required>

      <textarea name="content" class="form-control mb-2"
        rows="4"><?= htmlspecialchars($post['content']) ?></textarea>

      <input name="tags" class="form-control mb-2"
        value="<?= implode(',', $tags) ?>">

      <?php if ($post["image"]): ?>
        <img src="<?= $post["image"] ?>" class="img-fluid mb-2">
      <?php endif; ?>

      <input type="file" name="image" class="form-control mb-3">

      <button class="btn btn-primary align-right">Update</button>
      <a href="index.php" class="btn btn-secondary">Cancel</a>
    </form>
  </div>
</div>

</body>
</html>
