<?php
session_start();
require "../db.php";

$userId = $_SESSION["user_id"];
$title = $_POST["title"];
$content = $_POST["content"];
$tags = explode(",", $_POST["tags"]);

$imagePath = null;
if (!empty($_FILES["image"]["name"])) {
  $imagePath = "../uploads/" . time() . $_FILES["image"]["name"];
  move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath);
}

$conn->query("INSERT INTO posts (user_id, title, content, image)
              VALUES ($userId, '$title', '$content', '$imagePath')");

$postId = $conn->insert_id;

// TAG HANDLING
foreach ($tags as $tag) {
  $tag = trim($tag);
  if (!$tag) continue;

  $conn->query("INSERT IGNORE INTO tags (name) VALUES ('$tag')");
  $tagId = $conn->query("SELECT id FROM tags WHERE name='$tag'")->fetch_assoc()["id"];

  $conn->query("INSERT INTO post_tags VALUES ($postId, $tagId)");
}

header("Location: index.php");
