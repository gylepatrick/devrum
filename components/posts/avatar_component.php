<!-- Avatar -->
  <a href="../auth/user_profile.php?user_id=<?= $post['user_id'] ?>">
    <img 
      src="<?= $post['avatar'] ? htmlspecialchars($post['avatar']) : '../uploads/avatar/defult_profile.jpeg' ?>"
      alt="avatar"
      class="rounded-circle border-lg m-3"
      style="width:40px; height:40px; object-fit:cover;"
    >
  </a>