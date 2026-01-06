<?php
session_start();
require "../db.php";

$postId = $_POST["id"];
$userId = $_SESSION["user_id"];

$title = $_POST["title"];
$content = $_POST["content"];
$tags = explode(",", $_POST["tags"]);

// verify ownership
$check = $conn->query("
  SELECT * FROM posts WHERE id=$postId AND user_id=$userId
");

if ($check->num_rows === 0) {
  die("Unauthorized");
}

// image handling
$imageSql = "";
if (!empty($_FILES["image"]["name"])) {
  $imagePath = "../uploads/" . time() . $_FILES["image"]["name"];
  move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath);
  $imageSql = ", image='$imagePath'";
}

// update post
$conn->query("
  UPDATE posts 
  SET title='$title', content='$content' $imageSql
  WHERE id=$postId
");

// reset tags
$conn->query("DELETE FROM post_tags WHERE post_id=$postId");

foreach ($tags as $tag) {
  $tag = trim($tag);
  if (!$tag) continue;

  $conn->query("INSERT IGNORE INTO tags (name) VALUES ('$tag')");
  $tagId = $conn->query("
    SELECT id FROM tags WHERE name='$tag'
  ")->fetch_assoc()["id"];

  $conn->query("INSERT INTO post_tags VALUES ($postId, $tagId)");
}

header("Location: index.php");
