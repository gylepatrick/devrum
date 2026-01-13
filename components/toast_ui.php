<!-- Toast Container -->
<div class="toast-container position-fixed bottom-0 end-0 p-3" id="toastContainer" style="z-index: 9999;">
</div>

<?php if (isset($_SESSION["toast"])): ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= strpos($_SERVER['PHP_SELF'], '/auth/') !== false ? '../js/toast.js' : 'js/toast.js' ?>"></script>
<script>
  // Determine toast type
  const toastType = "<?= $_SESSION['toast'][0] ?>";
  const toastMessage = "<?= addslashes($_SESSION['toast'][1]) ?>";
  
  // Map backend types to UI types
  const typeMap = {
    'success': 'success',
    'error': 'error',
    'warning': 'warning',
    'info': 'info'
  };
  
  showToast(toastMessage, typeMap[toastType] || 'info');
</script>
<?php unset($_SESSION["toast"]); endif; ?>
