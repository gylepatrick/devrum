<?php
  session_start();
  require "../db.php";
  require "../config/posts/load_posts.php";
  require "../config/posts/load_tags.php";
?>
<?php include "../components/posts_ui/header.php"; ?>

<!-- NAVBAR -->
<?php include "../components/navbar.php"; ?>
<div style="height:80px"></div>

<div class="container-fluid col-md-7">
  <div class="row">
    <!-- TAG SIDEBAR -->
    <div class="col-md-4 mb-3">
      <?php include "../components/posts_ui/tags_card.php"; ?>
    </div>
    <!-- POSTS -->
    <div class="col-md-8 mx-auto">
      <?php include "../components/posts_ui/post_cards.php"; ?>
    </div>
  </div>
</div>
<!--modal-->
<?php include "../components/posts_ui/modals/ask_modal.php"; ?>
<!-- FOOTER -->
<?php include "../components/posts_ui/footer.php"; ?>
