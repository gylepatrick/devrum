<?php
session_start();
require "../db.php";

if (!isset($_SESSION["user_id"])) {
  die("Unauthorized");
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  header("Location: index.php");
  exit;
}

$postId = (int) ($_POST["post_id"] ?? 0);
$userId = (int) $_SESSION["user_id"];
$comment = trim($_POST["comment"] ?? "");

if ($postId > 0 && $comment !== "") {
  $stmt = $conn->prepare(
    "INSERT INTO comments (post_id, user_id, comment) VALUES (?, ?, ?)"
  );
  $stmt->bind_param("iis", $postId, $userId, $comment);
  $stmt->execute();
  $stmt->close();
}

/**
 * Redirect back to the previous page and jump to the comment section
 */
$ref = $_SERVER["HTTP_REFERER"] ?? "index.php";
$parts = parse_url($ref);

$path  = $parts["path"] ?? "index.php";
$query = isset($parts["query"]) ? "?" . $parts["query"] : "";

header("Location: {$path}{$query}#comment-section-{$postId}");
exit;
