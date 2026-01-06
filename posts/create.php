<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
  <title>Create Post</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../css/style.css">
</head>
<body class="bg-light">
<section class="ui-section">

  <nav class="bg-white p-3 rounded-4 m-3">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="../posts/index.php">Home</a></li>
      <li class="breadcrumb-item active">Ask</li>
    </ol>
  </nav>
</section>
<div class="container mt-5">
  <div class="card p-4">
    <h5 class="fw-bold mb-3">Ask a Dev Question</h5>

    <form action="store.php" method="POST" enctype="multipart/form-data">
      <input name="title" class="form-control mb-3" placeholder="Title" required>
      <textarea name="content" class="form-control mb-3" rows="5" placeholder="Explain your issue"></textarea>
      <input name="tags" class="form-control mb-3" placeholder="php, js, mysql">
      <input type="file" name="image" class="form-control mb-4">
      <button class="btn btn-sm btn-primary w-30 d-flex justify-center align-center">Post</button>
    </form>
  </div>
</div>

</body>
</html>
