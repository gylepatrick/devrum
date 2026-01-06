<?php
session_start();
require "../db.php";

if (!isset($_SESSION["user_id"])) {
  die("Unauthorized");
}

$postId = $_POST["post_id"];
$userId = $_SESSION["user_id"];
$comment = trim($_POST["comment"]);

if ($comment) {
  $stmt = $conn->prepare(
    "INSERT INTO comments (post_id, user_id, comment) VALUES (?, ?, ?)"
  );
  $stmt->bind_param("iis", $postId, $userId, $comment);
  $stmt->execute();
}

header("Location: index.php");
