<!-- Username + Read more -->
  <div>
    <a 
      href="../auth/user_profile.php?user_id=<?= $post['user_id'] ?>"
      class="text-decoration-none fw-bold text-dark p-2"
      style="font-size: 15px;"
    >
      <?= htmlspecialchars($post['username']) ?>
    </a>

    <div>
      <a 
        href="view_post.php?post_id=<?= $post['id'] ?>"
        class="text-primary small text-decoration-none"
      >
        Read more →
      </a>
    </div>
  </div>