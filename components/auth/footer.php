<!-- Toast Container -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
  <div class="toast" id="toast" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-header">
      <strong class="me-auto">DevRum | Notification</strong>
      <small class="text-muted">
        <!-- show how many seconds ago -->
        just now
      </small>
      <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body">
      See? Just like this.
    </div>
  </div>
</div>

<?php if (isset($_SESSION["toast"])): ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/toast.js"></script>
<script>
  showToast("<?= $_SESSION['toast'][1] ?>", "<?= $_SESSION['toast'][0] ?>");
</script>
<?php unset($_SESSION["toast"]); endif; ?>

</body>
</html>