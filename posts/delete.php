<?php
session_start();
require "../db.php";

$postId = $_GET["id"];
$userId = $_SESSION["user_id"];

// verify ownership
$check = $conn->query("
  SELECT * FROM posts WHERE id=$postId AND user_id=$userId
");

if ($check->num_rows === 0) {
  die("Unauthorized");
}

// delete relations first
$conn->query("DELETE FROM post_tags WHERE post_id=$postId");
$conn->query("DELETE FROM posts WHERE id=$postId");

header("Location: index.php");
