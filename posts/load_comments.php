<?php
session_start();
require "../db.php";

$postId = $_GET['post_id'];
$offset = $_GET['offset'];

$comments = $conn->query("
  SELECT comments.*, users.username
  FROM comments
  JOIN users ON users.id = comments.user_id
  WHERE comments.post_id = $postId
  ORDER BY comments.created_at ASC
  LIMIT 3 OFFSET $offset
");

while ($c = $comments->fetch_assoc()):
?>
  <div class="border-start ps-2 mb-2 comment-box">
    <strong><?= htmlspecialchars($c['username']) ?></strong>
    <p class="mb-0"><?= htmlspecialchars($c['comment']) ?></p>
    <small class="text-muted"><?= $c['created_at'] ?></small>
  </div>
<?php endwhile; ?>
