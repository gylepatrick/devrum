<?php
session_start();
require "../db.php";

if (!isset($_SESSION["user_id"])) {
  die("Unauthorized");
}

$postId = $_GET["post_id"];
$userId = $_SESSION["user_id"];

// check if already liked
$check = $conn->query(
  "SELECT id FROM likes WHERE post_id=$postId AND user_id=$userId"
);

if ($check->num_rows > 0) {
  // unlike
  $conn->query(
    "DELETE FROM likes WHERE post_id=$postId AND user_id=$userId"
  );
} else {
  // like
  $conn->query(
    "INSERT INTO likes (post_id, user_id) VALUES ($postId, $userId)"
  );
}

header("Location: index.php");
